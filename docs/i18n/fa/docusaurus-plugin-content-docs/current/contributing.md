---
title: مشارکت
description: "در نسخهٔ محلی Mona روی ترجمه‌ها، بسته‌های PHP و مستندات کار کنید."
---

از [مخزن Mona](https://github.com/Laravel-Lang/mona) یک فورک بسازید و درخواست ادغام را برای شاخهٔ `main` آن باز کنید. Mona الگوهای منبع، ترجمه‌های مشترک، بسته‌های PHP و مستندات را نگه می‌دارد. محتوای مخازن بسته‌ها از Mona صادر می‌شود.

## اصلاح یا افزودن ترجمه {#fix-or-add-a-translation}

1. عبارت انگلیسی را در پوشهٔ `src/<Package>/source/` بسته پیدا کنید. برای نمونه، منابع Laravel Framework و بسته‌های رسمی Laravel در `src/Lang/source/` قرار دارند. مانیفست `laravel_lang.json` بسته، فایل‌های منبع را برای هر نسخهٔ بستهٔ بالادستی فهرست می‌کند.
2. فایل `src/Library/locales/<locale>/default.json` را باز کنید. عبارت انگلیسی را به‌عنوان کلید JSON پیدا کنید و مقدارش را ویرایش کنید. اگر عبارت در منبع بسته هست و در واژه‌نامه وجود ندارد، مدخلی برای آن اضافه کنید.
3. عبارت منبع را دست‌نخورده نگه دارید و جای‌نگهدارهایی مانند `:count`، `:other` و `:attribute` را حفظ کنید. برای پیام اعتبارسنجی درون‌خطی، همان کلید را در `inline.json` با عبارتی به‌کار ببرید که نام فیلد در آن نیاید. جای‌نگهدارهای دیگر و صورت‌های جمع را حفظ کنید.
4. اگر ترجمه عمداً با عبارت انگلیسی یکسان است، آن عبارت را در بخش `excludes` فایل `_settings.json` همان زبان منطقه‌ای اضافه کنید. Toolkit در گزارش وضعیت ترجمه از این فهرست استفاده می‌کند.
5. درخواست ادغامی باز کنید که کد زبان منطقه‌ای در عنوانش باشد، مانند `[fr]: Updated validation messages`.

اگر غلط املایی در عبارت منبعِ یک پروژهٔ بالادستی پیدا کردید، آن را به همان پروژه گزارش کنید تا عبارت منبع و کلید جست‌وجوی آن با هم به‌روز شوند.

برای نمونه، مدخل پیش‌فرض فرانسوی در `src/Library/locales/fr/default.json` به این صورت است.

```json
{
  "The :attribute field is invalid.": "Le champ :attribute est invalide."
}
```

مدخل درون‌خطی متناظر در `src/Library/locales/fr/inline.json` قرار دارد.

```json
{
  "The :attribute field is invalid.": "Ce champ est invalide."
}
```

برای ترجمه‌های منتشرشدهٔ Mona، فایل‌های `default.json` و `inline.json` را در Library تغییر دهید. برخی بسته‌ها هنوز واژه‌نامه‌های قدیمی `php.json` و `json.json` را دارند که گردش‌کار خودکار وضعیت بسته نیز از آن‌ها استفاده می‌کند.

برای به‌روزرسانی نام کشورها، ارزها یا زبان‌های منطقه‌ای، واژه‌نامهٔ پوشهٔ `data/` بستهٔ مربوط را ویرایش کنید. بخش [زبان‌های منطقه‌ای و نام‌های ترجمه‌شده](./locales.md) را ببینید.

## پیشنهاد زبان منطقه‌ای {#propose-a-locale}

[کدهای استاندارد زبان‌های منطقه‌ای](./available-locales.md) را بررسی کنید. اگر کد در فهرست هست، واژه‌نامهٔ آن را در `src/Library/locales/` اضافه یا به‌روز کنید. Toolkit می‌تواند [پوشه‌های موجود‌نبوده](./toolkit.md#manage-locale-directories) را بسازد. برای پیشنهاد کد جدید، یک [بحث در Laravel-Lang](https://github.com/orgs/Laravel-Lang/discussions/new?category=ideas&title=I%20want%20to%20add%20a%20new%20localization) باز کنید.

## توسعهٔ بسته‌ها {#develop-packages}

از Git، Composer 2 و PHP با نسخهٔ `^8.3` و افزونه‌های `intl`، `json` و `zip` استفاده کنید. برای آزمون‌ها که پایگاه دادهٔ درون‌حافظه‌ای دارند، `pdo_sqlite` را فعال کنید. دستورها را از ریشهٔ مخزن اجرا کنید.

```bash
composer install
composer check-platform-reqs
```

پروژهٔ Composer در ریشه، کد `src/<Package>/src/` را به‌صورت خودکار بارگذاری می‌کند. وابستگی‌های هر بستهٔ منتشرشده را در `src/<Package>/composer.json` همان بسته نگه دارید. قید `self.version` امکان نصب هم‌زمان بسته‌های یک انتشار را فراهم می‌کند.

برای آزمایش محلی رفتار بسته‌ها از برنامهٔ Testbench در `workbench/` استفاده کنید. `composer serve` برنامه را می‌سازد و سرور توسعه را راه می‌اندازد. دستورهای زیر مسیرها و فرمان‌های Artisan آن را نشان می‌دهند.

```bash
composer serve
```

```bash
php vendor/bin/testbench route:list
php vendor/bin/testbench help lang:update
```

گردش‌کار CI وابستگی‌ها هر بسته را جداگانه با مخزن Composer از نوع `path` نصب می‌کند که به سایر بسته‌های نسخهٔ محلی اشاره دارد. پس از تغییر وابستگی‌ها، هم نصب جداگانهٔ بسته و هم آزمون‌های مخزن را بررسی کنید. تنظیمات و نسخه‌های Laravel در گردش‌کار آمده‌اند. [گردش‌کار آزمون وابستگی‌ها](https://github.com/Laravel-Lang/mona/blob/main/.github/workflows/test-dependencies.yml)

## اجرای بررسی‌های محلی {#run-local-checks}

پس از تغییر واژه‌نامه، وابستگی‌های JavaScript را در ریشه نصب کنید و بررسی‌های Biome را اجرا کنید. دستور `status` مدخل‌هایی را گزارش می‌کند که مقدارشان با کلید منبع یکسان است. برای یافتن عبارت‌های غایب از واژه‌نامه، فایل‌های منبع بسته را بررسی کنید. گزارش‌های تولیدشده را با `git status --short` ببینید. [دستورهای Toolkit](./toolkit.md)

```bash
npm install
npx biome check src/Library/locales/fr/default.json
php src/Toolkit/bin/toolkit status src/Library
```

برای تغییرات PHP، Pest و PHPStan را از ریشهٔ مخزن اجرا کنید. آزمون‌های قابلیت‌ها را مانند CI به‌صورت ترتیبی اجرا کنید، چون آزمون‌های تولید مدل از پوشهٔ مشترک Testbench استفاده می‌کنند. آزمون‌های معماری را می‌توانید موازی اجرا کنید. برای آزمودن یک قابلیت، فایل یا پوشهٔ آن را به Pest بدهید.

```bash
php vendor/bin/pest --exclude-group=arch
php vendor/bin/pest --group=arch --parallel
composer test:types
```

اگر آزمون‌های مسیر به دلیل تنظیمات توسعهٔ Testbench نشانی‌هایی با `http://localhost:8000` تولید می‌کنند، پیش از اجرای Pest مقدار `APP_URL` را در فرایند آزمون روی `http://localhost` بگذارید.

دستور `composer style` فایل‌های Composer را یکدست می‌کند و اصلاحات Rector و Pint را اعمال می‌کند. پیش از فرستادن درخواست ادغام، تغییرات را بررسی کنید. برای سنجش پوشش خطوط، Xdebug را فعال کنید و `composer run-script --timeout=0 test:coverage` را اجرا کنید.

برای تغییر مانیفست، همان بررسی شِما را اجرا کنید که CI به کار می‌برد.

```bash
npx --yes ajv-cli@5.0.0 validate --spec=draft2020 -s resources/laravel-lang.schema.json -d 'src/*/laravel_lang.json'
```

برای قالب مانیفست به [افزونه‌های ترجمه](./plugins.md) مراجعه کنید.

## ویرایش مستندات {#edit-the-documentation}

صفحه‌های انگلیسی نسخهٔ فعلی در `docs/docs/` و ترجمه‌ها در `docs/i18n/<language>/docusaurus-plugin-content-docs/current/` هستند. پس از تغییر دستورالعمل‌ها، صفحه‌های متناظر را به‌روز کنید و شناسهٔ عنوان‌ها را برای پیوندهای بین زبان‌ها حفظ کنید. آرشیو پیش از Mona در `docs/versioned_docs/version-legacy/` است و ترجمه‌هایش در پوشه‌های متناظر `version-legacy/` زیر `docs/i18n/` قرار دارند.

از نسخهٔ Node.js مشخص‌شده در `docs/package.json` استفاده کنید. دستورهای مستندات را از `docs/` اجرا کنید.

```bash
cd docs
npm ci
npm start
```

برای پیش‌نمایش یک ترجمه، `npm start -- --locale fr` را اجرا کنید. سرور توسعه را متوقف کنید و ساخت نهایی را اجرا کنید تا پیوندها، شناسهٔ عنوان‌ها و نمایهٔ جست‌وجو در همهٔ زبان‌ها بررسی شوند.

```bash
npm run typecheck
npm run build
npm run serve
```

نشانی چاپ‌شده توسط `serve` را باز کنید و جست‌وجو و خروجی‌های Markdown را بررسی کنید. ساخت، سایت ایستا را در `docs/build/` می‌نویسد.

## انتشار نسخه {#publish-a-release}

ادغام در `main`، زیرشاخهٔ هر بسته را به شاخهٔ `main` مخزن خودش صادر و پیش‌نویس انتشار Mona را به‌روز می‌کند. پیش‌نویس را بازبینی و انتشار را در Mona با برچسب نسخهٔ مشترک منتشر کنید. ایجاد برچسب به‌تنهایی گردش‌کارهای انتشار را آغاز نمی‌کند.

انتشار نسخه باعث صدور برچسب و ساخت انتشار متناظر در هر مخزن بسته می‌شود. ساخت و استقرار سایت مستندات نیز آغاز می‌شود. پس از انتشار هر دو گردش‌کار را بررسی کنید. برای پیش‌نمایش تفکیک بسته‌ها، **Export Packages** را از `main` با `dry-run` فعال به‌صورت دستی اجرا کنید. [صدور بسته‌ها](https://github.com/Laravel-Lang/mona/actions/workflows/export-packages.yml) · [مستندات](https://github.com/Laravel-Lang/mona/actions/workflows/docs.yml)
