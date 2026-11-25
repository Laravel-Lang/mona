---
title: ارتقا به Mona
description: وابستگی‌ها، پیکربندی، ترجمه‌های منتشرشده و یکپارچه‌سازی‌های Laravel-Lang را ارتقا دهید.
---

Mona مخزنی است که بسته‌های Laravel-Lang را با هم منتشر می‌کند. همچنان از بسته‌های جداگانهٔ Composer که برنامه نیاز دارد استفاده کنید.

:::info[چرا نسخهٔ 20؟]

اکوسیستم Laravel-Lang از پروژهٔ `laravel-lang/lang` شکل گرفت. بسته‌های دیگر در زمان‌های متفاوتی ایجاد شدند و هرکدام نسخه‌بندی و انتشار مستقلی داشتند. ارتقا به نسخه‌های اصلی جدید برای نگه‌دارندگان به هماهنگی نیاز داشت و کاربران باید بررسی می‌کردند کدام نسخه‌ها را می‌توانند کنار هم نصب کنند.

برای ساده‌تر شدن این کار، `laravel-lang/common` را ساختیم تا مجموعه‌ای از وابستگی‌های سازگار را مشخص کند. با این حال، یک ارتقا همچنان می‌توانست چند بسته با نسخه‌بندی مستقل را درگیر کند.

پیش از Mona، اگر برنامه‌ای با Laravel 11 و PHP 8.2 داشتید، کدام نسخه از هر بسته را نصب می‌کردید؟

<details>
<summary>نسخه‌های بسته‌ها پیش از Mona</summary>

| بسته | نسخه |
| --- | --- |
| Common | `6.9.0` |
| Actions | `1.13.2` |
| Attributes | `2.16.6` |
| Lang | `15.39.2` |
| MoonShine | `1.9.3` |
| HTTP Statuses | `3.13.2` |
| Currency Names | `1.11.0` |
| Locale Names | `2.10.0` |
| Country Names | `1.8.0` |
| Starter Kits | `1.17.4` |
| Locales | `2.11.0` |
| Models | `1.8.0` |
| Routes | `1.11.0` |
| JSON Fallback | `2.3.0` |
| Locale List | `1.7.0` |
| Config | `1.17.1` (نسخه‌ای از سری 1.x که بسته‌های دیگر Laravel-Lang همچنان از آن استفاده می‌کردند) و `2.3.2` (آخرین نسخهٔ پایدار پیش از Mona که آن بسته‌ها به‌ندرت از آن استفاده می‌کردند) |
| Publisher | `16.9.0` |

</details>

در Mona همهٔ بسته‌ها را با یک شمارهٔ نسخه و هم‌زمان منتشر می‌کنیم. Publisher بالاترین شمارهٔ نسخه را داشت و اگر شماره‌گذاری آن را ادامه می‌دادیم، نسخهٔ اصلی بعدی `17.0` می‌شد. برای گردآوردن این اکوسیستم در یک مخزن زحمت زیادی کشیدیم و عدد 20 را برای این نقطهٔ عطف انتخاب کردیم. می‌توانید آن را **Laravel-Lang 2.0** هم بخوانید. 🙂

:::

:::info[کدها و نام‌های مستعار زبان‌های منطقه‌ای]

Laravel-Lang اکنون مقادیر ذخیره‌شده، از جمله مسیرهای فایل زبان منتشرشده و ردیف‌های ترجمهٔ مدل، را با کد استاندارد زبان منطقه‌ای می‌نویسد. زبان‌های منطقه‌ای فعال و جایگزینِ Laravel نیز از کد استاندارد استفاده می‌کنند. هنگام اجرا، برای قطعه‌های URL و انتخابگر زبان از نام‌های مستعار استفاده کنید. متدهای نما که کد زبان منطقه‌ای برمی‌گردانند، به‌طور پیش‌فرض نام مستعار تنظیم‌شده و در صورت نبود آن کد استاندارد را برمی‌گردانند. برای نمونه، برای حفظ `/pt-br/account`، انتشار در `lang/pt_BR.json` و ذخیرهٔ `pt_BR` در پایگاه داده، `'pt_BR' => 'pt-br'` را در `localization_main.aliases` تنظیم کنید.

:::

## به‌روزرسانی وابستگی‌ها {#updating-dependencies}

نسخهٔ فعلی از Laravel 12 و 13 پشتیبانی می‌کند و به PHP `^8.3` نیاز دارد. در `composer.json` برنامه، قید نسخه را برای هر یک از بسته‌های زیر که در `require` یا `require-dev` است روی `^20.0` تنظیم کنید. اگر فقط `laravel-lang/common` را نصب کرده‌اید، همان یک وابستگی را تغییر دهید.

پیش از نصب `20.x` یا ارتقا به آن، بررسی کنید که نسخهٔ بستهٔ Composer مورد نیازتان در Packagist موجود باشد. نسخهٔ Mona و بسته‌های Composer متناظر ممکن است در زمان‌های متفاوتی در دسترس قرار بگیرند.

| کاربرد | بسته‌های Composer |
| --- | --- |
| مجموعهٔ بسته‌ها | `laravel-lang/common` |
| بسته‌های ترجمه | `laravel-lang/lang`، `laravel-lang/starter-kits`، `laravel-lang/moonshine`، `laravel-lang/actions`، `laravel-lang/attributes`، `laravel-lang/http-statuses` |
| انتشار و پیکربندی | `laravel-lang/publisher`، `laravel-lang/config`، `laravel-lang/json-fallback`, `laravel-lang/library` |
| یکپارچه‌سازی با برنامه | `laravel-lang/locales`، `laravel-lang/locale-list`، `laravel-lang/routes`، `laravel-lang/models` |
| ابزارهای مشترک | `laravel-lang/exceptions`, `laravel-lang/filesystem` |
| الگوی بسته | `laravel-lang/translations-template` |

اگر نصب شما تنها از رویهٔ استاندارد ترجمه استفاده می‌کند، وابستگی‌ها را به‌روز و ترجمه‌ها را دوباره منتشر کنید.

```bash
composer update
php artisan lang:update
```

اگر ارائه‌دهنده ثبت کرده‌اید، تنظیمات سفارشی منتشر کرده‌اید، از نام مستعار یا بستهٔ ترجمهٔ شخص ثالث استفاده می‌کنید یا API بسته‌ها را در برنامه فرا می‌خوانید، پیش از انتشار دوباره بخش‌های مربوط را دنبال کنید.

## جایگزینی وابستگی‌های تغییرنام‌یافته یا منسوخ {#replace-renamed-or-retired-dependencies}

پیش از اجرای `composer update`، وابستگی‌های مستقیم به بسته‌های قدیمی نام‌ها را در `composer.json` به‌روز کنید.

