---
title: ترجمه‌های مدل
description: با Laravel-Lang Models ویژگی‌های بومی‌شدهٔ Eloquent را ذخیره، بازیابی و پرس‌وجو کنید.
---

`laravel-lang/models` ویژگی‌های ترجمه‌شده را به مدل موجود Eloquent اضافه می‌کند. هر ردیف ترجمه، یک زبان منطقه‌ای و مقدار ویژگی‌ها برای آن زبان را ذخیره می‌کند. این بسته در Common گنجانده شده و به افزونهٔ Intl در PHP نیاز دارد.

```bash
composer require laravel-lang/models
```

## افزودن ترجمه به مدل {#add-translations-to-a-model}

اگر مدل `App\Models\Article` و مهاجرت جدول پایهٔ آن را دارید، فرمان‌های زیر را اجرا کنید.

```bash
php artisan make:model-translation 'App\Models\Article' --columns=title --columns=description
php artisan migrate
```

مولد، تریت `LaravelLang\Models\HasTranslations` را به مدل پایه اضافه می‌کند و مدل همراه `ArticleTranslation`، یک مهاجرت و فایل کمکی IDE می‌سازد. مهاجرت، یک کلید خارجی به مقاله، یک زوج یکتای `(item_id, locale)` و ستون‌های رشته‌ای تهی‌پذیر برای مقادیر ترجمه‌شده می‌سازد. اگر این مقادیر هم‌اکنون در جدول پایه‌اند، پیش از استفاده از خواننده‌های مدل جدید آن‌ها را به ردیف‌های ترجمه منتقل کنید.

اگر مدل خارج از `app/Models` قرار دارد، پوشهٔ آن را پیش از اجرای مولد به [`localization_models.directories`](./configure.md) اضافه کنید.

برای انتخاب تعاملی مدل و ستون‌ها `php artisan make:model-translation` را اجرا کنید. اگر فهرست را خالی بگذارید، `title` و `description` به کار می‌روند. برای افزودن فیلد در آینده، یک مهاجرت جدید بسازید و فیلد را به ویژگی‌های قابل مقداردهی و تبدیل‌های مدل ترجمه اضافه کنید.

## نوشتن و خواندن ویژگی‌های ترجمه‌شده {#write-and-read-translated-attributes}

پس از [نصب فرانسوی](./translations.md) می‌توانید با دادن کد استاندارد، نام مستعار، عضوی از `enum` با نام `Locale` یا `LocaleData` ترجمه‌ای ذخیره کنید. برای ذخیرهٔ پایدار تغییرات از `save()` در مدل پایه استفاده کنید.

```php
use App\Models\Article;
use LaravelLang\LocaleList\Locale;

$article = Article::create(['title' => 'First article']);
$article->setTranslation('title', 'Premier article', Locale::French)->save();

$article->title;                         // current locale, then fallback for a null value
$article->getTranslation('title', 'fr');  // 'Premier article'
```

با تعیین صریح زبان منطقه‌ای، مقدار همان زبان خوانده می‌شود. بدون آرگومان زبان منطقه‌ای، نوشتن با زبان منطقه‌ای جاری انجام می‌شود و هنگام خواندن، اگر مقدار جاری `null` باشد از زبان جایگزین تنظیم‌شده در Laravel استفاده می‌شود. نام‌های مستعار برای جست‌وجو و ذخیره‌سازی به کد استاندارد تبدیل می‌شوند. تعیین صریح زبان منطقه‌ای ناشناخته یا نصب‌نشده استثنای `LaravelLang\Exceptions\UnknownLocaleException` ایجاد می‌کند.

برای چند مقدار از یک فیلد از `setTranslations()` استفاده کنید. می‌توانید برای هر فیلد ترجمه‌شده به `create()` یا `fill()` نیز نگاشتی با کلید زبان منطقه‌ای بدهید. در نمونه فرض شده که انگلیسی و فرانسوی نصب شده‌اند.

```php
$article->setTranslations('title', [
    'en' => 'First article',
    'fr' => 'Premier article',
])->save();
```

با `isTranslatable('title')` فیلدهای پیکربندی‌شدهٔ مدل و با `hasTranslated('title', 'fr')` وجود مقداری پرشده را بررسی کنید. `forgetTranslation('fr')` ردیف فرانسوی و `forgetAllTranslations()` همهٔ ردیف‌های مدل را حذف می‌کند.

## پرس‌وجوی ترجمه‌ها {#query-translations}

متدهای `translated()`، `whereTranslation()` و `whereTranslationLike()` از رابطهٔ `translations()` پرس‌وجو می‌کنند. این رابطه به‌طور پیش‌فرض ردیف‌ها را به زبان‌های منطقه‌ای جاری و جایگزین محدود می‌کند. برای پرس‌وجوی سایر زبان‌های منطقه‌ای با این محدوده‌ها، `localization_models.filter` را روی `false` بگذارید. برای خواندن تمام ردیف‌های یک مدل از `translationsRaw()` استفاده کنید.

وقتی زبان منطقه‌ای جاری فرانسوی است، می‌توانید بر اساس عنوان فرانسوی پرس‌وجو و مرتب‌سازی کنید.

```php
use App\Models\Article;

$articles = Article::query()
    ->whereTranslationLike('title', 'Premier%', 'fr')
    ->orderByTranslation('title', 'asc', 'fr')
    ->get();
```

مدل‌ها رابطهٔ `translations()` را پیشاپیش بارگیری می‌کنند. `orWhereTranslation()` و `orWhereTranslationLike()` شرط جایگزین اضافه می‌کنند. `orderByTranslation()` بر اساس یک زبان منطقه‌ای مرتب می‌کند و پیش‌فرض آن زبان جاری است.

## مشاهدهٔ تغییرات و به‌روزرسانی فایل‌های کمکی IDE {#observe-changes-and-refresh-ide-helpers}

فضای نام `LaravelLang\Models\Events` رویدادهای `TranslationHasBeenSetEvent`، `TranslationHasBeenForgetEvent` و `AllTranslationsHasBeenForgetEvent` را دارد. رویداد تنظیم شامل مدل، ستون، نام مستعار اختیاری زبان منطقه‌ای، مقدار پیشین و مقدار جدید است. این رویداد هنگام انتساب ویژگی، پیش از ذخیرهٔ بعدی رخ می‌دهد.

برای تولید دوبارهٔ فایل‌های کمکی همهٔ مدل‌های شناسایی‌شده، `php artisan lang:models-helper` را اجرا کنید. پوشهٔ پیش‌فرض آن‌ها `vendor/_laravel_lang` است که `localization_models.helpers` آن را تعیین می‌کند.
