---
title: Locales and names
description: Find supported and installed locales, resolve aliases, and look up localized names.
---

Laravel-Lang uses canonical codes such as `en`, `fr`, and `pt_BR` in Laravel, translation filenames, and model translation rows. A locale alias is an application-facing name, such as `pt-br` in a URL. Define aliases as canonical code to alias in [`localization_main.aliases`](./configure.md).

## Find supported and installed locales

`laravel-lang/locale-list` provides the `LaravelLang\LocaleList\Locale` backed enum. Its cases correspond to the [supported canonical codes](./available-locales.md).

```php
use LaravelLang\LocaleList\Locale;

Locale::French->value;        // 'fr'
Locale::tryFrom('pt_BR');     // Locale::PortugueseBrazil
Locale::values();            // array of canonical codes
Locale::collection();        // Collection of canonical codes
Locale::collection(false);   // Collection of enum cases
```

`laravel-lang/locales` adds the application-aware `LaravelLang\Locales\Facades\Locales` facade. These methods return collections of strings. Pass `false` as the argument to get canonical codes. By default, each code is represented by its configured alias when available.

| Method | Returns |
| --- | --- |
| `Locales::available()` | All known locales |
| `Locales::installed()` | Locales recognized from app language files, plus protected locales |
| `Locales::uninstalled()` | Available locales outside the installed set |
| `Locales::protects()` | The effective current and fallback locales with duplicates removed |

Discovery reads the immediate entries in `lang_path()` and JSON files matching `vendor/*/*.json` relative to that path. The filename or directory name must resolve to a supported code. PHP-only translation directories below `lang/vendor/<package>/` are outside these discovery patterns. See [managing translations](./translations.md) for how to publish a locale.

## Read or set the application locale

```php
use LaravelLang\Locales\Facades\Locales;

if (Locales::isInstalled('fr')) {
    Locales::set('fr');
}

$canonicalCode = Locales::current(false);
$displayCode = Locales::current();
```

`set()` accepts a code, alias, `Locale` enum case, or `LocaleData` object. It updates Laravel's locale to the canonical code when the locale is installed. Use `isAvailable()` for supported-code checks and `isInstalled()` when files or protection are required. `isProtected()` identifies the current and fallback locales.

`current()` uses Laravel's current locale if recognized, then `fallback()`. `fallback()` resolves Laravel's configured fallback and defaults to `en` if it is unknown. Both methods accept the same `false` argument for canonical codes.

Use `fromAlias()` to resolve an alias to a canonical code and `toAlias()` to get the alias for a canonical code. These conversions preserve unrecognized nonempty strings. Use `isAvailable()` when validating input.

## Display locale choices

`Locales::info('fr')` returns a `LaravelLang\LocaleNames\LocaleData` object with `code`, `alias`, `native`, and `localized` properties. The display language is the current application locale. Omit the argument for the current locale. A known locale can be looked up before it is installed. An unknown code raises `LaravelLang\Exceptions\UnknownLocaleException`.

Use `Locales::data()->installed()` to get the installed locales as `LocaleData` objects. `data()` also provides `available()`, `uninstalled()`, and `protects()` for other collections, plus `current()` and `fallback()` for individual objects. A language selector can use the `alias` as the submitted value and the `native` name as the label.

## Look up locale, country, and currency names

The `laravel-lang/locale-names`, `laravel-lang/country-names`, and `laravel-lang/currency-names` packages each provide a service you can inject from Laravel's container. Common includes all three.

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

The first parameter is the display language. Use `null` for native names. The second parameter of `one()` is the locale for the row, such as `fr` in the examples above. For country and currency lookups, the ISO code is on the returned object. Both parameters accept canonical codes, configured aliases, and `Locale` enum cases.

`all($forLocale)` returns a collection keyed by canonical locale code. Pass `SortDirection::Ascending` or `SortDirection::Descending` to sort by the `localized` property. Each value is a readonly object. Country and currency values also include the row's `locale`.