| بستهٔ پیشین Composer | وابستگی جدید با قید `^20.0` |
| --- | --- |
| `laravel-lang/native-country-names` | `laravel-lang/country-names` |
| `laravel-lang/native-currency-names` | `laravel-lang/currency-names` |
| `laravel-lang/native-locale-names` | `laravel-lang/locale-names` |

Common از پیش به هر سه بستهٔ نام‌ها وابسته است. کد PHP خود را هم به‌روز کنید. [جایگزینی APIهای جست‌وجوی نام‌ها](#replace-the-name-lookup-apis) را ببینید.

برای هر افزونهٔ شخص ثالث، انتشار سازگار با Publisher جدید را پیدا کنید. پیش از حذف وابستگی‌های `laravel-lang/translator` یا `laravel-lang/status-generator`، کاربردهای آن‌ها را مهاجرت دهید. [ابزارهای ترجمه](#update-json-fallback-and-translation-tooling) را ببینید.

## به‌روزرسانی ارائه‌دهندگان سرویس {#update-service-providers}

Laravel ارائه‌دهندگان Config، Publisher، Routes و Models را از مانیفست بسته‌ها شناسایی می‌کند. اگر به شناسایی خودکار متکی هستید، کد یا آزمون‌هایی را که به کلاس‌های پیشین اشاره دارند به‌روز کنید. اگر برنامه بسته‌ها را دستی ثبت می‌کند، در همان فهرست ارائه‌دهندگان، مانند `bootstrap/providers.php` یا `config/app.php`، این جایگزینی‌ها را انجام دهید.

- `LaravelLang\Config\ServiceProvider` → `LaravelLang\Config\LangConfigServiceProvider`
- `LaravelLang\Publisher\ServiceProvider` → `LaravelLang\Publisher\LangPublisherServiceProvider`
- `LaravelLang\Routes\ServiceProvider` → `LaravelLang\Routes\LangRoutesServiceProvider`
- `LaravelLang\Models\ServiceProvider` → `LaravelLang\Models\LangModelsServiceProvider`

ثبت دستی `LaravelLang\Locales\LaravelLangLocalesServiceProvider` و کلاس‌های قدیمی `ServiceProvider` در `LaravelLang\Actions`، `LaravelLang\Attributes`، `LaravelLang\HttpStatuses`، `LaravelLang\Lang`، `LaravelLang\MoonShine` و `LaravelLang\StarterKits` را حذف کنید. این بسته‌های ترجمه از مانیفست‌هایشان شناسایی می‌شوند.

اگر از ترجمهٔ جایگزین JSON استفاده می‌کنید، `LaravelLang\JsonFallback\TranslationServiceProvider` را در محل ثبت با `LaravelLang\JsonFallback\LangJsonFallbackServiceProvider` جایگزین کنید. این کار جایگزینیِ ارائه‌دهندهٔ ترجمهٔ Laravel را فعال نگه می‌دارد.

پیش از `composer update` ارجاع‌های قدیمی به ارائه‌دهندگان را به‌روز کنید تا شناسایی بسته‌ها بتواند برنامه را راه‌اندازی کند. اگر اسکریپت Composer همچنان به‌علت بارگذاری کلاس قدیمی خطا می‌دهد، `composer update --no-scripts` را اجرا کنید، ارجاع‌ها را به‌روز کنید و با `composer dump-autoload` شناسایی را دوباره اجرا کنید.

## مهاجرت پیکربندی {#migrate-configuration}

اگر `config/localization.php` را سفارشی کرده‌اید، هنگام انتقال تغییرات آن را برای مراجعه نگه دارید. بسته‌ها پیش‌فرض‌های جدید را خودکار بارگذاری می‌کنند. [فایل‌های پیکربندی](./configure.md) مورد نیاز برای ویرایش را منتشر کنید. برچسب `localization` هر پنج فایل را منتشر می‌کند. برای انتشار یک فایل از نام آن بدون `.php` به‌عنوان برچسب استفاده کنید.

```bash
php artisan vendor:publish --tag=localization
```

### نام‌های مستعار، سبک‌های ترجمه و قالب‌بندی فایل {#aliases-translation-styles-and-file-formatting}

- نگاشت `aliases` را به `config/localization_main.php` کپی کنید. کد استاندارد را به‌عنوان کلید و نام مستعار را به‌عنوان مقدار نگه دارید، مانند `'de_CH' => 'de-CH'`.
- در `config/localization_main.php`، `inline` را با `styles` جایگزین کنید. برای حفظ پیش‌فرض پیشین با پیام‌های استاندارد اعتبارسنجی، `'styles' => ['default']` را تنظیم کنید. اگر از پیام‌های درون‌خطی استفاده می‌کردید، `['default', 'inline']` را که پیش‌فرض جدید است نگه دارید. `LOCALIZATION_INLINE` و `LANG_PUBLISHER_INLINE` را از محیط‌هایی که در آن‌ها تنظیم کرده‌اید حذف کنید.
- کلید `align` و متغیرهای محیطی `LOCALIZATION_ALIGN` و `LANG_PUBLISHER_ALIGN` را حذف کنید. برای هم‌تراز کردن عملگرهای `=>` در آرایه‌های PHP منتشرشده، قالب‌بند کد PHP پروژه را تنظیم کنید و پس از انتشار روی فایل‌های زبان اجرا کنید. برای نمونه، قاعدهٔ `binary_operator_spaces` در Laravel Pint می‌تواند `align_single_space_minimal` را روی `=>` اعمال کند.

### نشانه‌گذاری هوشمند {#smart-punctuation}

اگر نشانه‌گذاری هوشمند را فعال کرده بودید، `LOCALIZATION_SMART_ENABLED` را به `LOCALIZATION_PUNCTUATION_ENABLED` تغییر نام دهید و مقدار آن را `true` نگه دارید. اگر `smart_punctuation.enable` را مستقیم در PHP تنظیم کرده‌اید، مقدار بولی را به کلید سطح بالای `enabled` در `config/localization_punctuation.php` منتقل کنید. نشانه‌گذاری به‌طور پیش‌فرض غیرفعال است.

گیومه‌های سفارشی را از `smart_punctuation.common` و `smart_punctuation.locales` به آرایه‌های سطح بالای `common` و `locales` در همان فایل ببرید. کلیدهای `locales` را کدهای استاندارد قرار دهید. گیومهٔ هر زبان منطقه‌ای کلید متناظر در `common` را بازنویسی می‌کند. اگر از نمونه‌ای قدیمی برای فرانسوی با `&nbsp;` استفاده کرده‌اید، در رشته‌های گیومه یک فاصلهٔ نشکن واقعی یا `"\u{00A0}"` به کار ببرید.

### مسیرهای بومی‌شده {#localized-routes}

تغییرات سفارشی `routes` در پیکربندی قدیمی را به `config/localization_routes.php` منتقل کنید.

| بخش سفارشی‌شده | تغییر در `config/localization_routes.php` | متغیر محیطی برای تغییر نام |
| --- | --- | --- |
| پارامتر زبان منطقه‌ای، سرآیند، کوکی، کلید نشست یا ستون مدل کاربر | تغییرات `names` را کپی و باقی مدخل‌های پیش‌فرض را حفظ کنید. | |
| پیشوند نام مسیرهای بومی‌شده | `name_prefix` را به `prefix` منتقل کنید. اگر مقدار خالی باشد، از `localized.` استفاده می‌شود. | `LOCALIZATION_NAME_PREFIX` → `LOCALIZATION_PREFIX` |
| هدایت از مسیر پیشونددار | `redirect_default` را به `redirect` منتقل کنید. | `LOCALIZATION_REDIRECT_DEFAULT` → `LOCALIZATION_REDIRECT` |
| پنهان کردن زبان منطقه‌ای جایگزین توسط تابع کمکی URL | `hide_default` را به `hide` منتقل کنید. | `LOCALIZATION_HIDE_DEFAULT` → `LOCALIZATION_HIDE` |
| میان‌افزار اعمال‌شده بر گروه‌های مسیر بومی‌شده | `group.middlewares.default` را برای مسیرهای بدون پیشوند به `middlewares.default` و `group.middlewares.prefix` را برای مسیرهای پیشونددار به `middlewares.prefix` منتقل کنید. نام کلاس‌های میان‌افزار را طبق [مهاجرت مسیرها](#update-localized-routes-and-middleware) به‌روز کنید. | |

### ترجمه‌های مدل {#model-translations}

تغییرات سفارشی `models` را به `config/localization_models.php` ببرید. مقادیر سفارشی `suffix` و `helpers` را به سطح بالا کپی کنید. به‌جای کلید قدیمی `filter.enabled` از `filter` بولی استفاده کنید. اگر فیلتر و مسیر فایل کمکی IDE را از طریق محیط تنظیم می‌کنید، `LOCALIZATION_FILTER_ENABLED` و `VENDOR_PATH` را حفظ کنید.

فرمان‌های مدل اکنون به‌طور پیش‌فرض `app/Models` را بررسی می‌کنند. اگر به بررسی پیشین تمام `app/` متکی بودید یا `localization-private.models.directory` را خودتان تنظیم کرده‌اید، هر پوشهٔ مورد نظر را در آرایهٔ `directories` بگذارید. برای نمونه، برای حفظ بررسی پیشین `'directories' => [app_path()]` را تنظیم کنید.

### پیکربندی مورد استفادهٔ کد برنامه {#configuration-used-by-application-code}

- خواندن و نوشتن از طریق `LaravelLang\Config\Facades\Config` را با فراخوانی‌های پیکربندی Laravel برای کلیدهای جدید جایگزین کنید. برای نمونه، به‌جای `Config::shared()->models->filter->enabled` از `config('localization_models.filter')` استفاده کنید. اگر خواندن نوع‌دار را ترجیح می‌دهید، `LaravelLang\Config\Services\ModelLangConfig` را تزریق کنید و ویژگی `filter` آن را بخوانید. برای تنظیمات مسیر هم می‌توانید `RouteLangConfig` را از همان فضای نام تزریق کنید.
- اگر کد فرادادهٔ زبان منطقه‌ای را از `localization-private.map` می‌خواند، به‌جای آن `localization_map.locales` را بخوانید. مقادیر سفارشی را به آرایهٔ `locales` در `config/localization_map.php` کپی کنید.
- ثبت از طریق `localization-private.plugins` و `localization-private.packages` را با [مانیفست افزونه](./plugins.md) جایگزین کنید.
- بلوک قدیمی `translators` و هر تنظیم قدیمی `TRANSLATION_*` را که فقط `laravel-lang/translator` استفاده می‌کرد حذف کنید.

پس از انتقال مقادیر سفارشی و به‌روزرسانی ارجاع‌های برنامه، `config/localization.php` را حذف و `php artisan config:clear` را اجرا کنید.

## انتقال فایل‌های زبان به مسیرهای استاندارد {#move-language-files-to-canonical-paths}

1. `APP_LOCALE` و `APP_FALLBACK_LOCALE` را روی کدهای استاندارد تنظیم کنید. نام‌های مستعار را به `localization_main.aliases` منتقل کنید تا URLها و ورودی‌های زبان منطقه‌ای همچنان کار کنند.
2. نام فایل‌ها و پوشه‌های مبتنی بر نام مستعار، مانند `lang/pt-br.json` و `lang/pt-br/`، را به `lang/pt_BR.json` و `lang/pt_BR/` تغییر دهید. همان نام‌های مستعار را در `lang/vendor/<package>/` هم بررسی کنید. اگر هر دو نام وجود دارند، پیش از حذف مسیر مستعار، محتوا را ادغام و تضاد ترجمه‌های سفارشی را حل کنید.
3. هر زبان منطقه‌ای را که فقط ترجمه‌های وابستهٔ PHP دارد با `php artisan lang:add <locale>` نصب کنید. فهرست نصب‌شده از مدخل‌های مستقیم پوشهٔ زبان، فایل‌های JSON در `lang/vendor/<package>/` و زبان‌های منطقه‌ای جاری و جایگزینِ مؤثر استفاده می‌کند.
4. پس از مهاجرت پیکربندی و افزونه‌ها، `php artisan lang:update` را اجرا و تفاوت‌ها را بازبینی کنید. Publisher می‌تواند کلیدهای یکپارچه‌سازی‌هایی را که دیگر فایل منتشر نمی‌کنند نگه دارد. کلیدهایی را که برنامه همچنان به کار می‌برد حفظ کنید و کلیدهای منسوخ را پس از بررسی فراخوانندگان حذف کنید. برای تولید دوباره یا حفظ فایل‌ها [اسکریپت‌های انتشار و ترجمه‌های سفارشی](#update-publication-scripts-and-keep-custom-translations) را ببینید.

## به‌روزرسانی یکپارچه‌سازی‌ها {#update-integrations}

### Locales {#locales}

دستور import مربوط به `LaravelLang\Locales\Facades\Locales` را حفظ کنید و فراخوانی‌ها را مطابق داده‌های مورد استفادهٔ هر فراخواننده به‌روز کنید.

| فراخوانی پیشین | جایگزین |
| --- | --- |
| `Locales::available()`، `installed()` یا `protects()` که شیء برمی‌گردانند | همان متد را روی `Locales::data()` فراخوانی کنید. |
| `Locales::raw()->available()`، `installed()` یا `protects()` | برای دریافت مجموعهٔ رشته‌های نام مستعار، همان متد را مستقیم روی `Locales` فراخوانی کنید. برای کدهای استاندارد `false` بدهید و اگر فراخواننده آرایه می‌خواهد، `->all()` را اجرا کنید. |
| `Locales::installed(false)` یا `Locales::raw()->installed(false)` | `false` اکنون کد استاندارد را انتخاب می‌کند. زبان‌های منطقه‌ای جاری و جایگزین حتی بدون فایل زبان در مجموعه هستند. آرگومان نام‌دار قدیمی `withProtects: false` را نیز جایگزین کنید. برای حذف آن‌ها از مجموعه از `Locales::installed(false)->diff(Locales::protects(false))->values()` و برای اشیا از `Locales::data()->installed()->reject(fn ($locale) => Locales::isProtected($locale))->values()` استفاده کنید. |
| `Locales::notInstalled()` یا `Locales::raw()->notInstalled()` | برای اشیا `Locales::data()->uninstalled()` و برای رشته‌های نام مستعار `Locales::uninstalled()` را به کار ببرید. برای رشته‌های استاندارد `false` بدهید. |
| `Locales::getCurrent()` یا `Locales::getDefault()` | برای DTO از `Locales::data()->current()` و برای رشته از `Locales::current()` استفاده کنید. `Locales::raw()->getDefault()` را با `Locales::current()` جایگزین کنید. اگر برای خواندن مقدار نگه‌داری‌شده توسط Laravel به `Locales::raw()->getCurrent()` متکی بودید، `app()->getLocale()` را به کار ببرید. |
| `Locales::getFallback()` یا `Locales::raw()->getFallback()` | برای DTO از `Locales::data()->fallback()` و برای رشته از `Locales::fallback()` استفاده کنید. زبان منطقه‌ای جایگزین ناشناخته اکنون به انگلیسی تبدیل می‌شود. زبان منطقه‌ای فعال ناشناخته به جایگزین مؤثر تبدیل می‌شود. |
| `Locales::get($locale)` یا `Locales::raw()->get($locale)` | از `isInstalled()` استفاده و جایگزین را صریح انتخاب کنید. برای زبان منطقه‌ای پشتیبانی‌شده‌ای که ممکن است نصب نباشد، ابتدا `isAvailable()` و سپس `info()` را فراخوانی کنید. |
| `Locales::info($locale)` یا `Locales::raw()->info($locale)` | از `Locales::info($locale)` استفاده کنید و در صورت نیاز به رشته، `->alias` یا `->code` را بخوانید. برای زبان منطقه‌ای جاری `null` بدهید. برای کد ناشناخته یا خالی، `LaravelLang\Exceptions\UnknownLocaleException` را مدیریت کنید یا پیش از جست‌وجو `isAvailable()` را بررسی کنید. |

برای حفظ رفتار قدیمی `get()` که در ورودی نامعتبر یا نصب‌نشده به زبان منطقه‌ای جاری برمی‌گشت، این انتخاب را در کد فراخواننده انجام دهید.

```php
use LaravelLang\Locales\Facades\Locales;

$locale = Locales::acceptsInput($requested) && Locales::isInstalled($requested)
    ? Locales::info($requested)
    : Locales::data()->current();

$code = $locale->code;   // در مسیرها، ردیف‌های پایگاه داده و سایر داده‌های پایدار استفاده کنید.
$alias = $locale->alias; // در URLها و انتخابگرهای زبان استفاده کنید.
```

رسیدگی‌کننده‌های درخواست را که پیش‌تر نتیجهٔ `raw()->get()` را به `app()->setLocale()` می‌دادند به‌روز کنید. پس از پذیرش مقدار درخواست، از `Locales::set($locale)` استفاده کنید. این متد کدهای نصب‌شده، نام‌های مستعار تنظیم‌شده، اعضای `enum` و DTO جدید را می‌پذیرد و کد استاندارد را به Laravel می‌دهد. با مقدار درخواستی `pt-br`، Laravel مقدار `pt_BR` را دریافت می‌کند و `Locales::current()` مقدار `pt-br` را برمی‌گرداند. هر جا به کد استاندارد نیاز دارید، `Locales::current(false)` یا `Locales::fallback(false)` را فراخوانی کنید.

برای سایر موارد ذخیره‌سازی در برنامه، مقدار را با `isAvailable()` یا `isInstalled()` اعتبارسنجی و سپس کد استاندارد آن را با `fromAlias()` پیدا کنید. برای نمایش کد، از `toAlias()` استفاده کنید. `Services\Resolver` قدیمی را با `acceptsInput()` برای بررسی نوع‌های PHP پذیرفته‌شده یا `normalize()` برای استخراج رشته از مقدار نوع‌دار جایگزین کنید. `normalize()` رشته‌ها را همان‌گونه که دریافت می‌کند حفظ می‌کند، از جمله نام مستعار مانند `pt-br`.

در importها، اعلان‌های نوع و آزمون‌ها، `LaravelLang\Locales\Data\LocaleData` را با `LaravelLang\LocaleNames\LocaleData` جایگزین کنید. شیء فقط‌خواندنی جدید ویژگی‌های رشته‌ای `code`، `alias`، `native` و `localized` دارد. در URL و مقدار رابط کاربری، به‌جای ویژگی قدیمی `code` از `alias` استفاده کنید. هر جا پیش‌تر `$data->locale` را می‌خواندید، پس از import کردن `LaravelLang\LocaleList\Locale`، از `Locale::from($data->code)` استفاده کنید. اگر الگوها یا قالب‌بندها از `type`، `regional` و `direction` استفاده می‌کنند، آن‌ها را از `config("localization_map.locales.{$data->code}", [])` بخوانید. برای مدخل‌های غایب پیش‌فرض تعیین کنید. برای حفظ پیش‌فرض‌های قدیمی، `Latn` را برای خط، کد استاندارد را برای منطقه و `Direction::LeftToRight` را برای جهت به کار ببرید.

پرچم‌های `withCountry`، `withCurrency`، `withCountries` و `withCurrencies` را حذف کنید. کشور و ارز را برای ردیف زبان منطقه‌ای صریحاً دریافت کنید. import نوع‌های قدیمی `LaravelLang\Locales\Data\CountryData` و `CurrencyData` را به کلاس‌های داده در بسته‌های متناظر نام‌ها تغییر دهید.

```php
use LaravelLang\CountryNames\CountryNames;
use LaravelLang\CurrencyNames\CurrencyNames;
use LaravelLang\Locales\Facades\Locales;

$locale = Locales::info('pt_BR');
$displayLanguage = Locales::current(false);

$country = app(CountryNames::class)->one($displayLanguage, $locale->code);
$currency = app(CurrencyNames::class)->one($displayLanguage, $locale->code);
```

اگر `localizedLocales()`، `localizedCountries()` یا `localizedCurrencies()` را روی سرویس قدیمی فراخوانی می‌کردید، در سرویس متناظر نام‌ها به‌ترتیب از `all(null)` و `all($displayLanguage)` برای مقادیر بومی و بومی‌شده استفاده کنید. تزریق مستقیم کلاس‌های پیشین `Services\Locales` و `Services\RawLocales` را با نما یا سرویس متناظر `Services\MainService` و `Services\DataService` جایگزین کنید. APIهای جدید سرویس در [زبان‌های منطقه‌ای و نام‌ها](./locales.md) آمده‌اند.

#### متدهای کمکی `enum` زبان منطقه‌ای {#locale-enum-helpers}

اعضای `enum`های `Locale` و `Direction` مقادیرشان را حفظ می‌کنند. هنگام ارتقای فهرست‌ها یا انتخابگرهای سفارشی زبان منطقه‌ای، متدهای کمکی حذف‌شده از `archtechx/enums` را جایگزین کنید.

| متد کمکی پیشین | جایگزین |
| --- | --- |
| `names()` | `array_column(Locale::cases(), 'name')` |
| `options()` | با `array_column(Locale::cases(), 'value', 'name')` نگاشت نام به مقدار را بسازید. |
| `stringOptions()` | گزینه‌ها را در نما از `Locale::cases()` تولید کنید. مقدار عضو را برای کد استاندارد یا `Locales::toAlias($case)` را برای نام مستعار نمایشی به کار ببرید. |
| `fromName()` یا `tryFromName()` | عضو را با `name` در `Locale::cases()` بیابید و نبود نتیجه را مدیریت کنید. برای مقدار `enum` مانند `pt_BR`، از `Locale::from()` یا `Locale::tryFrom()` استفاده کنید. |
| `Locale::onlyValues()` | نگاشت مقدار به مقدار را با `array_column(Locale::cases(), 'value', 'value')` بسازید. برای فهرست مقادیر `Locale::values()` را به کار ببرید. |
| `Direction::values()` | از `array_column(Direction::cases(), 'value')` استفاده کنید. |

### جایگزینی APIهای جست‌وجوی نام {#replace-the-name-lookup-apis}

importهای `LaravelLang\NativeCountryNames`، `LaravelLang\NativeCurrencyNames` و `LaravelLang\NativeLocaleNames` را به `LaravelLang\CountryNames`، `LaravelLang\CurrencyNames` و `LaravelLang\LocaleNames` تغییر دهید. `CountryNames`، `CurrencyNames` یا `LocaleNames` را از ظرف خدمات Laravel دریافت کنید یا سرویس را به کلاس تزریق کنید.

| کاربرد پیشین | جایگزین |
| --- | --- |
| `SomeNames::get($displayLocale)` | `$names->all($displayLocale)` |
| `SomeNames::get()` برای نام‌های بومی | `$names->all(null)` |
| `SortBy::None` | آرگومان مرتب‌سازی را ننویسید. |
| `SortBy::Key` | `->sortKeys()` را روی مجموعهٔ برگشتی اجرا کنید. |
| `SortBy::Value` | برای مرتب‌سازی با نام بومی‌شده، `SortDirection::Ascending` را به `all()` بدهید. |

برای دریافت یک مدخل شناخته‌شده، `one($displayLocale, $entryLocale)` را به کار ببرید. کلید جست‌وجو زبان منطقه‌ای مانند `fr` است. برای نمونه، `app(CountryNames::class)->one('de', 'fr')` کشور متناظر با فرانسوی را با نام آلمانی و `code` برابر `FR` برمی‌گرداند. DTOهای کشور و ارز در فضای نام ریشهٔ بسته‌هایشان قرار دارند و کلید جست‌وجو را در `locale` ارائه می‌کنند. فیلدهای `code` همچنان کد کشور یا ارز را دارند.

هر سه سرویس مجموعه‌هایی با کلید کد استاندارد برمی‌گردانند. اگر از آرایهٔ رشته‌های بستهٔ قدیمی نام زبان‌های منطقه‌ای استفاده می‌کردید، هر DTO را به `localized` نگاشت کنید. اگر روی DTOهای قدیمی کشور یا ارز `toArray()` فراخوانی می‌کردید یا ویژگی‌هایشان را تغییر می‌دادید، آرایهٔ برنامه را صریحاً بسازید. DTOهای جدید فقط‌خواندنی‌اند.

```php
use LaravelLang\LocaleNames\LocaleData;
use LaravelLang\LocaleNames\LocaleNames;

$options = app(LocaleNames::class)
    ->all('de', SortDirection::Ascending)
    ->map(fn (LocaleData $locale): string => $locale->localized)
    ->all();
```

پیش از فراخوانی `one()`، زبان‌های منطقه‌ای انتخاب‌شده توسط کاربر را با فهرست مربوط اعتبارسنجی کنید. زبان نمایش ناشناخته ممکن است باعث شود `all()` فهرست خالی برگرداند. اگر برنامه به نام‌های بومی نیاز دارد `null` بدهید. اگر کد از `enum` مقدار‌دار سفارشی استفاده می‌کرد، مقدار رشته‌ای آن را بدهید. [زبان‌های منطقه‌ای و نام‌ها](./locales.md)

### به‌روزرسانی مسیرهای بومی‌شده و میان‌افزارها {#update-localized-routes-and-middleware}

تابع کمکی دارای فضای نام را در فایل‌های PHP و الگوهای Blade که URL بومی‌شده تولید می‌کنند import کنید. می‌توانید `\LaravelLang\Routes\localizedRoute()` را هم مستقیم فراخوانی کنید.

```php
use LaravelLang\Locales\Facades\Locales;

use function LaravelLang\Routes\localizedRoute;

$url = localizedRoute('products.show', [
    'product' => 42,
    'locale' => Locales::current(),
]);
```

`Route::localizedGroup()` و `LocalizationRoute::group()` هم مسیر بدون پیشوند و هم مسیر با پیشوند زبان منطقه‌ای را ثبت می‌کنند. گروه موجود را نگه دارید و فراخوانی‌های تابع کمکی URL را بازبینی کنید. اگر پارامتر زبان منطقه‌ای حذف شود، از نام مستعارِ جایگزین استفاده می‌کند. برای انتخابگر زبان، نام مستعار دلخواه را صریحاً با `Locales::toAlias($locale)` یا `$localeData->alias` بدهید. رشتهٔ داده‌شدهٔ زبان منطقه‌ای بدون تغییر به کار می‌رود. اگر `localization_routes.names.parameter` را تغییر نام داده‌اید، کلید آرایه را با آن هماهنگ کنید.

importها و ثبت‌ها را در مسیرها، `bootstrap/app.php` و فهرست‌های سفارشی میان‌افزار جایگزین کنید. تمام کلاس‌های این جدول در `LaravelLang\Routes\Middlewares` قرار دارند.

| میان‌افزار پیشین | جایگزین |
| --- | --- |
| `LocalizationByCookie` | `LocalizationByCookieMiddleware` |
| `LocalizationByHeader` | `LocalizationByHeaderMiddleware` |
| `LocalizationByModel` | `LocalizationByModelMiddleware` |
| `LocalizationBySession` | `LocalizationBySessionMiddleware` |
| `LocalizationByParameter` | `LocalizationByParameterMiddleware` |
| `LocalizationByParameterWithRedirect` | `LocalizationByParameterWithRedirectMiddleware` |
| `LocalizationByParameterPrefix` | `LocalizationByParameterPrefixMiddlewareMiddleware` |

اگر میان‌افزار مدل را با guard ساخته بودید، آن را به‌عنوان پارامتر میان‌افزار Laravel بدهید، برای نمونه `LocalizationByModelMiddleware::class . ':admin'`. تعیین‌کنندهٔ کاربر در آن، یک مدل Eloquent می‌خواهد. در زیرکلاس‌های سفارشی میان‌افزار، `LaravelLang\Config\Services\RouteLangConfig` را تزریق کنید، نام منابع را از `$this->routeConfig->name` بخوانید و از `detect()` نوع `int|string|null` را برگردانید. ارجاع مستقیم به `LaravelLang\Routes\Services\Route` را با `LaravelLang\Routes\Services\RouteService` جایگزین کنید.

هر منبع مورد استفادهٔ برنامه را آزمایش کنید. تشخیص از سرآیند با PHP Intl زبان‌های نصب‌شده را به ترتیب ترجیح `Accept-Language` بررسی می‌کند و ابتدا برچسب کامل و سپس زبان پایه را می‌آزماید. میان‌افزار بعدی می‌تواند انتخاب منبع پیشین را بازنویسی کند. برای اینکه URL در مسیرهای پیشونددار تعیین‌کننده باشد، فقط میان‌افزار انتخاب زبان از URL را در زنجیرهٔ آن مسیرها نگه دارید.

تولید URL و درخواست‌ها را جداگانه آزمایش کنید. `localization_routes.hide` هنگام تولید URL، قطعهٔ زبان منطقه‌ای جایگزین را حذف می‌کند. `localization_routes.redirect` درخواست‌های پیشونددارِ زبان منطقه‌ای جاری را به مسیر بدون پیشوند می‌فرستد. مسیر نام‌دار و پیشونددار، زبان منطقه‌ای نامعتبر یا نصب‌نشده را هم به مسیر بدون پیشوند هدایت می‌کند.

در شنونده‌های `LocaleHasBeenSetEvent`، برای کد استاندارد از `$event->locale->code` و برای نمایش از `$event->locale->alias` استفاده کنید. دادهٔ رویداد اکنون `LaravelLang\LocaleNames\LocaleData` است. میان‌افزار در تغییر زبان منطقه‌ای انتخاب‌شده رویداد را می‌فرستد و کد استاندارد را در نشست ذخیره می‌کند. فراخوانی دستی `LocaleHasBeenSetEvent::dispatch($data)` را با `event(new LocaleHasBeenSetEvent($data))` جایگزین کنید. [مسیرهای بومی‌شده](./routes.md)

### مهاجرت کد و داده‌های ترجمهٔ مدل {#migrate-model-translation-code-and-data}

ستون `locale` هر جدول ترجمه را برای ردیف‌های دارای نام مستعار بررسی کنید. برای هر نام مستعار، آن ردیف‌ها را در یک مهاجرت داده به کد استاندارد به‌روز کنید. پیش از به‌روزرسانی بررسی کنید آیا همان `item_id` از پیش ردیفی برای کد استاندارد دارد. برای رعایت قید یکتای `(item_id, locale)`، مقادیری را که می‌خواهید نگه دارید در یک ردیف ادغام کنید. نسخه‌های قدیمی تریت نیز کدهای استاندارد می‌نوشتند، بنابراین مهاجرت را بر اساس مقادیر واقعاً ذخیره‌شده در جدول‌ها انجام دهید.

importها و کلاس‌های تولیدشده را به‌روز کنید.

| کاربرد پیشین | جایگزین |
| --- | --- |
| `LaravelLang\Locales\Data\LocaleData` | `LaravelLang\LocaleNames\LocaleData` |
| `LaravelLang\Models\Casts\TrimCast` | `LaravelLang\Models\Casts\TrimStringCast` |
| `LaravelLang\Models\Exceptions\UnavailableLocaleException` | `LaravelLang\Exceptions\UnknownLocaleException` |
| `LaravelLang\Models\Exceptions\AttributeIsNotTranslatableException` یا `UnknownModelPathException` | همان نام کلاس در `LaravelLang\Exceptions`. |
| `translationModelName()` | `translationModel(): string`. بازنویسی‌هایی را که کلاس ترجمهٔ سفارشی انتخاب می‌کنند به‌روز کنید. |
| `getTranslationTable()` | برای نام کامل ستون از `translationQualifyColumn($column)` و برای نام جدول از `translationsRaw()->getRelated()->getTable()` استفاده کنید. |

`getTranslation()`، `setTranslation()`، `hasTranslated()` و `forgetTranslation()` رشتهٔ زبان منطقه‌ای، `Locale` یا `LocaleData` جدید را می‌پذیرند. هر زبان منطقه‌ای صریح را پیش از فراخوانی این متدها نصب کنید. خواندن بدون تعیین صریح زبان منطقه‌ای، اگر مقدار جاری `null` باشد، از جایگزین استفاده می‌کند. تعیین صریح، مقدار همان زبان را مستقیم می‌خواند. اگر متد سطح پایین‌تر `translation()` را فرا می‌خوانید، رشتهٔ زبان منطقه‌ای یا `null` بدهید. DTO را با `$locale->code` تبدیل کنید.

```php
$article->setTranslation('title', 'Schweizer Titel', 'de-CH')->save();
$article->translation('de_CH');
```

اگر `de_CH` به `de-CH` نگاشت شده و آلمانی سوئیس نصب شده باشد، هر دو فراخوانی به ردیف ذخیره‌شده با `de_CH` اشاره می‌کنند. برای پر کردن چند مقدار یک فیلد ترجمه‌شده، نگاشتی با کلید زبان منطقه‌ای بدهید، مانند `['en' => 'Title', 'de-CH' => 'Schweizer Titel']`. فهرست‌های دارای اندیس عددی اکنون استثنای `InvalidLocaleInputException` ایجاد می‌کنند. کد سفارشی که رابطهٔ بارگذاری‌شدهٔ `translations` را تنظیم می‌کند باید یک مجموعهٔ Laravel از مدل‌های ترجمه بدهد.

فراخوانی `translated()`، `whereTranslation()` و محدوده‌های مرتبط را از طریق سازندهٔ پرس‌وجوی Eloquent انجام دهید. برای رشتهٔ زبان منطقه‌ایِ محدوده، کد یا نام مستعار بدهید. برای `orderByTranslation()`، `'asc'`، `'desc'` یا `SortDirection` را به کار ببرید. هنگام پرس‌وجوی زبانی غیر از جاری یا جایگزین با `whereTranslation()` یا `whereTranslationLike()`، `localization_models.filter` را روی `false` بگذارید یا مستقیم از `translationsRaw()` پرس‌وجو کنید.

مصرف‌کنندگان رویدادی را که `$event->locale->value` را می‌خوانند یا `$event->locale` را با عضوی از `enum` با نام `Locale` مقایسه می‌کنند به‌روز کنید. `TranslationHasBeenSetEvent` و `TranslationHasBeenForgetEvent` اکنون برای زبان منطقه‌ای صریح یک نام مستعار رشته‌ای دارند. اگر فراخواننده زبان منطقه‌ای را مشخص نکند، رویداد تنظیم `null` دارد. پیش از استفاده از نام مستعار به‌عنوان کلید ذخیره‌شده، آن را با `Locales::fromAlias()` تبدیل کنید. رویداد تنظیم هنگام انتساب ویژگی و قبل از `save()` رخ می‌دهد. برای ارسال دستی هر یک از سه رویداد ترجمهٔ مدل از `event(new …)` استفاده کنید.

اگر فایل‌های کمکی تولیدشدهٔ IDE را نگه می‌دارید، پس از به‌روزرسانی importها و مسیرهای شناسایی مدل، `php artisan lang:models-helper` را اجرا کنید. [ترجمه‌های مدل](./models.md)

### به‌روزرسانی اسکریپت‌های انتشار و حفظ ترجمه‌های سفارشی {#update-publication-scripts-and-keep-custom-translations}

| فرمان یا فرض پیشین | مهاجرت |
| --- | --- |
| `lang:add <locale>` فقط زبان‌های در حال افزودن را تازه‌سازی می‌کرد | اکنون همهٔ زبان‌های منطقه‌ای نصب‌شده را تازه‌سازی می‌کند. در اسکریپت‌ها کدها را صریحاً بدهید و به‌روزرسانی فایل‌های موجود را در نظر بگیرید. |
| `lang:reset` | برای ادغام از `lang:update` استفاده کنید. برای ساخت دوباره از ابتدا، از مدخل‌های سفارشی پشتیبان بگیرید، فایل‌های مورد نظر برای تولید دوباره را حذف کنید و سپس `lang:add` را با کدهای صریح زبان‌های مورد نظر اجرا کنید. |
| `lang:rm <locale> --force` | `lang:rm <locale>` را به کار ببرید. زبان‌های منطقه‌ای جاری و جایگزین Laravel محافظت می‌شوند. |
| بررسی صرفاً کد خروج `lang:rm` | هر نتیجه را بررسی کنید. `DONE` حذف را تأیید می‌کند و `FAILED` یعنی مسیری پس از تلاش برای حذف باقی مانده است. `PROTECTED`، `INVALID` و `SKIP` می‌توانند همراه کد خروج موفق باشند. |

اگر Artisan را با کلاس فرمان فرا می‌خوانید، `LaravelLang\Publisher\Console\Add`، `Remove` و `Update` را به `AddCommand`، `RemoveCommand` و `UpdateCommand` تغییر دهید. برای آراستن متن به‌صورت سفارشی، `LaravelLang\Publisher\Contracts\Decorator` را به‌جای `Contracts\TextDecorator` پیاده‌سازی و متصل کنید. متد `convert(string $locale, string $value): string` کد استاندارد زبان منطقه‌ای را دریافت می‌کند.

نخستین `lang:update` را در حالی اجرا کنید که فایل‌های زبان موجود تحت کنترل نسخه باشند. Publisher در مقصدهای موجود ادغام می‌کند و کلیدهای منطبق، از جمله کلیدهای عددی، را جایگزین می‌کند. در صورت امکان ترجمه‌های سفارشی را در فایل ترجمهٔ جداگانهٔ برنامه نگه دارید. برای نگه‌داری دستی کل یک مقصد تولیدشده، الگوی مسیرش را به `localization_main.skip` اضافه کنید. هر مقداری را که به عبارت منبع برگشته بررسی کنید. Publisher در نبود ترجمه در واژه‌نامه‌ها از همان عبارت استفاده می‌کند.

اگر به‌روزرسانی قبلی کلیدهای فایل‌های `http-statuses.php` را دوباره شماره‌گذاری کرده است، از فایل‌های تولیدشدهٔ آسیب‌دیده پشتیبان بگیرید و سپس آن‌ها را حذف کنید. اگر برای دور زدن مشکل `'*/http-statuses.php'` را به `localization_main.skip` اضافه کرده‌اید، آن را بردارید. برای تولید دوبارهٔ فایل‌ها `php artisan lang:update` را اجرا کنید و سپس ترجمه‌های سفارشی را با کدهای وضعیت مربوط بازیابی کنید. [مدیریت ترجمه‌ها](./translations.md)

### جایگزینی ثبت قدیمی افزونه {#replace-legacy-plugin-registration}

اگر بستهٔ ترجمهٔ شخص ثالث دارید، به انتشار مبتنی بر مانیفست آن ارتقا دهید. اگر نگه‌دارندهٔ بسته هستید، ارائه‌دهنده و مدخل شناسایی Composer را که `LaravelLang\Publisher\Plugins\Provider` قدیمی را ثبت می‌کنند حذف کنید. اگر آن ارائه‌دهنده سرویس‌های دیگر Laravel را هم پیکربندی می‌کند، ثبت آن‌ها را در یک ارائه‌دهندهٔ معمولی سرویس Laravel نگه دارید.

1. `laravel_lang.json` را به ریشهٔ بستهٔ نصب‌شده در `vendor/` اضافه کنید. هر مدخل `Plugin::files()` را به مسیر `source` نسبت به پوشهٔ `source/` بسته و مسیر `target` نسبت به پوشهٔ زبان برنامه تبدیل کنید. در مقصد از `{locale}` استفاده کنید.
2. قواعد تطبیق Composer را به مانیفست ببرید. برای وابستگی نصب‌شده از `vendor` و `version` و برای پروژهٔ ریشهٔ Composer از مدخلی جداگانه با `project: true` استفاده کنید. اگر افزونهٔ قدیمی با `$with_project_name = true` از هر دو حالت پشتیبانی می‌کرد، هر دو مدخل را بسازید.
3. واژه‌نامه‌ها را به `locales/<code>/<style>.json` تبدیل کنید. مقادیر فایل‌های منبع، کلیدهای جست‌وجوی واژه‌نامه‌اند. برای نمونه، مدخل PHP به‌شکل `'enable' => 'Enable audit trail'` هنگام انتشار در `{locale}/audit.php` همچنان کلید `audit.enable` را در برنامه می‌سازد. ترجمه را با کلید `"Enable audit trail"` در واژه‌نامه ذخیره کنید.
4. فرمان زیر از Toolkit را از نسخهٔ کاری Mona روی بستهٔ قدیمی اجرا کنید. فرمان از واژه‌نامه‌های JSON و PHP قدیمی، `default.json` و `inline.json` می‌سازد و استثناهای زبان منطقه‌ای را منتقل می‌کند. خروجی را بازبینی و همراه مانیفست و فایل‌های منبع به بستهٔ جدید اضافه کنید. پوشه‌های واژه‌نامهٔ مبتنی بر نام مستعار را به کدهای استاندارد تغییر نام دهید.
5. Publisher فعلی را در مانیفست Composer بسته لازم کنید. اگر بسته از واژه‌نامهٔ مشترک انتظار ترجمه دارد، `laravel-lang/library` را هم اضافه کنید. بستهٔ حاصل را در یک برنامه نصب و `php artisan lang:update` را برای بررسی خروجی منتشرشده اجرا کنید.

```bash
php src/Toolkit/bin/toolkit migrate-locales path/to/legacy-pack --target=path/to/new-pack/locales
```

برای برنامه‌های موجود بررسی کنید کدام قواعد داخلی بسته پس از ارتقا اعمال می‌شوند.

- اگر همچنان به ترجمه‌های پیشین Laravel-Lang برای Spark Paddle، Spark Stripe، Nova Dusk Suite یا Nova Log Viewer نیاز دارید، کلیدهای مربوط را در فایل‌های تحت مالکیت برنامه نگه دارید یا منابع و واژه‌نامه‌های پیشین را به بسته‌ای سفارشی ببرید.
- ترجمه‌های کیت آغازین اکنون با `name` پروژه در Composer تطبیق داده می‌شوند. اگر نام پروژه را تغییر داده‌اید یا از کیت به‌عنوان وابستگی استفاده می‌کنید، نام Composer کیت، برای نمونه `laravel/livewire-starter-kit`، را به `localization_main.force` اضافه کنید تا ترجمه‌هایش منتشر شوند. [افزونه‌های ترجمه](./plugins.md)

### به‌روزرسانی ترجمهٔ جایگزین JSON و ابزارهای ترجمه {#update-json-fallback-and-translation-tooling}

اگر کد برنامه `LaravelLang\JsonFallback\Translator` را import یا به‌عنوان نوع اعلام می‌کند، نوع را به `LaravelLang\JsonFallback\OverridedTranslator` تغییر دهید یا به قرارداد مترجم Laravel وابسته شوید. اگر برای مترجم قدیمی زیرکلاس ساخته بودید، رفتار سفارشی را به مترجم برنامه یا سرویس ترجمهٔ خود منتقل کنید. برای کلیدی که پیدا نشده، مترجم جایگزین تابع بازخوانِ ترجمهٔ مفقود Laravel را صدا می‌زند. اگر برنامه چنین تابعی ثبت کرده، آن را بازبینی کنید و کلیدهایی را که هم در JSON زبان جایگزین و هم در PHP زبان جاری موجودند آزمایش کنید، چون JSON جایگزین زودتر بررسی می‌شود. [ترجمهٔ جایگزین JSON](./translations.md#json-fallback)

اگر از بستهٔ جداگانهٔ `laravel-lang/translator` برای ترجمهٔ ماشینی استفاده می‌کنید، پیش از حذف وابستگی، کاربردهای نمای `Translate` و یکپارچه‌سازی‌های Google، DeepL یا Yandex را به سرویس ترجمهٔ دلخواه مهاجرت دهید. ترجمه‌های تولیدشده توسط آن اسکریپت‌ها را در واژه‌نامهٔ برنامه یا بسته نگه دارید.

اگر فهرست‌های بسته را با `vendor/bin/lang` از `laravel-lang/status-generator` نگه‌داری می‌کنید، فرمان‌های جدید را پس از `composer install` از نسخهٔ کاری Mona اجرا کنید.

| اسکریپت پیشین | اسکریپت جایگزین Toolkit |
| --- | --- |
| `vendor/bin/lang status --path=path/to/pack` | `php src/Toolkit/bin/toolkit status path/to/pack`. گزارش‌های تولیدشدهٔ `locales/<code>/_status.json` را بخوانید. |
| `vendor/bin/lang sync --path=path/to/pack` | `php src/Toolkit/bin/toolkit sync:package path/to/pack`. برای نگه‌داری Library مشترک از `sync:library` استفاده کنید. |
| `vendor/bin/lang create` | `php src/Toolkit/bin/toolkit create-locale path/to/pack`. برای زبان‌های منطقه‌ای شناخته‌شده پوشه می‌سازد. |
| `vendor/bin/lang download` | URL فایل ZIP را با `--url`، مقصد کامل را با `--directory` و مسیرهای بایگانی را با `--paths` تکرارپذیر بدهید. |
| `vendor/bin/lang translate` | ترجمهٔ ماشینی را به سرویس جداگانه منتقل کنید. برای یافتن رشته‌های ذخیره‌شده‌ای که همچنان با منبع منطبق‌اند `status` را در Toolkit اجرا کنید. |

برای نمونه‌های کامل، [فرمان‌های Toolkit](./toolkit.md) را ببینید.

## بررسی ارتقا {#verify-the-upgrade}

آزمون‌های برنامه را اجرا کنید. پیام منتشرشده در هر زبان منطقه‌ای مهم، ترجمهٔ جایگزین، URL بومی‌شده با نام مستعار تنظیم‌شده و ترجمهٔ مدل ذخیره‌شده با کد استاندارد را بررسی کنید. فایل‌های تولیدشده و مقادیر ذخیره‌شدهٔ زبان منطقه‌ای را در کنار تغییرات منبع بازبینی کنید.
