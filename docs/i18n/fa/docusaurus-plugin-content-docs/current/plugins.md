---
title: افزونه‌های ترجمه
description: بسته‌های ترجمه را نصب کنید یا افزونهٔ Composer خودتان را برای Laravel-Lang منتشر کنید.
---

Publisher بسته‌های ترجمهٔ نصب‌شده با Composer را شناسایی می‌کند. هر بسته یک مانیفست `laravel_lang.json`، فایل‌های منبع PHP یا JSON و در صورت نیاز واژه‌نامه‌های زبان منطقه‌ای خود را ارائه می‌دهد.

## نصب بسته {#install-a-pack}

بسته را در برنامهٔ Laravel نصب و زبان‌های منطقه‌ای درحال‌استفاده را به‌روز کنید. برای نمونه، بستهٔ Actions برچسب دکمه‌ها و سایر عملیات را اضافه می‌کند.

```bash
composer require laravel-lang/actions
php artisan lang:update
```

برای افزودن زبان منطقه‌ای [مدیریت ترجمه‌ها](./translations.md) و برای بسته‌های شخص ثالث [افزونه‌های انجمن](./plugins-community.md) را ببینید.

### بسته‌های رسمی ترجمه {#official-translation-packs}

Common شامل این بسته‌هاست. می‌توانید هر یک را جداگانه نیز با Composer نصب کنید.

| بسته | ترجمه‌ها |
| --- | --- |
| `laravel-lang/lang` | فریم‌ورک Laravel و بسته‌هایی مانند Breeze، Cashier، Fortify، Jetstream، Nova، Passkeys و Laravel UI |
| `laravel-lang/starter-kits` | کیت‌های آغازین Livewire، React، Svelte و Vue برای Laravel |
| `laravel-lang/moonshine` | پنل مدیریت MoonShine |
| `laravel-lang/actions` | دکمه‌ها و دیگر برچسب‌های عملیات |
| `laravel-lang/attributes` | نام‌های خوانای ویژگی‌های اعتبارسنجی |
| `laravel-lang/http-statuses` | عبارت‌های وضعیت HTTP |

هر بسته Publisher و Library مشترک را به‌همراه دارد. مانیفست آن تعیین می‌کند که برای کدام یکپارچه‌سازی‌ها و نسخه‌ها می‌تواند ترجمه منتشر کند.

## ساخت بسته {#create-a-pack}

از الگوی ترجمه شروع کنید. در `composer.json` بسته، `laravel-lang/publisher` را لازم کنید و نسخه‌های پشتیبانی‌شده را مشخص کنید. اگر بسته به ترجمه‌های مشترک وابسته است، `laravel-lang/library` را هم لازم کنید. Publisher مانیفست‌ها را در `vendor/*/*/laravel_lang.json` در برنامه پیدا می‌کند. [الگوی ترجمه](https://github.com/Laravel-Lang/mona/tree/main/src/TranslationsTemplate)

مانیفست را در ریشهٔ بسته قرار دهید. این نمونه برای هر زبان منطقه‌ای نصب‌شده یک فایل JSON و یک گروه ترجمهٔ PHP منتشر می‌کند.

```json
{
  "$schema": "https://raw.githubusercontent.com/Laravel-Lang/mona/main/resources/laravel-lang.schema.json",
  "plugins": [
    {
      "files": [
        {"source": "audit.json", "target": "{locale}.json"},
        {"source": "audit.php", "target": "{locale}/audit.php"}
      ]
    }
  ]
}
```

هر مسیر `source` نسبت به پوشهٔ `source/` بسته است. هر `target` نسبت به پوشهٔ زبان Laravel در برنامه است و `{locale}` را در بر دارد.

برای نمونه، این محتوا را در `source/audit.json` قرار دهید.

```json
{
  "Enable audit trail": "Enable audit trail"
}
```

این محتوا را هم در `source/audit.php` قرار دهید.

```php
<?php

return [
    'enable' => 'Enable audit trail',
];
```

هنگام انتشار فرانسوی، هر دو عبارت انگلیسی را در `locales/fr/default.json` بستهٔ شما جست‌وجو می‌کنند.

```json
{
  "Enable audit trail": "Activer le journal d’audit"
}
```

در برنامه، `__('Enable audit trail')` ترجمهٔ JSON و `__('audit.enable')` ترجمهٔ PHP را می‌خواند. برای عبارت یکسان، ترجمهٔ بسته بر مدخل Library مشترک اولویت دارد. اگر هیچ واژه‌نامه‌ای آن عبارت را نداشته باشد، Publisher از مقدار منبع استفاده می‌کند.

برای بسته نام Composer مستقلی انتخاب کنید و `self.version` را در الگو با محدودیت نسخه‌ای متناسب با نسخهٔ پشتیبانی‌شده جایگزین کنید. برای نام پوشه‌های واژه‌نامه از کدهای معیار زبان استفاده کنید. ترجمه‌های معمول را در `default.json` و گونه‌های سبکی را در فایل‌هایی مانند `inline.json` بگذارید.

### اعمال مشروط ترجمه‌ها {#apply-translations-conditionally}

| ویژگی مانیفست | شرط اعمال نگاشت |
| --- | --- |
| بدون شرط | Publisher همیشه آن را اعمال می‌کند. |
| `vendor` | بستهٔ نام‌بردهٔ Composer نصب شده است. |
| `vendor` و `version` | نسخهٔ نصب‌شده شرط نسخهٔ Composer را برآورده می‌کند. |
| `vendor` و `project: true` | نام Composer پروژهٔ ریشه با `vendor` منطبق است. Publisher در این حالت نام را بررسی می‌کند. |

اگر نگاشت باید هم برای وابستگی نصب‌شده و هم برای پروژهٔ ریشه با همان نام اعمال شود، از دو مدخل مانیفست استفاده کنید. برای تبدیل ارائه‌دهندهٔ قدیمی به [راهنمای ارتقا](./upgrade.md) و برای قواعد استثنا و تطبیق اجباری بسته‌ها به [پیکربندی](./configure.md) مراجعه کنید.

### آزمایش بسته در یک برنامهٔ Laravel {#test-a-pack-in-a-laravel-application}

اگر پوشهٔ بسته کنار برنامهٔ آزمایشی است، دستورها را از ریشهٔ برنامه اجرا کنید. `lang/fr.json` و `lang/fr/audit.php` را بررسی کنید، زبان برنامه را روی `fr` بگذارید و هر دو کلید را آزمایش کنید. پس از ویرایش بسته، `php artisan lang:update` را اجرا کنید.

```bash
composer config repositories.audit-translations path ../audit-translations
composer require "acme/audit-translations:*@dev"
php artisan lang:add fr
```
