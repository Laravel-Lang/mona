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
php src/Toolkit/bin/toolkit status src/Lang
```

فرمان به‌طور پیش‌فرض فایل `locales/<locale>/_status.json` را در پروژه می‌نویسد. مدخل‌هایی را فهرست می‌کند که ترجمهٔ ذخیره‌شدهٔ آن‌ها با عبارت منبع یکی است و آن‌ها را بر اساس سبک گروه‌بندی می‌کند. استثناهای `locales/_settings.json` و `locales/<locale>/_settings.json` را ترکیب و گزارش‌های خالی را حذف می‌کند. برای یافتن عبارت‌هایی که اصلاً در واژه‌نامه نیستند، آن را با فایل‌های منبع بسته مقایسه کنید.

برای نوشتن گزارش‌های `<locale>.json` در پوشه‌ای جداگانه، از گزینهٔ `--output-dir` استفاده کنید. فرمان پوشهٔ کاری فعلی را مبنای مسیرهای نسبی قرار می‌دهد و وقتی گزارشی برای نوشتن وجود داشته باشد، پوشهٔ مقصد را می‌سازد.

```bash
php src/Toolkit/bin/toolkit status src/Library --output-dir=workbench/status
```

عبارت‌هایی را که عمداً یکسان مانده‌اند در `excludes` پروندهٔ `_settings.json` مربوط اضافه کنید.

```json
{
  "excludes": ["Laravel"]
}
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

بدون آرگومان، `sync:library` از پوشهٔ `src/` مخزن و `sync:package` از پوشهٔ کاری کنونی استفاده می‌کند. هر دو پوشه‌های دارای کد معیار زبان را پردازش می‌کنند.

## مهاجرت واژه‌نامه‌های قدیمی {#migrate-legacy-dictionaries}

```bash
php src/Toolkit/bin/toolkit migrate-locales path/to/legacy-pack --target=path/to/new-pack/locales
```

بسته‌ای قدیمی را که `source/` و `locales/` دارد به فرمان بدهید. با `--target` پوشه‌ای را مشخص کنید که زیرپوشه‌های زبان منطقه‌ای در آن قرار می‌گیرند. اگر این گزینه را ننویسید، فرمان نتیجه را در پوشهٔ `locales/` پروژهٔ قدیمی می‌نویسد.

- `json.json` و `json-inline.json` به‌ترتیب به `default.json` و `inline.json` منتقل می‌شوند. عبارت‌های منبع که کلیدهای این فایل‌ها هستند حفظ می‌شوند.
- `php.json` و `php-inline.json` به‌ترتیب به `default.json` و `inline.json` منتقل می‌شوند. فرمان با استفاده از فایل‌های PHP در `source/` کلیدهای تودرتو را به عبارت‌های متناظر منبع تبدیل می‌کند.
- مدخل‌های `_excludes.json` به بخش `excludes` در فایل `_settings.json` زبان منطقه‌ای مقصد منتقل می‌شوند.

Toolkit ترجمه‌ها را در واژه‌نامه‌های مقصد ادغام می‌کند. اگر کلیدی تکراری باشد، ترجمهٔ منتقل‌شده اولویت دارد. اگر هر دو قالب قدیمی برای عبارت یکسان ترجمه داشته باشند، واژه‌نامهٔ PHP اولویت دارد.

Toolkit فایل‌های `source/` را نگه می‌دارد. واژه‌نامه‌های قدیمی و فایل‌های موارد استثنا که با موفقیت منتقل شده‌اند، حتی در صورت استفاده از مقصد جداگانه حذف می‌شوند. برای تغییرات برنامه یا مانیفست افزونه، [راهنمای ارتقا](./upgrade.md) را ببینید.

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

گزینه‌های `--url` و `--directory` ضروری‌اند. پوشه‌های `--paths` همراه زیرفهرست‌هایشان پیمایش می‌شوند. برای جست‌وجوی صرفِ کلیدهای ترجمه `--paths` را ندهید. کلیدهای یافت‌شده را پیش از افزودن به بسته بررسی کنید.
