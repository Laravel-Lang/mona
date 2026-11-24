---
title: 本地化路由
description: 注册本地化 URL，并根据请求选择 Laravel 的语言区域。
---

`laravel-lang/routes` 在处理请求时选择语言区域，并为本地化路由组生成 URL。Common 已包含此包。

```bash
composer require laravel-lang/routes
```

确认 [URL 中使用的语言区域已安装](./translations.md)。在 [`config/localization_routes.php`](./configure.md) 中配置参数名、中间件和默认语言区域的行为。

## 注册本地化路由组 {#register-a-localized-route-group}

在 `routes/web.php` 中使用 `Route::localizedGroup()` 宏。门面 `LaravelLang\Routes\Facades\LocalizationRoute::group()` 提供相同的分组方法。

```php
use Illuminate\Support\Facades\Route;

Route::localizedGroup(function (): void {
    Route::get('/products/{product}', fn (string $product) => view('products.show', [
        'product' => $product,
    ]))->name('products.show');
});
```

在默认配置下，回调会注册以下两种 URL 模式。

| URL 模式 | 路由名称 |
| --- | --- |
| `/products/{product}` | `products.show` |
| `/{locale}/products/{product}` | `localized.products.show` |

语言区域中间件会在调用路由处理程序前取走 `locale` 参数。如果 URL 指向未知或未安装的语言区域，并且路由有名称，则请求会重定向到无前缀的路由。

## 生成本地化 URL {#generate-a-localized-url}

```php
use function LaravelLang\Routes\localizedRoute;

$url = localizedRoute('products.show', [
    'product' => 42,
    'locale' => 'fr',
], absolute: false);
// /fr/products/42
```

辅助函数使用已配置的语言区域参数名。如果有带前缀的路由，会优先使用它。传入的语言区域字符串会原样交给 URL 生成器。要使用别名生成 URL，请传入别名，或先用 [`Locales::toAlias()`](./locales.md) 解析。省略语言区域时，辅助函数使用 `Locales::fallback()`。要使用当前语言区域，请显式传入 `Locales::current()`。

`localization_routes.hide` 会在辅助函数生成的 URL 中省略回退语言区域。`localization_routes.redirect` 会将当前语言区域的请求从带前缀的路由组重定向到无前缀的路由。两项设置默认都是 `false`。

## 使用中间件选择语言区域 {#select-a-locale-with-middleware}

可以通过 `LaravelLang\Routes\Middlewares` 下的类直接应用各个中间件。

| 中间件 | 来源 |
| --- | --- |
| `LocalizationByParameterMiddleware` | 路由参数，默认为 `locale` |
| `LocalizationByParameterWithRedirectMiddleware` | 路由参数。对于已命名且声明了参数的路由，若请求未指定语言区域或其指定值未安装，则使用当前语言区域 |
| `LocalizationByCookieMiddleware` | 默认读取名为 `Accept-Language` 的 Cookie |
| `LocalizationByHeaderMiddleware` | 按 `Accept-Language` 请求头中的偏好顺序选择第一个已安装的语言区域设置。先匹配完整语言标签，再匹配基础语言，跳过通配符和权重为 0 的条目。 |
| `LocalizationBySessionMiddleware` | 默认读取名为 `Accept-Language` 的会话键 |
| `LocalizationByModelMiddleware` | 默认读取已认证 Eloquent 用户的 `locale` 属性。在中间件类名后添加 `:guard_name`，即可选择身份验证守卫 |

对于带前缀的路由组，默认顺序为 URL 参数、Cookie、请求头、会话，最后是模型。无前缀的路由组顺序相同，但跳过 URL 步骤。后续的有效来源可覆盖前面取得的值。更改语言区域会更新 Laravel，并将规范代码保存在配置的会话键中。如需使用会话和 Cookie，请使用 `web` 中间件组。请求头中间件需要 PHP Intl 扩展。

中间件更改语言区域时会派发 `LaravelLang\Routes\Events\LocaleHasBeenSetEvent`。其 `locale` 属性是 `LaravelLang\LocaleNames\LocaleData` 对象。通过 `$event->locale->code` 读取规范代码。
