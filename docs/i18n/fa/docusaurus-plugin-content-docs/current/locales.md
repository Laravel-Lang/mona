---
title: زبان‌های منطقه‌ای و نام‌ها
description: زبان‌های منطقه‌ای پشتیبانی‌شده و نصب‌شده را بیابید، نام‌های مستعار را به کد تبدیل کنید و نام‌های بومی‌شده را به دست آورید.
---

Laravel-Lang در Laravel، نام فایل‌های ترجمه و ردیف‌های ترجمهٔ مدل از کدهای استاندارد مانند `en`، `fr` و `pt_BR` استفاده می‌کند. نام مستعار زبان منطقه‌ای نامی برای استفاده در برنامه است، مانند `pt-br` در یک URL. نگاشت کد استاندارد به نام مستعار را در [`localization_main.aliases`](./configure.md) تعریف کنید.

## یافتن زبان‌های منطقه‌ای پشتیبانی‌شده و نصب‌شده {#find-supported-and-installed-locales}

`laravel-lang/locale-list` `enum` مقدار‌دار `LaravelLang\LocaleList\Locale` را ارائه می‌کند. اعضای آن با [کدهای استاندارد پشتیبانی‌شده](./available-locales.md) متناظرند.

```php
use LaravelLang\LocaleList\Locale;

Locale::French->value;        // 'fr'
Locale::tryFrom('pt_BR');     // Locale::PortugueseBrazil
Locale::values();            // آرایه‌ای از کدهای استاندارد
Locale::collection();        // مجموعه‌ای از کدهای استاندارد
Locale::collection(false);   // مجموعه‌ای از اعضای enum
```

`laravel-lang/locales` نمای `LaravelLang\Locales\Facades\Locales` را اضافه می‌کند که از پیکربندی برنامه آگاه است. این متدها مجموعه‌هایی از رشته‌ها برمی‌گردانند. برای دریافت کدهای استاندارد، `false` را به‌عنوان آرگومان بدهید. به‌طور پیش‌فرض هر کد با نام مستعارِ تنظیم‌شده‌اش نمایش داده می‌شود، اگر چنین نامی موجود باشد.

| متد | مقدار برگشتی |
| --- | --- |
| `Locales::available()` | همهٔ زبان‌های منطقه‌ای شناخته‌شده |
| `Locales::installed()` | زبان‌های منطقه‌ای شناسایی‌شده از فایل‌های زبان برنامه به‌همراه موارد محافظت‌شده |
| `Locales::uninstalled()` | زبان‌های منطقه‌ای موجود که نصب نشده‌اند |
| `Locales::protects()` | زبان‌های منطقه‌ای جاری و جایگزینِ مؤثر، بدون موارد تکراری |

شناسایی، مدخل‌های مستقیم `lang_path()` و فایل‌های JSON منطبق با `vendor/*/*.json` نسبت به آن مسیر را می‌خواند. نام فایل یا پوشه باید به کدی پشتیبانی‌شده تبدیل شود. پوشه‌های ترجمهٔ صرفاً PHP در `lang/vendor/<package>/` خارج از این الگوهای شناسایی‌اند. برای روش انتشار زبان منطقه‌ای به [مدیریت ترجمه‌ها](./translations.md) مراجعه کنید.

## خواندن یا تنظیم زبان منطقه‌ای برنامه {#read-or-set-the-application-locale}

```php
use LaravelLang\Locales\Facades\Locales;

if (Locales::isInstalled('fr')) {
    Locales::set('fr');
}

$canonicalCode = Locales::current(false);
$displayCode = Locales::current();
```

`set()` کد، نام مستعار، عضوی از `enum` با نام `Locale` یا شیء `LocaleData` را می‌پذیرد. اگر زبان منطقه‌ای نصب شده باشد، زبان منطقه‌ای Laravel را روی کد استاندارد تنظیم می‌کند. برای بررسی پشتیبانی از کد از `isAvailable()` و در صورت نیاز به فایل یا محافظت از `isInstalled()` استفاده کنید. `isProtected()` زبان‌های منطقه‌ای جاری و جایگزین را تشخیص می‌دهد.

`current()` از زبان منطقه‌ای جاری Laravel، اگر شناخته‌شده باشد، و در غیر این صورت از `fallback()` استفاده می‌کند. `fallback()` زبان منطقه‌ای جایگزین تنظیم‌شدهٔ Laravel را پیدا می‌کند و اگر ناشناخته باشد `en` را برمی‌گرداند. هر دو متد آرگومان `false` را برای دریافت کد استاندارد می‌پذیرند.

برای تبدیل نام مستعار به کد استاندارد از `fromAlias()` و برای دریافت نام مستعار یک کد استاندارد از `toAlias()` استفاده کنید. این تبدیل‌ها رشته‌های غیرخالی و ناشناخته را دست‌نخورده برمی‌گردانند. برای اعتبارسنجی ورودی از `isAvailable()` استفاده کنید.

## نمایش گزینه‌های زبان منطقه‌ای {#display-locale-choices}

`Locales::info('fr')` شیء `LaravelLang\LocaleNames\LocaleData` را با ویژگی‌های `code`، `alias`، `native` و `localized` برمی‌گرداند. زبان نمایش، زبان منطقه‌ای جاری برنامه است. برای زبان منطقه‌ای جاری آرگومان را ننویسید. اطلاعات یک زبان منطقه‌ای شناخته‌شده پیش از نصب آن هم در دسترس است. کد ناشناخته استثنای `LaravelLang\Exceptions\UnknownLocaleException` ایجاد می‌کند.

با `Locales::data()->installed()`، زبان‌های منطقه‌ای نصب‌شده را به‌صورت اشیای `LocaleData` دریافت کنید. `data()` برای مجموعه‌های دیگر نیز `available()`، `uninstalled()` و `protects()` و برای اشیای تکی `current()` و `fallback()` را ارائه می‌کند. یک انتخابگر زبان می‌تواند مقدار `alias` را بفرستد و نام `native` را نمایش دهد.

## جست‌وجوی نام زبان‌های منطقه‌ای، کشورها و ارزها {#look-up-locale-country-and-currency-names}

هر یک از بسته‌های `laravel-lang/locale-names`، `laravel-lang/country-names` و `laravel-lang/currency-names` سرویسی دارند که می‌توانید از ظرف خدمات Laravel تزریق کنید. Common هر سه را در بر می‌گیرد.

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

پارامتر نخست زبان نمایش است. برای دریافت نام‌ها به زبان خودشان، `null` بدهید. پارامتر دوم `one()` زبان منطقه‌ای ردیف است، مانند `fr` در نمونه‌های بالا. در جست‌وجوی کشور و ارز، کد ISO در شیء برگشتی قرار دارد. هر دو پارامتر کد استاندارد، نام مستعار تنظیم‌شده و عضوی از `enum` با نام `Locale` را می‌پذیرند.

`all($forLocale)` مجموعه‌ای برمی‌گرداند که کلیدهای آن کدهای استاندارد زبان منطقه‌ای‌اند. برای مرتب‌سازی بر اساس ویژگی `localized`، `SortDirection::Ascending` یا `SortDirection::Descending` را بدهید. هر مقدار شیئی فقط‌خواندنی است. مقادیر کشور و ارز ویژگی `locale` ردیف را نیز در بر دارند.
