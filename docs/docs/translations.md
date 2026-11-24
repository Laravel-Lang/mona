---
title: Manage translations
description: Add, update, and remove application and vendor translations with the Mona Publisher.
---

The Publisher is included with the official translation packs. Its Artisan commands write PHP and JSON translations to the application's language directory, normally `lang/`. Package-specific files may also be written under `lang/vendor/`.

## Add locales

Pass one or more [locale codes](./available-locales.md), separated by spaces. Configured aliases are accepted as input, and the resulting files use canonical codes.

```bash
php artisan lang:add fr de_CH
```

The command creates the locale directories and then updates **all installed locales**, including the application's current and fallback locales. Without arguments, it asks whether to install all supported locales. Pass explicit codes in automation.

## Update translations

```bash
php artisan lang:update
```

The Publisher finds locales from the application's current and fallback locales, files or directories at the root of the language directory, and JSON files under `lang/vendor/<package>/`. A vendor PHP translation directory alone does not mark a locale as installed. Use `lang:add` for that locale to create its application directory.

Translation packs declare supported packages, versions, and target files in [plugin manifests](./plugins.md). The Publisher combines shared and package dictionaries, then merges their translations into each target file. It keeps unrelated keys and replaces matching keys, including numeric keys such as `404` in `http-statuses.php`. When a phrase is missing from the dictionaries, the Publisher writes the source phrase. Keep custom overrides in separate translation files or [skip](./configure.md#skip-files-and-force-integrations) the generated targets that you maintain yourself.

Run `lang:update` after changing Composer dependencies or [publication settings](./configure.md). Review the generated diff.

## Remove locales

```bash
php artisan lang:rm de_CH
```

This deletes the canonical locale directory and JSON file from the language root and each immediate `lang/vendor/<package>/` directory, including any custom files they contain. For a linked locale directory, the command removes the link itself. The current and fallback locales are protected. Without arguments, the command asks whether to remove all unprotected locales and defaults to no.

The command reports `DONE`, `PROTECTED`, `INVALID`, `SKIP`, or `FAILED` for each locale. If a file or directory remains after attempted removal, the command reports its path and returns a failure exit code. Check the per-locale status to confirm removal, since `PROTECTED`, `INVALID`, and `SKIP` can accompany a successful exit.

## JSON fallback

`laravel-lang/json-fallback` adds lookup in the fallback locale's JSON before continuing to PHP translations when a JSON key is missing. Common includes the package. For a separate installation, run `composer require laravel-lang/json-fallback`.

Add `LaravelLang\JsonFallback\LangJsonFallbackServiceProvider::class` to the application providers in `bootstrap/providers.php`. If the application explicitly lists `Illuminate\Translation\TranslationServiceProvider`, replace that entry with the Mona provider. [Laravel provider registration](https://laravel.com/docs/13.x/providers#registering-providers)

With French as the current locale and English as the fallback, a key missing from `lang/fr.json` can now resolve to its value in `lang/en.json`.
