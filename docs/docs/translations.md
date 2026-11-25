---
title: Manage translations
description: Add, update, and remove application and vendor translations with the Mona Publisher.
---

The Publisher is included with the official translation packs. Run its Artisan commands from the Laravel application root. They write PHP and JSON translations to the directory returned by `lang_path()`, normally `lang/`. Package-specific files may also be written under `lang/vendor/`.

## Add locales

Pass one or more [locale codes](./available-locales.md), separated by spaces. Configured aliases are accepted as input, and the resulting files use canonical codes.

```bash
php artisan lang:add fr de_CH
```

The command creates the locale directories and then updates **all installed locales**, including the application's current and fallback locales. Without arguments, it asks whether to install all supported locales. An unknown locale code fails the command before any directories are created. Pass explicit codes in automation.

## Update translations

```bash
php artisan lang:update
```

`lang:update` takes no locale arguments. It refreshes every [installed locale](./locales.md#find-supported-and-installed-locales). If the locale only has a vendor PHP translation directory, use `lang:add` to create its application directory.

Translation packs declare supported packages, versions, and target files in [plugin manifests](./plugins.md). The Publisher combines shared and package dictionaries, then merges their translations into each target file. It keeps unrelated keys and replaces matching keys, including numeric keys such as `404` in `http-statuses.php`. When a phrase is missing from the dictionaries, the Publisher writes the source phrase. Keep custom overrides in separate translation files or [skip](./configure.md#skip-files-and-force-integrations) the generated targets that you maintain yourself.

The Publisher explicitly writes all keys in generated PHP translation files as strings, including numeric keys such as `'404'`. When PHP loads the file, it converts a key such as `'404'` to the integer `404`. Each entry in a nonempty nested PHP array gets its own line. JSON translation dictionaries are written as objects with string keys at every level, including dictionaries with consecutive numeric keys.

In the application's `validation.php` files, the Publisher places any top-level `custom` and `attributes` keys last, in that order. The other top-level keys are sorted alphabetically.

Run `lang:update` after changing Composer dependencies or [publication settings](./configure.md). Review the generated diff.

On a fresh checkout, run `composer install` before publishing. Deploy the generated language files with the application or generate them during deployment, when the language directory is writable. If the files are absent, run `php artisan lang:add fr de_CH --no-interaction` with the application's locale codes to create and populate their directories. If the files are already present, run `php artisan lang:update --no-interaction` to refresh them.

## Remove locales

```bash
php artisan lang:rm de_CH
```

Pass canonical locale codes or configured aliases. This deletes the canonical locale directory and JSON file from the language root and each immediate `lang/vendor/<package>/` directory, including any custom files they contain. For a linked locale directory, the command removes the link itself. The current and fallback locales are protected. Without arguments, the command asks whether to remove all unprotected locales and defaults to no.

The command reports `DONE`, `PROTECTED`, `INVALID`, `SKIP`, or `FAILED` for each locale. If a file or directory remains after attempted removal, the command reports its path and returns a failure exit code. Check the per-locale status to confirm removal, since `PROTECTED`, `INVALID`, and `SKIP` can accompany a successful exit.

## Find the Artisan commands

Run `php artisan help lang:add` to check that the command is registered. If it is missing, check that `laravel-lang/publisher` is installed with `composer show laravel-lang/publisher`, then run `php artisan package:discover`. If you disable package discovery for Publisher and Config, register `LaravelLang\Config\LangConfigServiceProvider::class` and `LaravelLang\Publisher\LangPublisherServiceProvider::class` in your application's `bootstrap/providers.php`.

## JSON fallback

`laravel-lang/json-fallback` adds lookup in the fallback locale's JSON before continuing to PHP translations when a JSON key is missing. Common includes the package. For a separate installation, run `composer require laravel-lang/json-fallback`.

Add `LaravelLang\JsonFallback\LangJsonFallbackServiceProvider::class` to the application providers in `bootstrap/providers.php`. If the application explicitly lists `Illuminate\Translation\TranslationServiceProvider`, replace that entry with the Mona provider. [Laravel provider registration](https://laravel.com/docs/13.x/providers#registering-providers)

With French as the current locale and English as the fallback, a key missing from `lang/fr.json` can now resolve to its value in `lang/en.json`. Use `Lang::get($key, fallback: false)` with the `Illuminate\Support\Facades\Lang` facade when a lookup should stay in the requested locale.
