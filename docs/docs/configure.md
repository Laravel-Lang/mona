---
title: Configuration
description: Configure Mona's translation styles, locale aliases, publication filters, punctuation, routes, and models.
---

Mona loads default settings automatically. Publish the files you need to customize into your application's `config/` directory.

```bash
php artisan vendor:publish --tag=localization_main
```

The `localization` tag publishes all five files. Each filename without `.php` is also a publication tag and a Laravel configuration key.

| File | Settings |
| --- | --- |
| `localization_main.php` | Translation styles, aliases, and publication filters |
| `localization_punctuation.php` | Smart punctuation and locale-specific quote marks |
| `localization_routes.php` | [Route names, locale sources, and middleware](./routes.md) |
| `localization_models.php` | [Translation model suffix, filtering, and generator paths](./models.md) |
| `localization_map.php` | Locale metadata such as script, region, and direction |

Laravel replaces each published top-level setting with the application value. For example, when editing the `locales` array in `localization_punctuation.php`, keep any default locale overrides you still want to use.

After changing configuration, clear an existing Laravel config cache with `php artisan config:clear`. Run `php artisan lang:update` to regenerate translations affected by publication settings.

## Translation styles

A translation style is a named dictionary that changes the wording of selected phrases. For example, a pack can provide `informal.json` for a friendly tone or `john.json` for wording used by a product. Style names are independent of locale codes, application names, and package names. Any translation pack can provide a style under `locales/<locale>/<style>.json` for the languages it covers. A style can contain as few as one phrase. The original use case was informal forms of address. [Informal translations discussion](https://github.com/orgs/Laravel-Lang/discussions/124)

After you install a plugin that provides `john`, put it last in `config/localization_main.php` to give it priority within each dictionary source.

```php
'styles' => ['default', 'informal', 'john'],
```

When you run `php artisan lang:update`, Publisher merges the style files for each installed locale. For each phrase, `john` wins if it supplies the phrase, followed by `informal` and then `default`. For example, a French pack can override one phrase in `locales/fr/john.json`, while German uses `locales/de/informal.json` or `locales/de/default.json` for that same phrase. Publisher applies the order separately to the shared Library and the pack contributing the file. For that file, a matching phrase from the pack takes precedence over the Library's entry.

The default setting is `['default', 'inline']`. The `inline` style provides generic validation messages such as “This field must be accepted.” Set `styles` to `['default']` to use standard wording only. If you omit `default` from the list, Publisher adds it at the beginning.

## Locale aliases

Set `localization_main.aliases` as a map from canonical codes to aliases. For example, add the following entry to the returned array in `config/localization_main.php`.

```php
'aliases' => [
    'de_CH' => 'de-CH',
],
```

Artisan can accept `de-CH`, while Laravel and published files use `de_CH`. Alias matching is case-sensitive. Give each locale a distinct alias that is different from any other canonical code. Set `APP_LOCALE` and `APP_FALLBACK_LOCALE` to canonical codes. See [Locales and names](./locales.md) for conversion and lookup examples.

## Skip files and force integrations

- `localization_main.skip` matches case-insensitive `*` patterns against full paths with `/` separators. It can exclude source files, dictionaries, and publication targets. For example, `['*/lang/fr/validation.php']` keeps the Publisher from writing that target in an application using `lang/`.
- `localization_main.force` accepts Composer package names. It lets the Publisher use manifest entries even if the named dependency is absent, the installed version does not match, or the root project name differs. Use it when the manifest's translations are applicable to your application.

`skip` takes precedence over `force`.

## Smart punctuation

Set `LOCALIZATION_PUNCTUATION_ENABLED=true` to enable typographic quotes, dashes, and ellipses in published translations. The setting defaults to disabled. Edit `localization_punctuation.common` for general quote settings and `localization_punctuation.locales` for overrides keyed by canonical locale code. Published strings take on these rules the next time the Publisher writes them.

## Locale metadata

`localization_map.locales` maps canonical locale codes to script (`type`), regional locale (`regional`), and an optional writing direction (`direction`). Use `Direction::LeftToRight` when no direction is configured.

```php
use LaravelLang\LocaleList\Direction;

$metadata = config('localization_map.locales.ar', []);
$script = $metadata['type'];         // 'Arab'
$regional = $metadata['regional'];  // 'ar_AE'
$dir = ($metadata['direction'] ?? Direction::LeftToRight)->value; // 'rtl'
```
