---
title: Upgrade to Mona
description: Upgrade Laravel-Lang dependencies, configuration, published translations, and application integrations.
---

Mona is the repository that releases the Laravel-Lang packages together. Keep using the individual Composer packages your application needs.

:::info[Why version 20?]

Laravel-Lang grew out of `laravel-lang/lang`. New packages appeared over time, each with its own releases and version numbers. As the ecosystem grew, major upgrades took more coordination for us, and users had to figure out which versions would work together.

We created `laravel-lang/common` to make that choice easier by bringing in a compatible set of dependencies. Upgrading could still involve several packages, each with its own version.

For example, which versions would you have installed in a project running Laravel 11 on PHP 8.2 before Mona?

<details>
<summary>Package versions before Mona</summary>

| Package | Version |
| --- | --- |
| Common | `6.9.0` |
| Actions | `1.13.2` |
| Attributes | `2.16.6` |
| Lang | `15.39.2` |
| MoonShine | `1.9.3` |
| HTTP Statuses | `3.13.2` |
| Currency Names | `1.11.0` |
| Locale Names | `2.10.0` |
| Country Names | `1.8.0` |
| Starter Kits | `1.17.4` |
| Locales | `2.11.0` |
| Models | `1.8.0` |
| Routes | `1.11.0` |
| JSON Fallback | `2.3.0` |
| Locale List | `1.7.0` |
| Config | `1.17.1` (the 1.x release that other Laravel-Lang packages continued to use) and `2.3.2` (the latest stable release before Mona, rarely used by those packages) |
| Publisher | `16.9.0` |

</details>

The Mona monorepo lets us release the packages together under a shared version.

Publisher had the highest version number, and `17.0` would have been its next major version. We put a lot of work and care into bringing the ecosystem together, and we chose 20 to mark that milestone. You can also read it as **Laravel-Lang 2.0**. 🙂

:::

:::info[Locale codes and aliases]

Laravel-Lang now uses canonical locale codes for values it stores, including published language paths and model translation rows. Laravel's active and fallback locales also use canonical codes. At runtime, use aliases for URL segments and language selectors. Facade methods that return locale codes use the configured alias by default, or the canonical code when no alias is configured. For example, configure `'pt_BR' => 'pt-br'` in `localization_main.aliases` to keep `/pt-br/account` while publishing to `lang/pt_BR.json` and storing `pt_BR` in the database.

:::

## Updating dependencies

Mona 20.x supports Laravel 12 and 13 and requires PHP `^8.3`. Check that the stable 20.x versions you need have been published before upgrading. A Mona release and its Composer package releases can become available at different times. [Release notes](./release-notes.md)

In your application's `composer.json`, set the constraint to `^20.0` for each package below that appears in `require` or `require-dev`. If you installed only `laravel-lang/common`, it is the only requirement you need to change.

| Use | Composer packages |
| --- | --- |
| Bundle | `laravel-lang/common` |
| Translation packs | `laravel-lang/lang`, `laravel-lang/starter-kits`, `laravel-lang/moonshine`, `laravel-lang/actions`, `laravel-lang/attributes`, `laravel-lang/http-statuses` |
| Publishing and configuration | `laravel-lang/publisher`, `laravel-lang/config`, `laravel-lang/json-fallback`, `laravel-lang/library` |
| Application integrations | `laravel-lang/locales`, `laravel-lang/locale-list`, `laravel-lang/routes`, `laravel-lang/models` |
| Shared utilities | `laravel-lang/exceptions`, `laravel-lang/filesystem` |
| Pack template | `laravel-lang/translations-template` |

For an installation that only uses the standard translation workflow, apply the dependency update and republish your translations. If you registered providers, published custom settings, use aliases or third-party translation packs, or call package APIs in your application, read the sections below that apply to you before running these commands.

```bash
composer update
php artisan lang:update
```

## Replace renamed or obsolete dependencies {#replace-renamed-or-retired-dependencies}

Update any direct dependencies on the old names packages in `composer.json` before running `composer update`.

