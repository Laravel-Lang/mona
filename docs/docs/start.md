---
title: Getting started
description: Install Laravel-Lang and publish your first Laravel translations.
---

Laravel-Lang publishes translations for Laravel and its ecosystem. The `laravel-lang/common` bundle also provides [locale and name lookups](./locales.md), [localized routes](./routes.md), and [model translations](./models.md). To install only the Laravel translation pack and its publishing dependencies, choose `laravel-lang/lang` instead.

## Quick start

Run these commands from the root of a Laravel 12 or 13 application with Composer, PHP `^8.3`, and the `intl` and `json` PHP extensions. Your Laravel version may require a newer PHP version.

```bash
composer require laravel-lang/common
php artisan lang:add fr
```

Use Laravel's translation helper in your application to read a published message in French.

```php
__('auth.failed', locale: 'fr');
// Ces identifiants ne correspondent pas à nos enregistrements.
```

To make French the default language, set `APP_LOCALE=fr` in your application's `.env` file. Set `APP_FALLBACK_LOCALE=en` to use English when a supported fallback lookup cannot find a translation in the current locale. Clear an existing configuration cache with `php artisan config:clear`. Calls such as `__('auth.failed')` then use the current application locale.

- [Earlier Laravel-Lang releases](/legacy) and [upgrade instructions](./upgrade.md)
- [Manage translations](./translations.md) and [browse available locales](./available-locales.md)
- [Configure localization](./configure.md)
- [Check compatibility](./release-notes.md#versioning-and-compatibility)

## Try a release candidate

Use a separate Laravel test application to try a published 20.x release candidate. Set the minimum stability in that application to `RC` so Composer can also install the matching Laravel-Lang dependencies. Choose a version listed on [Packagist](https://packagist.org/packages/laravel-lang/common). Replace `<release-candidate>` with the version you selected.

```bash
composer config minimum-stability RC
composer config prefer-stable true
composer require "laravel-lang/common:<release-candidate>"
php artisan lang:add fr
```
