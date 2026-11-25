---
title: شروع کار
description: Laravel-Lang را نصب کنید و نخستین ترجمه‌های Laravel را منتشر کنید.
---

Laravel-Lang ترجمه‌های Laravel و بسته‌های زیست‌بوم آن را منتشر می‌کند. مجموعهٔ `laravel-lang/common` همچنین امکان [جست‌وجوی زبان‌های منطقه‌ای و نام‌ها](./locales.md)، [مسیرهای بومی‌سازی‌شده](./routes.md) و [ترجمهٔ مدل‌ها](./models.md) را فراهم می‌کند. اگر فقط بستهٔ ترجمهٔ Laravel و وابستگی‌های انتشار آن را می‌خواهید، `laravel-lang/lang` را انتخاب کنید.

## شروع سریع {#quick-start}

این فرمان‌ها را از پوشهٔ ریشهٔ برنامهٔ Laravel 12 یا 13 با Composer، PHP `^8.3` و افزونه‌های PHP به نام `intl` و `json` اجرا کنید. نسخهٔ Laravel شما ممکن است به نسخهٔ جدیدتری از PHP نیاز داشته باشد.

```bash
composer require laravel-lang/common
php artisan lang:add fr
```

با تابع کمکی ترجمهٔ Laravel در برنامهٔ خود، یکی از پیام‌های منتشرشده به زبان فرانسوی را بخوانید.

```php
__('auth.failed', locale: 'fr');
// Ces identifiants ne correspondent pas à nos enregistrements.
```

برای انتخاب فرانسوی به‌عنوان زبان پیش‌فرض، `APP_LOCALE=fr` را در فایل `.env` برنامه تنظیم کنید. با تنظیم `APP_FALLBACK_LOCALE=en`، جست‌وجویی که از زبان جایگزین پشتیبانی می‌کند، هنگام نبود ترجمه در زبان فعلی به انگلیسی رجوع می‌کند. اگر پیکربندی در حافظهٔ نهان است، آن را با `php artisan config:clear` پاک کنید. فراخوانی‌هایی مانند `__('auth.failed')` سپس از زبان فعلی برنامه استفاده می‌کنند.

- [نسخه‌های پیشین Laravel-Lang](/legacy)
- [مدیریت ترجمه‌ها](./translations.md) و [مشاهدهٔ زبان‌های منطقه‌ای موجود](./available-locales.md)
- [پیکربندی بومی‌سازی](./configure.md)
- [بررسی سازگاری](./release-notes.md#versioning-and-compatibility) یا [ارتقای نصب فعلی](./upgrade.md)

## آزمایش نسخهٔ نامزد انتشار {#try-a-release-candidate}

برای آزمایش نامزد انتشار منتشرشدهٔ 20.x، از یک برنامهٔ آزمایشی جداگانهٔ Laravel استفاده کنید. حداقل پایداری آن برنامه را روی `RC` بگذارید تا Composer بتواند وابستگی‌های هم‌نسخهٔ Laravel-Lang را هم نصب کند. نسخه‌ای را از [Packagist](https://packagist.org/packages/laravel-lang/common) انتخاب کنید. `<release-candidate>` را با نسخه‌ای که انتخاب کرده‌اید جایگزین کنید.

```bash
composer config minimum-stability RC
composer config prefer-stable true
composer require "laravel-lang/common:<release-candidate>"
php artisan lang:add fr
```