| Previous Composer package | Require at `^20.0` |
| --- | --- |
| `laravel-lang/native-country-names` | `laravel-lang/country-names` |
| `laravel-lang/native-currency-names` | `laravel-lang/currency-names` |
| `laravel-lang/native-locale-names` | `laravel-lang/locale-names` |

Common already depends on all three names packages. Update your PHP code as well. See [replacing the name lookup APIs](#replace-the-name-lookup-apis).

Check each third-party plugin for a release compatible with the new Publisher. Migrate any use of `laravel-lang/translator` or `laravel-lang/status-generator` before dropping those dependencies. See [translation tooling](#update-json-fallback-and-translation-tooling).

## Update service providers

Laravel discovers the Config, Publisher, Routes, and Models providers from the package manifests. If you rely on discovery, update any application code or tests that refer to old provider classes. If your application registers the packages manually, use these replacements in the same provider list, such as `bootstrap/providers.php` or `config/app.php`.

- `LaravelLang\Config\ServiceProvider` → `LaravelLang\Config\LangConfigServiceProvider`
- `LaravelLang\Publisher\ServiceProvider` → `LaravelLang\Publisher\LangPublisherServiceProvider`
- `LaravelLang\Routes\ServiceProvider` → `LaravelLang\Routes\LangRoutesServiceProvider`
- `LaravelLang\Models\ServiceProvider` → `LaravelLang\Models\LangModelsServiceProvider`

Remove manual registrations of `LaravelLang\Locales\LaravelLangLocalesServiceProvider` and the old `ServiceProvider` classes under `LaravelLang\Actions`, `LaravelLang\Attributes`, `LaravelLang\HttpStatuses`, `LaravelLang\Lang`, `LaravelLang\MoonShine`, and `LaravelLang\StarterKits`. These translation packages are discovered through their manifests.

If you use JSON fallback, replace `LaravelLang\JsonFallback\TranslationServiceProvider` with `LaravelLang\JsonFallback\LangJsonFallbackServiceProvider` where you registered it. This keeps the override of Laravel's translation provider active.

Update stale provider references before running `composer update` so package discovery can boot the application. If a Composer script still fails because it loads an old class, run `composer update --no-scripts`, update the references, and run `composer dump-autoload` to run discovery again.

## Migrate configuration

If you customized `config/localization.php`, keep it as a reference while you move your changes. The packages load the new defaults automatically. Publish the [configuration files](./configure.md) you need to edit. The `localization` tag publishes all five, or use a filename without `.php` as the tag to publish one.

```bash
php artisan vendor:publish --tag=localization
```

### Aliases, translation styles, and file formatting

- Copy your `aliases` map to `config/localization_main.php`. Keep canonical codes as keys and aliases as values, such as `'de_CH' => 'de-CH'`.
- Replace `inline` with `styles` in `config/localization_main.php`. Set `'styles' => ['default']` to keep the previous default with standard validation messages. Keep `['default', 'inline']`, the new default, if you used inline messages. Remove `LOCALIZATION_INLINE` and `LANG_PUBLISHER_INLINE` from the environments where you set them.
- Remove the `align` key and the `LOCALIZATION_ALIGN` and `LANG_PUBLISHER_ALIGN` environment variables. To align the `=>` operators in published PHP arrays, configure your project's PHP code formatter and run it on the language files after publication. For example, Laravel Pint's `binary_operator_spaces` rule can apply `align_single_space_minimal` to `=>`.

### Smart punctuation

If you enabled smart punctuation, rename `LOCALIZATION_SMART_ENABLED` to `LOCALIZATION_PUNCTUATION_ENABLED` and keep it set to `true`. If you set `smart_punctuation.enable` directly in PHP, move the boolean to the top-level `enabled` key in `config/localization_punctuation.php`. Punctuation is off by default.

Move any customized quote marks from `smart_punctuation.common` and `smart_punctuation.locales` to the top-level `common` and `locales` arrays in that file. Key `locales` by canonical codes. Each locale's quote marks override the corresponding keys in `common`. If you copied an older French example that uses `&nbsp;`, use an actual nonbreaking space or `"\u{00A0}"` in the quote strings.

### Localized routes

Move your overrides from `routes` in the old config into `config/localization_routes.php`.

| What you customized | Change in `config/localization_routes.php` | Environment variable to rename |
| --- | --- | --- |
| Locale parameter, header, cookie, session key, or user model column | Copy your `names` overrides and keep the remaining default entries. | |
| Prefix attached to localized route names | Move `name_prefix` to `prefix`. An empty value uses `localized.`. | `LOCALIZATION_NAME_PREFIX` → `LOCALIZATION_PREFIX` |
| Redirects from the prefixed route | Move `redirect_default` to `redirect`. | `LOCALIZATION_REDIRECT_DEFAULT` → `LOCALIZATION_REDIRECT` |
| Whether the URL helper hides the fallback locale | Move `hide_default` to `hide`. | `LOCALIZATION_HIDE_DEFAULT` → `LOCALIZATION_HIDE` |
| Middleware applied to localized route groups | Move `group.middlewares.default` to `middlewares.default` for unprefixed routes and `group.middlewares.prefix` to `middlewares.prefix` for prefixed routes. Update the middleware class names as shown in [the routes migration](#update-localized-routes-and-middleware). | |

### Model translations

Move your overrides from `models` to `config/localization_models.php`. Copy custom `suffix` and `helpers` values to the top level. Use a boolean `filter` instead of the old `filter.enabled` key. If you control filtering and the IDE helper path through the environment, keep `LOCALIZATION_FILTER_ENABLED` and `VENDOR_PATH`.

The model commands now scan `app/Models` by default. If you relied on the old scan of all of `app/`, or set `localization-private.models.directory` yourself, put each directory you want to scan in the `directories` array. For example, set `'directories' => [app_path()]` to retain the old scan.

### Configuration used by application code

- Replace reads and writes through `LaravelLang\Config\Facades\Config` with Laravel configuration calls against the new keys. For example, replace `Config::shared()->models->filter->enabled` with `config('localization_models.filter')`. If you prefer typed reads, inject `LaravelLang\Config\Services\ModelLangConfig` and read its `filter` property, or inject `RouteLangConfig` from the same namespace for route settings.
- If your code reads locale metadata from `localization-private.map`, read `localization_map.locales` instead. Copy overrides to the `locales` array in `config/localization_map.php`.
- Replace registration through `localization-private.plugins` and `localization-private.packages` with a [plugin manifest](./plugins.md).
- Remove the legacy `translators` block and any old `TRANSLATION_*` settings that were used only by `laravel-lang/translator`.

After moving your overrides and updating application references, remove `config/localization.php` and run `php artisan config:clear`.

## Move language files to canonical paths

1. Set `APP_LOCALE` and `APP_FALLBACK_LOCALE` to canonical codes. Move your aliases to `localization_main.aliases` so URLs and locale inputs keep working.
2. Rename aliased files and directories, such as `lang/pt-br.json` and `lang/pt-br/`, to `lang/pt_BR.json` and `lang/pt_BR/`. Check for the same aliases under `lang/vendor/<package>/`. Where both names exist, merge their contents and resolve conflicting custom translations before removing the alias path.
3. Install each locale represented only by vendor PHP translations with `php artisan lang:add <locale>`. The installed list uses entries directly under the language directory, JSON files under `lang/vendor/<package>/`, and the effective current and fallback locales.
4. Run `php artisan lang:update` after migrating configuration and plugins, then review the diff. Publisher can keep keys from integrations that no longer publish a file. Keep any keys your application still uses and remove obsolete ones after checking their callers. See [publication scripts and custom translations](#update-publication-scripts-and-keep-custom-translations) for regenerating or preserving files.

## Update integrations

### Locales

Keep the `LaravelLang\Locales\Facades\Locales` import and update the calls according to the data each caller uses.

| Previous call | Replacement |
| --- | --- |
| `Locales::available()`, `installed()`, or `protects()` returning objects | Call the same method on `Locales::data()`. |
| `Locales::raw()->available()`, `installed()`, or `protects()` | Call the same method directly on `Locales` for a collection of aliased strings. Pass `false` for canonical codes and call `->all()` if the caller requires an array. |
| `Locales::installed(false)` or `Locales::raw()->installed(false)` | `false` now selects canonical codes. Both current and fallback locales are included even without language files. Replace the old named argument `withProtects: false` too. Use `Locales::installed(false)->diff(Locales::protects(false))->values()` to exclude them. Use `Locales::data()->installed()->reject(fn ($locale) => Locales::isProtected($locale))->values()` for objects. |
| `Locales::notInstalled()` or `Locales::raw()->notInstalled()` | Use `Locales::data()->uninstalled()` for objects, or `Locales::uninstalled()` for aliased strings. Pass `false` to get canonical strings. |
| `Locales::getCurrent()` or `Locales::getDefault()` | Use `Locales::data()->current()` for a DTO or `Locales::current()` for a string. Replace `Locales::raw()->getDefault()` with `Locales::current()`. If you relied on `Locales::raw()->getCurrent()` for the value held by Laravel, use `app()->getLocale()`. |
| `Locales::getFallback()` or `Locales::raw()->getFallback()` | Use `Locales::data()->fallback()` for a DTO or `Locales::fallback()` for a string. An unknown fallback locale now resolves to English. An unknown active locale resolves to the effective fallback. |
| `Locales::get($locale)` or `Locales::raw()->get($locale)` | Use `isInstalled()` and select the fallback explicitly. For a supported locale that may be uninstalled, use `isAvailable()` followed by `info()`. |
| `Locales::info($locale)` or `Locales::raw()->info($locale)` | Use `Locales::info($locale)` and read `->alias` or `->code` when you need a string. Pass `null` for the current locale. For an unknown or empty code, handle `LaravelLang\Exceptions\UnknownLocaleException` or check `isAvailable()` before looking it up. |

To preserve the old `get()` behavior of falling back to the current locale for uninstalled or invalid input, make that choice in the caller.

```php
use LaravelLang\Locales\Facades\Locales;

$locale = Locales::acceptsInput($requested) && Locales::isInstalled($requested)
    ? Locales::info($requested)
    : Locales::data()->current();

$code = $locale->code;   // Use this in paths, database rows, and other persisted data.
$alias = $locale->alias; // Use this in URLs and language selectors.
```

Update request handlers that previously passed the result of `raw()->get()` to `app()->setLocale()`. Use `Locales::set($locale)` after accepting the request value. It accepts installed locale codes, configured aliases, enum cases, and the new DTO, and assigns Laravel the canonical code. For a request value of `pt-br`, Laravel receives `pt_BR`, while `Locales::current()` returns `pt-br`. Call `Locales::current(false)` or `Locales::fallback(false)` where you need canonical codes.

For other persistence in your application, validate the value with `isAvailable()` or `isInstalled()`, then use `fromAlias()` to resolve its canonical code. Use `toAlias()` to format a canonical code for display. Replace uses of the old `Services\Resolver` with `acceptsInput()` to check the accepted PHP types or `normalize()` to extract a string from a typed value. `normalize()` preserves strings as supplied, including an alias such as `pt-br`.

Replace `LaravelLang\Locales\Data\LocaleData` with `LaravelLang\LocaleNames\LocaleData` in imports, type hints, and tests. The new readonly object has `code`, `alias`, `native`, and `localized` string properties. Change uses of the old `code` property to `alias` for URLs and UI values. Use `Locale::from($data->code)` where you previously read `$data->locale`, importing `LaravelLang\LocaleList\Locale`. Read `type`, `regional`, and `direction` from `config("localization_map.locales.{$data->code}", [])` if your templates or formatters use them. Supply your defaults for missing entries. To keep the legacy defaults, use `Latn` for the script, the canonical code for the region, and `Direction::LeftToRight` for the direction.

Remove the `withCountry`, `withCurrency`, `withCountries`, and `withCurrencies` flags. Fetch the country and currency explicitly for the locale row. Update imports of the old `LaravelLang\Locales\Data\CountryData` and `CurrencyData` types to the data classes in the corresponding names packages.

```php
use LaravelLang\CountryNames\CountryNames;
use LaravelLang\CurrencyNames\CurrencyNames;
use LaravelLang\Locales\Facades\Locales;

$locale = Locales::info('pt_BR');
$displayLanguage = Locales::current(false);

$country = app(CountryNames::class)->one($displayLanguage, $locale->code);
$currency = app(CurrencyNames::class)->one($displayLanguage, $locale->code);
```

If you called `localizedLocales()`, `localizedCountries()`, or `localizedCurrencies()` on the old service, use `all(null)` and `all($displayLanguage)` on the matching names service for the native and localized values respectively. Replace direct injection of the previous `Services\Locales` or `Services\RawLocales` classes with the facade or the corresponding `Services\MainService` and `Services\DataService`. See [Locales and names](./locales.md) for the new service APIs.

#### Locale enum helpers

The `Locale` and `Direction` enum cases retain their values. Replace uses of removed helpers from `archtechx/enums` when upgrading custom locale lists or selectors.

| Previous helper | Replacement |
| --- | --- |
| `names()` | `array_column(Locale::cases(), 'name')` |
| `options()` | Build the name-to-value map with `array_column(Locale::cases(), 'value', 'name')`. |
| `stringOptions()` | Generate options in the view from `Locale::cases()`. Use the case value for a canonical code or `Locales::toAlias($case)` for a display alias. |
| `fromName()` or `tryFromName()` | Find the case by its `name` in `Locale::cases()` and handle a missing match. For a backed value such as `pt_BR`, use `Locale::from()` or `Locale::tryFrom()`. |
| `Locale::onlyValues()` | Build the value-to-value map with `array_column(Locale::cases(), 'value', 'value')`. For a list of values, use `Locale::values()`. |
| `Direction::values()` | Use `array_column(Direction::cases(), 'value')`. |

### Replace the name lookup APIs

Update imports from `LaravelLang\NativeCountryNames`, `LaravelLang\NativeCurrencyNames`, and `LaravelLang\NativeLocaleNames` to `LaravelLang\CountryNames`, `LaravelLang\CurrencyNames`, and `LaravelLang\LocaleNames`. Resolve `CountryNames`, `CurrencyNames`, or `LocaleNames` through the Laravel container or inject the service into your class.

| Previous usage | Replacement |
| --- | --- |
| `SomeNames::get($displayLocale)` | `$names->all($displayLocale)` |
| `SomeNames::get()` for native names | `$names->all(null)` |
| `SortBy::None` | Omit the sorting argument. |
| `SortBy::Key` | Call `->sortKeys()` on the returned collection. |
| `SortBy::Value` | Pass `SortDirection::Ascending` to `all()` to sort by localized name. |

Use `one($displayLocale, $entryLocale)` to fetch a known entry. The lookup key is a locale such as `fr`. For example, `app(CountryNames::class)->one('de', 'fr')` returns the country for French with its name in German and `code` set to `FR`. The country and currency DTOs live in their package's root namespace and expose the lookup key as `locale`. Their `code` fields still hold the country or currency code.

All three services return collections keyed by canonical locale. If you used the array of strings from the old locale names package, map each DTO to `localized`. If you called `toArray()` on old country or currency DTOs or changed their properties, build the application array explicitly. The new DTOs are readonly.

```php
use LaravelLang\LocaleNames\LocaleData;
use LaravelLang\LocaleNames\LocaleNames;

$options = app(LocaleNames::class)
    ->all('de', SortDirection::Ascending)
    ->map(fn (LocaleData $locale): string => $locale->localized)
    ->all();
```

Validate user-selected locales against the relevant catalog before calling `one()`. An unknown display locale can produce an empty list from `all()`. Pass `null` when your application needs native names. If your code used a custom backed enum, pass its string value. [Locales and names](./locales.md)

### Update localized routes and middleware

Import the namespaced helper in PHP files and Blade templates that generate localized URLs. You can also call `\LaravelLang\Routes\localizedRoute()` directly.

```php
use LaravelLang\Locales\Facades\Locales;

use function LaravelLang\Routes\localizedRoute;

$url = localizedRoute('products.show', [
    'product' => 42,
    'locale' => Locales::current(),
]);
```

`Route::localizedGroup()` and `LocalizationRoute::group()` register both unprefixed and locale-prefixed routes. Keep the existing group and review calls to the URL helper. It uses the fallback alias when the locale parameter is omitted. For a language switcher, pass the desired alias explicitly with `Locales::toAlias($locale)` or `$localeData->alias`. A supplied locale string is used as-is. Match the array key to `localization_routes.names.parameter` if you renamed it.

Replace imports and registrations in your routes, `bootstrap/app.php`, and custom middleware lists. All classes in this table are in `LaravelLang\Routes\Middlewares`.

| Previous middleware | Replacement |
| --- | --- |
| `LocalizationByCookie` | `LocalizationByCookieMiddleware` |
| `LocalizationByHeader` | `LocalizationByHeaderMiddleware` |
| `LocalizationByModel` | `LocalizationByModelMiddleware` |
| `LocalizationBySession` | `LocalizationBySessionMiddleware` |
| `LocalizationByParameter` | `LocalizationByParameterMiddleware` |
| `LocalizationByParameterWithRedirect` | `LocalizationByParameterWithRedirectMiddleware` |
| `LocalizationByParameterPrefix` | `LocalizationByParameterPrefixMiddlewareMiddleware` |

If you constructed model middleware with a guard, pass it as a Laravel middleware parameter, for example `LocalizationByModelMiddleware::class . ':admin'`. Its user resolver expects an Eloquent model. In custom middleware subclasses, inject `LaravelLang\Config\Services\RouteLangConfig`, read source names from `$this->routeConfig->name`, and return `int|string|null` from `detect()`. Replace direct references to `LaravelLang\Routes\Services\Route` with `LaravelLang\Routes\Services\RouteService`.

Test each source your application uses. Header detection uses PHP Intl to check installed languages in `Accept-Language` preference order, trying the full tag before its base language. A later middleware can override an earlier source, including the locale from the URL. To make the URL authoritative for prefixed routes, configure the prefixed chain to use only the URL locale middleware. [Middleware configuration](./routes.md#select-a-locale-with-middleware)

Test URL generation and requests separately. `localization_routes.hide` suppresses the fallback locale segment when generating a URL. `localization_routes.redirect` redirects prefixed requests for the current locale to the unprefixed route. A named, prefixed route also redirects an invalid or uninstalled locale to the unprefixed route.

In listeners for `LocaleHasBeenSetEvent`, use `$event->locale->code` for the canonical code and `$event->locale->alias` for presentation. The payload is now `LaravelLang\LocaleNames\LocaleData`. The middleware sends the event when the selected locale changes and saves the canonical code in the session. Replace manual `LocaleHasBeenSetEvent::dispatch($data)` calls with `event(new LocaleHasBeenSetEvent($data))`. [Localized routes](./routes.md)

### Migrate model translation code and data

Check the `locale` column in each translation table for aliased rows. For each alias, update those rows to its canonical code in a data migration. Before updating, check whether the same `item_id` already has a row for the canonical code. Merge any values you want to keep into one row to satisfy the unique `(item_id, locale)` constraint. Versions of the old trait already wrote canonical codes, so base this migration on the values actually stored in your tables.

Update imports and generated classes.

| Previous usage | Replacement |
| --- | --- |
| `LaravelLang\Locales\Data\LocaleData` | `LaravelLang\LocaleNames\LocaleData` |
| `LaravelLang\Models\Casts\TrimCast` | `LaravelLang\Models\Casts\TrimStringCast` |
| `LaravelLang\Models\Exceptions\UnavailableLocaleException` | `LaravelLang\Exceptions\UnknownLocaleException` |
| `LaravelLang\Models\Exceptions\AttributeIsNotTranslatableException` or `UnknownModelPathException` | The same class name in `LaravelLang\Exceptions`. |
| `translationModelName()` | `translationModel(): string`. Update overrides that select a custom translation class. |
| `getTranslationTable()` | Use `translationQualifyColumn($column)` for a qualified column, or `translationsRaw()->getRelated()->getTable()` for the table name. |

`getTranslation()`, `setTranslation()`, `hasTranslated()`, and `forgetTranslation()` accept a locale string, `Locale`, or the new `LocaleData`. Install any explicit locale used by these methods before calling them. A read without an explicit locale uses the fallback when the current value is `null`. An explicit locale reads that locale's value directly. If you call the lower-level `translation()` method, pass a locale string or `null`. Convert a DTO with `$locale->code`.

```php
$article->setTranslation('title', 'Schweizer Titel', 'de-CH')->save();
$article->translation('de_CH');
```

With a `de_CH` → `de-CH` alias and Swiss German installed, both calls address the row stored as `de_CH`. When filling several values for a translated field, pass a map keyed by locale, such as `['en' => 'Title', 'de-CH' => 'Schweizer Titel']`. Indexed lists now raise `InvalidLocaleInputException`. Custom code setting the loaded `translations` relation should supply a Laravel collection of translation models.

Call `translated()`, `whereTranslation()`, and related scopes through the Eloquent query builder. Pass a code or alias as the scope's locale string. For `orderByTranslation()`, use `'asc'`, `'desc'`, or `SortDirection`. When querying a locale other than current or fallback via `whereTranslation()` or `whereTranslationLike()`, set `localization_models.filter` to `false` or query `translationsRaw()` directly.

Update event consumers that call `$event->locale->value` or compare `$event->locale` to a `Locale` enum. `TranslationHasBeenSetEvent` and `TranslationHasBeenForgetEvent` now carry a string alias for an explicit locale. The set event carries `null` when the caller omits the locale and runs when the attribute is assigned, before `save()`. Convert an alias with `Locales::fromAlias()` before using it as a persisted key. For manual dispatch of any of the three model translation events, use `event(new …)`.

If you keep generated IDE helpers, run `php artisan lang:models-helper` after updating the imports and model discovery paths. [Model translations](./models.md)

### Update publication scripts and keep custom translations

| Previous command or assumption | Migration |
| --- | --- |
| `lang:add <locale>` only refreshed the locales being added | It now refreshes all installed locales. Pass explicit codes in scripts and account for updates to existing locale files. |
| `lang:reset` | Use `lang:update` for a merge. For a clean rebuild, back up custom entries, remove the files you intend to regenerate, then run `lang:add` with explicit codes for the locales to rebuild. |
| `lang:rm <locale> --force` | Use `lang:rm <locale>`. Laravel's current and fallback locales are protected. |
| Checking only the `lang:rm` exit code | Check each result. `DONE` confirms removal and `FAILED` indicates that a path remains after attempted removal. `PROTECTED`, `INVALID`, and `SKIP` can accompany a successful exit code. |

If you call Artisan by command class, switch `LaravelLang\Publisher\Console\Add`, `Remove`, and `Update` to `AddCommand`, `RemoveCommand`, and `UpdateCommand`. For a custom text decorator, implement and bind `LaravelLang\Publisher\Contracts\Decorator` in place of `Contracts\TextDecorator`. Its `convert(string $locale, string $value): string` receives the canonical locale.

Run the first `lang:update` with your existing language files under version control. Publisher merges into existing targets and replaces matching keys, including numeric keys. Keep custom translations in a separate application translation file where possible. To maintain an entire generated target yourself, add its path pattern to `localization_main.skip`. Check any value that reverts to the source phrase. Publisher uses that phrase when the dictionaries lack a translation.

If an earlier update reindexed your `http-statuses.php` files, back up and remove the affected generated files. Remove any `'*/http-statuses.php'` entry you added to `localization_main.skip` as a workaround. Run `php artisan lang:update` to rebuild the files, then restore any custom translations under their intended status codes. [Manage translations](./translations.md)

### Replace legacy plugin registration

If you use a third-party translation pack, upgrade to its manifest-based release. If you maintain a pack, remove the provider and Composer discovery entry that register a legacy `LaravelLang\Publisher\Plugins\Provider`. If that provider also configures unrelated Laravel services, keep those registrations in a regular Laravel service provider.

1. Add `laravel_lang.json` to the root of the pack installed under `vendor/`. Turn each `Plugin::files()` entry into a `source` path relative to the pack's `source/` directory and a `target` path relative to the application's language directory. Use `{locale}` in the target.
2. Move any Composer matching rules to the manifest. Use `vendor` and `version` for an installed dependency and a separate entry with `project: true` for a root Composer project. If your old plugin used `$with_project_name = true` and supported both modes, create both entries.
3. Convert the dictionaries to `locales/<code>/<style>.json`. The values in your source files are the dictionary lookup keys. For example, a PHP entry `'enable' => 'Enable audit trail'` still creates the application key `audit.enable` when published to `{locale}/audit.php`. Store its translation under `"Enable audit trail"` in the dictionary.
4. From a Mona checkout, run the Toolkit command below against the legacy pack. This creates `default.json` and `inline.json` from the old JSON and PHP dictionaries and carries over locale exclusions. Review the output and add it to the new pack with your manifest and source files. Rename any alias-based dictionary directories to canonical codes.
5. Require the current Publisher in the pack's Composer manifest. Add `laravel-lang/library` as well if your pack expects translations from the shared dictionary. Install the resulting pack in an application and run `php artisan lang:update` to check its published output.

```bash
php src/Toolkit/bin/toolkit migrate-locales path/to/legacy-pack --target=path/to/new-pack/locales
```

For existing applications, check which built-in pack rules apply after the upgrade.

- If you still need the Laravel-Lang translations previously published for Spark Paddle, Spark Stripe, Nova Dusk Suite, or Nova Log Viewer, keep those keys in application-owned files or move the old sources and dictionaries into a custom pack.
- Starter Kit translations now match the project's Composer `name`. If you renamed the project or use a kit as a dependency, add the kit's Composer name, for example `laravel/livewire-starter-kit`, to `localization_main.force` to publish its translations. [Translation plugins](./plugins.md)

### Update JSON fallback and translation tooling

If application code imports or type-hints `LaravelLang\JsonFallback\Translator`, change the type to `LaravelLang\JsonFallback\OverridedTranslator` or depend on Laravel's translator contract. If you subclassed the old translator, move the custom behavior to an application translator or a translation service of your own. For an unresolved key, the replacement translator calls Laravel's missing-translation callback. Review that callback if your application registers one, and test keys that exist in fallback JSON and current-language PHP because fallback JSON is checked first. [JSON fallback](./translations.md#json-fallback)

If you use the separate `laravel-lang/translator` package for machine translation, migrate uses of its `Translate` facade and any Google, DeepL, or Yandex integrations to your chosen translation service before removing that dependency. Keep any translations already generated by those scripts in your application or pack dictionaries.

If you maintain pack catalogs with `vendor/bin/lang` from `laravel-lang/status-generator`, run the new commands from a Mona checkout after `composer install`.

| Previous script | Toolkit script to use |
| --- | --- |
| `vendor/bin/lang status --path=path/to/pack` | `php src/Toolkit/bin/toolkit status path/to/pack`. Read the generated `locales/<code>/_status.json` reports. |
| `vendor/bin/lang sync --path=path/to/pack` | `php src/Toolkit/bin/toolkit sync:package path/to/pack`. Use `sync:library` to maintain the shared Library. |
| `vendor/bin/lang create` | `php src/Toolkit/bin/toolkit create-locale path/to/pack`. This creates directories for known locales. |
| `vendor/bin/lang download` | Pass the ZIP URL with `--url`, the full destination with `--directory`, and any paths from the archive with repeatable `--paths`. |
| `vendor/bin/lang translate` | Move machine translation to a separate service. Run Toolkit `status` to find saved strings that still match the source. |

See the [Toolkit commands](./toolkit.md) for full examples.

## Verify the upgrade

Run your application tests. Check a published message in each important locale, a fallback translation, a localized URL with your configured alias, and a model translation stored under the canonical code. Review generated files and saved locale values alongside the source changes.
