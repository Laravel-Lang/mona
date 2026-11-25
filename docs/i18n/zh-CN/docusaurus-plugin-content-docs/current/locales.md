---
title: 语言区域和名称
description: 查找受支持及已安装的语言区域，解析别名并查询本地化后的名称。
---

Laravel-Lang 在 Laravel、翻译文件名和模型翻译记录中使用 `en`、`fr` 和 `pt_BR` 等规范代码。语言区域别名是应用对外使用的名称，例如 URL 中的 `pt-br`。请在 [`localization_main.aliases`](./configure.md) 中定义从规范代码到别名的映射。

## 查找受支持及已安装的语言区域 {#find-supported-and-installed-locales}

`laravel-lang/locale-list` 提供有标量值的枚举 `LaravelLang\LocaleList\Locale`。其中的枚举项与[受支持的规范代码](./available-locales.md)一一对应。

```php
use LaravelLang\LocaleList\Locale;

Locale::French->value;        // 'fr'
Locale::tryFrom('pt_BR');     // Locale::PortugueseBrazil
Locale::values();            // 规范代码数组
Locale::collection();        // 规范代码集合
Locale::collection(false);   // 枚举项集合
```

`laravel-lang/locales` 提供能够读取应用状态的 `LaravelLang\Locales\Facades\Locales` 门面。以下方法返回字符串集合。传入 `false` 可获取规范代码。默认情况下，若某个代码配置了别名，则以别名表示。

| 方法 | 返回值 |
| --- | --- |
| `Locales::available()` | 所有已知的语言区域 |
| `Locales::installed()` | 从应用翻译文件识别出的语言区域，以及受保护的语言区域 |
| `Locales::uninstalled()` | 可用但未安装的语言区域 |
| `Locales::protects()` | 去重后的当前和回退语言区域 |

自动发现过程会读取 `lang_path()` 的直接子项，以及相对于该路径匹配 `vendor/*/*.json` 的 JSON 文件。文件名或目录名必须能解析为受支持的代码。`lang/vendor/<package>/` 下仅包含 PHP 文件的翻译目录不在这些匹配范围内。如何发布语言区域的译文，请参阅[管理译文](./translations.md)。

## 读取或设置应用的语言区域 {#read-or-set-the-application-locale}

```php
use LaravelLang\Locales\Facades\Locales;

if (Locales::isInstalled('fr')) {
    Locales::set('fr');
}

$canonicalCode = Locales::current(false);
$displayCode = Locales::current();
```

`set()` 接受代码、别名、`Locale` 枚举项或 `LocaleData` 对象。若该语言区域已安装，它会将 Laravel 的语言区域设置为相应的规范代码。用 `isAvailable()` 检查代码是否受支持，用 `isInstalled()` 检查是否已有翻译文件或受到保护。`isProtected()` 用于确认是否属于当前或回退语言区域。

`current()` 优先使用 Laravel 当前的语言区域，无法识别时使用 `fallback()`。`fallback()` 解析 Laravel 配置的回退语言区域，无法识别时返回 `en`。这两个方法都可传入 `false` 获取规范代码。

用 `fromAlias()` 将别名解析为规范代码，用 `toAlias()` 查询规范代码对应的别名。转换时，无法识别的非空字符串会原样返回。验证输入时请使用 `isAvailable()`。

## 显示语言区域选项 {#display-locale-choices}

`Locales::info('fr')` 返回 `LaravelLang\LocaleNames\LocaleData` 对象，包含 `code`、`alias`、`native` 和 `localized` 属性。显示语言是应用当前的语言区域。不传参数则查询当前语言区域。已知的语言区域在安装前也可查询。未知代码会引发 `LaravelLang\Exceptions\UnknownLocaleException`。

使用 `Locales::data()->installed()` 可将已安装的语言区域作为 `LocaleData` 对象获取。`data()` 还提供 `available()`、`uninstalled()` 和 `protects()` 来获取其他集合，以及 `current()` 和 `fallback()` 来获取单个对象。语言选择器可以将 `alias` 用作提交值，将 `native` 名称用作显示标签。

## 查找语言区域、国家或地区以及货币名称 {#look-up-locale-country-and-currency-names}

`laravel-lang/locale-names`、`laravel-lang/country-names` 和 `laravel-lang/currency-names` 各提供一个可从 Laravel 容器注入的服务。Common 包含这三个包。

```php
use LaravelLang\CountryNames\CountryNames;
use LaravelLang\CurrencyNames\CurrencyNames;
use LaravelLang\LocaleNames\LocaleNames;

$locale = app(LocaleNames::class)->one('de', 'fr');
$locale->localized; // 'Französisch'

$country = app(CountryNames::class)->one('de', 'fr');
$country->code;      // 'FR'
$country->localized; // 'Frankreich'

$currency = app(CurrencyNames::class)->one('de', 'fr');
$currency->code;    // 'EUR'
$currency->numeric; // 978

$nativeLocaleNames = app(LocaleNames::class)->all(null, \SortDirection::Ascending);
```

第一个参数表示显示语言。传入 `null` 可使用本地名称。`one()` 的第二个参数表示该条目的语言区域，例如上例的 `fr`。查询国家或地区及货币时，返回对象包含 ISO 代码。这两个参数都接受规范代码、已配置的别名和 `Locale` 枚举项。

`all($forLocale)` 返回以规范语言区域代码为键的集合。传入 `SortDirection::Ascending` 或 `SortDirection::Descending` 可按 `localized` 属性排序。各值均为只读对象。国家或地区及货币的对象还包含条目的 `locale`。
