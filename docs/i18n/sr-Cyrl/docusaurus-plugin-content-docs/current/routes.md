---
title: Локализоване руте
description: Региструјте локализоване URL-ове и изаберите локал Laravel-а из захтева.
---

`laravel-lang/routes` бира локал при обради захтева и генерише URL-ове за групе локализованих рута. Пакет је укључен у Common.

```bash
composer require laravel-lang/routes
```

Уверите се да су [локали које користите у URL-овима инсталирани](./translations.md). Називе параметара, мидлвер и понашање подразумеваног локала подесите у [`config/localization_routes.php`](./configure.md).

## Регистровање групе локализованих рута {#register-a-localized-route-group}

У `routes/web.php` користите макро `Route::localizedGroup()`. Фасада `LaravelLang\Routes\Facades\LocalizationRoute::group()` нуди исти начин груписања.

```php
use Illuminate\Support\Facades\Route;

Route::localizedGroup(function (): void {
    Route::get('/products/{product}', fn (string $product) => view('products.show', [
        'product' => $product,
    ]))->name('products.show');
});
```

Са подразумеваним подешавањима повратна функција региструје оба URL обрасца.

| Образац | Назив руте |
| --- | --- |
| `/products/{product}` | `products.show` |
| `/{locale}/products/{product}` | `localized.products.show` |

Мидлвер за локал преузима параметар `locale` пре позива руковаоца руте. Захтев за непознат или неинсталиран локал у URL-у преусмерава се на руту без префикса ако рута има назив.

Laravel извршава функцију групе два пута. Ако спољна група има префикс URL-а `admin` и префикс имена `admin.`, локализована рута се зове `admin.localized.products.show`.

## Генерисање локализованог URL-а {#generate-a-localized-url}

```php
use function LaravelLang\Routes\localizedRoute;

$url = localizedRoute('products.show', [
    'product' => 42,
    'locale' => 'fr',
], absolute: false);
// /fr/products/42
```

Помоћна функција користи подешени назив параметра локала и даје предност рути са префиксом ако постоји. Прослеђену ниску локала преноси у генератор URL-а. Да добијете URL са алијасом, проследите алијас или га претходно разрешите помоћу [`Locales::toAlias()`](./locales.md). Ако изоставите локал, функција користи `Locales::fallback()`. За текући локал изричито проследите `Locales::current()`.

`localization_routes.hide` изоставља резервни локал из URL-ова које генерише помоћна функција. `localization_routes.redirect` преусмерава захтеве за текући локал из групе са префиксом на руту без префикса. Оба подешавања подразумевано имају вредност `false`.

Проследите `Locale::French->value` за канонски код или објекат `LocaleData` за његов алијас. Проверите корисников избор помоћу `Locales::isInstalled()` пре прављења URL-а.

## Избор локала помоћу мидлвера {#select-a-locale-with-middleware}

Сваки мидлвер можете применити непосредно преко његове класе из `LaravelLang\Routes\Middlewares`.

| Мидлвер | Извор |
| --- | --- |
| `LocalizationByParameterMiddleware` | Параметар руте, подразумевано `locale` |
| `LocalizationByParameterWithRedirectMiddleware` | Параметар руте. Ако рута има назив и параметар је декларисан, користи текући локал када тражени локал недостаје или није инсталиран |
| `LocalizationByCookieMiddleware` | Подразумевано колачић `Accept-Language` |
| `LocalizationByHeaderMiddleware` | Први инсталирани локал по редоследу преференција из заглавља `Accept-Language`. Прво проверава целу језичку ознаку, а затим основни језик. Прескаче џокер знакове и ставке чија је вредност квалитета 0. |
| `LocalizationBySessionMiddleware` | Подразумевано кључ сесије `Accept-Language` |
| `LocalizationByModelMiddleware` | Подразумевано атрибут `locale` пријављеног Eloquent корисника. Додајте `:guard_name` називу класе да изаберете механизам аутентификације |

Подразумевани редослед за групу са префиксом јесте URL параметар, колачић, заглавље, сесија, па модел. Група без префикса користи исти редослед без провере URL-а. Каснији важећи извор може да замени ранији. Промена локала ажурира Laravel и чува канонски код у подешеном кључу сесије. Када су вам потребни сесије и колачићи, користите групу мидлвера `web`. Мидлвер за заглавље захтева PHP Intl.

Када мидлвер промени локал, шаље догађај `LaravelLang\Routes\Events\LocaleHasBeenSetEvent`. Његово својство `locale` је објекат `LaravelLang\LocaleNames\LocaleData`. За канонски код користите `$event->locale->code`.

Да би URL одређивао локал, оставите само `LocalizationByParameterPrefixMiddlewareMiddleware::class` у `localization_routes.middlewares.prefix`. Ланац рута без префикса подесите засебно.
