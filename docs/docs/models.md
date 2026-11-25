---
title: Model translations
description: Store, read, and query localized Eloquent attributes with Laravel-Lang Models.
---

`laravel-lang/models` adds translated attributes to an existing Eloquent model. Each translation row stores a locale and that locale's attribute values. The package is included in Common and requires PHP Intl.

```bash
composer require laravel-lang/models
```

## Add translations to a model

For an existing `App\Models\Article` model with a base table migration, run:

```bash
php artisan make:model-translation 'App\Models\Article' --columns=title --columns=description
php artisan migrate
```

The generator adds the `LaravelLang\Models\HasTranslations` trait to the base model and creates the companion `ArticleTranslation` model, a migration, and an IDE helper. Run `php artisan make:model-translation` without arguments to choose a model interactively and enter columns one at a time. The generator uses `title` and `description` when you leave the column list empty.

The generated migration creates a foreign key back to the article with cascading deletes, a unique `(item_id, locale)` pair, and nullable string columns for the translated values. Review the migration and adjust column types and model casts to suit your data. If you already store these values in the base table, backfill the translation rows under the appropriate canonical locale codes and update callers before dropping the original columns.

If the model is outside `app/Models`, add its directory to [`localization_models.directories`](./configure.md) before running the generator. To add a translated field later, add the column in a new migration and include it in the companion model's fillable fields and casts.

## Write and read translated attributes

With [French installed](./translations.md), you can store a translation by passing its canonical code, alias, `Locale` enum case, or `LocaleData`. Use the base model's `save()` to persist changes.

```php
use App\Models\Article;
use LaravelLang\LocaleList\Locale;

$article = Article::create(['title' => 'First article']);
$article->setTranslation('title', 'Premier article', Locale::French)->save();

$article->title;                         // current locale, then fallback for a null value
$article->getTranslation('title', 'fr');  // 'Premier article'
```

An explicit locale reads that locale's value. Without a locale argument, writes use the current locale and reads fall back to the configured Laravel fallback locale when the current value is `null`. Aliases resolve to canonical codes for lookup and storage. An explicit unknown or uninstalled locale raises `LaravelLang\Exceptions\UnknownLocaleException`.

Use `setTranslations()` for several values of one field. You can also pass a locale-keyed map to `create()` or `fill()` for each translated field. For writes from HTTP requests, validate the translated field values and locale keys before passing them to the model. The example assumes English and French are installed.

```php
$article->setTranslations('title', [
    'en' => 'First article',
    'fr' => 'Premier article',
])->save();
```

Use `translatable()` to list the configured fields, `isTranslatable('title')` to check one field, and `hasTranslated('title', 'fr')` to check for a filled value. `forgetTranslation('fr')` deletes the French row, and `forgetAllTranslations()` deletes all rows for the model. Both deletion methods take effect immediately.

## Query translations

Models eager-load the `translations()` relation. By default it filters rows to the current and fallback locales. To query another locale directly for a single model, use `translationsRaw()` and filter its `locale` column by canonical code.

`translated()` checks for a row in the `translations()` relation. `whereTranslation()` matches a field exactly and `whereTranslationLike()` uses SQL `LIKE` patterns. Their `orWhereTranslation()` and `orWhereTranslationLike()` variants add an alternative condition. To query other locales through these scopes, set `localization_models.filter` to `false`.

With French as the current locale, query and sort by the French title:

```php
use App\Models\Article;

$articles = Article::query()
    ->whereTranslationLike('title', 'Premier%', 'fr')
    ->orderByTranslation('title', 'asc', 'fr')
    ->get();
```

`orderByTranslation()` sorts by a single locale, using the current locale when omitted. Pass `'asc'`, `'desc'`, or a `SortDirection` enum case. The sort looks up its locale independently of the filter on `translations()`.

## Observe changes and refresh IDE helpers

The `LaravelLang\Models\Events` namespace contains `TranslationHasBeenSetEvent`, `TranslationHasBeenForgetEvent`, and `AllTranslationsHasBeenForgetEvent`. The set event contains the model, column, optional locale alias, old value, and new value. It fires during attribute assignment before the subsequent save. The forget events fire after deletion and carry the model, plus the locale alias for a single-locale deletion.

Run `php artisan lang:models-helper` to rebuild helpers for all discovered models, or `php artisan lang:models-helper 'App\Models\Article'` to refresh one. The full rebuild clears the configured helper directory first. Its default is `vendor/_laravel_lang`, controlled by `localization_models.helpers`.
