---
title: جعبه‌ابزار ترجمه
description: منابع و واژه‌نامه‌های ترجمه را در نسخهٔ کاری Mona نگه‌داری، مهاجرت و وارد کنید.
---

Toolkit ابزار خط فرمانِ نگه‌داری بسته‌های ترجمه است. آن را پس از نصب وابستگی‌های Composer پروژهٔ ریشه، از نسخهٔ کاری Mona اجرا کنید. برای انتشار ترجمه در یک برنامهٔ Laravel از [فرمان‌های Artisan](./translations.md) استفاده کنید.

```bash
composer install
php src/Toolkit/bin/toolkit list
php src/Toolkit/bin/toolkit migrate-locales --help
```

## بررسی وضعیت ترجمه {#check-translation-status}

```bash
php src/Toolkit/bin/toolkit status src/Library
```

فرمان به‌طور پیش‌فرض فایل `locales/<locale>/_status.json` را در پروژه می‌نویسد. مدخل‌هایی را فهرست می‌کند که ترجمهٔ ذخیره‌شدهٔ آن‌ها با عبارت منبع یکی است و آن‌ها را بر اساس سبک گروه‌بندی می‌کند. استثناهای `locales/_settings.json` و `locales/<locale>/_settings.json` را ترکیب و گزارش‌های خالی را حذف می‌کند. برای یافتن عبارت‌هایی که اصلاً در واژه‌نامه نیستند، آن را با فایل‌های منبع بسته مقایسه کنید.

برای نوشتن گزارش‌های `<locale>.json` در پوشه‌ای جداگانه، از گزینهٔ `--output-dir` استفاده کنید. فرمان پوشهٔ کاری فعلی را مبنای مسیرهای نسبی قرار می‌دهد و وقتی گزارشی برای نوشتن وجود داشته باشد، پوشهٔ مقصد را می‌سازد.

```bash
php src/Toolkit/bin/toolkit status src/Library --output-dir=workbench/status
```

## مدیریت پوشه‌های زبان‌های منطقه‌ای {#manage-locale-directories}

```bash
php src/Toolkit/bin/toolkit create-locale src/Library
php src/Toolkit/bin/toolkit delete-locale src/Library
```

`create-locale` برای تمام [کدهای استاندارد زبان منطقه‌ای](./available-locales.md) که پوشه ندارند، پوشه می‌سازد. پوشه‌های جدید در آغاز خالی‌اند. `delete-locale` هر زیرپوشهٔ مستقیم `locales/` را که نامش خارج از فهرست است حذف می‌کند. پیش از اجرا مقصد را بررسی کنید، به‌خصوص اگر نام مستعار یا کد سفارشی دارد.

## حذف مدخل‌های قدیمی واژه‌نامه {#remove-obsolete-dictionary-entries}

```bash
php src/Toolkit/bin/toolkit sync:library src
php src/Toolkit/bin/toolkit sync:package path/to/translation-pack
```

- `sync:library` عبارت‌های منبع را از بسته‌هایی جمع می‌کند که مانیفست `laravel_lang.json` در `src/` دارند و واژه‌نامه‌های `src/Library/locales/` را بازنویسی می‌کند.
- `sync:package` پوشهٔ `source/` یک بسته را می‌خواند و مدخل‌های استفاده‌نشده را در پوشهٔ `locales/` آن حذف می‌کند. واژه‌نامه‌هایی که پس از هرس خالی شده‌اند نیز حذف می‌شوند.

هر دو فرمان ترجمه‌های موجود عبارت‌های فعال و فایل‌هایی با پیشوند زیرخط، مانند `_settings.json`، را حفظ می‌کنند. ترجمهٔ عبارت‌های جدید را مستقیماً به واژه‌نامه اضافه کنید.

## مهاجرت واژه‌نامه‌های قدیمی {#migrate-legacy-dictionaries}

```bash
php src/Toolkit/bin/toolkit migrate-locales path/to/legacy-pack --target=path/to/new-pack/locales
```

بسته‌ای قدیمی را که `source/` و `locales/` دارد به فرمان بدهید. مقصد، پوشه‌ای است که باید زیرپوشه‌های زبان منطقه‌ای را در بر بگیرد. در اسکریپت‌ها `--target` را بدهید. در اجرای تعاملی، اگر آن را ننویسید ابزار مقصد را می‌پرسد.

- `php.json` و `json.json` به `default.json` تبدیل می‌شوند. کلیدهای جدید همان عبارت‌های اصلی منبع هستند.
- `php-inline.json` و `json-inline.json` برای عبارت‌های منبعِ دارای `:attribute` به `inline.json` تبدیل می‌شوند.
- مدخل‌های `_excludes.json` به بخش `excludes` در فایل `_settings.json` زبان منطقه‌ای مقصد منتقل می‌شوند.

Toolkit فایل‌های قدیمی را حفظ و محتوا را در واژه‌نامه‌های مقصد ادغام می‌کند. اگر کلیدی تکراری باشد، ترجمهٔ منتقل‌شده اولویت دارد. اگر هر دو قالب قدیمی برای عبارت یکسان ترجمه داشته باشند، واژه‌نامهٔ PHP اولویت دارد. پیش از جایگزینی یک واژه‌نامهٔ فعال، از مقصد جداگانه‌ای برای بازبینی مهاجرت استفاده کنید. برای تغییرات برنامه یا مانیفست افزونه، [راهنمای ارتقا](./upgrade.md) را ببینید.

## وارد کردن منابع از بایگانی ZIP {#import-sources-from-a-zip-archive}

```bash
php src/Toolkit/bin/toolkit download \
  --url=https://github.com/laravel/framework/archive/refs/heads/12.x.zip \
  --directory=workbench/downloads/framework/12.x \
  --paths=src/Illuminate/Translation/lang/en \
  --only-copy
```

- `--directory` مقصد نهایی را تعیین می‌کند. برای چند پوشه نسبت به ریشهٔ بایگانی می‌توانید `--paths` را تکرار کنید.
- فایل‌های انتخاب‌شدهٔ PHP و JSON با همان نام فایل به مقصد کپی می‌شوند. فایل‌های PHP بایت‌به‌بایت کپی می‌شوند. فایل‌های JSON هم‌نام ادغام می‌شوند و مقادیر ورودی اولویت دارند.
- بدون `--only-copy`، Toolkit فراخوانی‌های ترجمهٔ پشتیبانی‌شده را با کلیدهای لفظی جست‌وجو می‌کند. اگر عبارتی بیابد، منبع JSON را با نام پوشهٔ والد مقصد می‌نویسد، مانند `framework.json` در نمونه.
- فرمان به افزونهٔ ZIP در PHP نیاز دارد. تغییرات را پیش از انتشار آماده می‌کند و اگر انتشار ناموفق بود، برای بازیابی وضعیت پیشین مقصد تلاش می‌کند.
