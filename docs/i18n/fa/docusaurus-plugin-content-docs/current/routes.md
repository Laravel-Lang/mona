---
title: مسیرهای بومی‌شده
description: نشانی‌های بومی‌شده را ثبت کنید و زبان منطقه‌ای Laravel را از روی درخواست تعیین کنید.
---

`laravel-lang/routes` زبان منطقه‌ای را هنگام رسیدن درخواست انتخاب می‌کند و برای گروه‌های مسیر بومی‌شده URL می‌سازد. این بسته در Common گنجانده شده است.

```bash
composer require laravel-lang/routes
```

مطمئن شوید [زبان‌های منطقه‌ای به‌کاررفته در URLها نصب شده‌اند](./translations.md). نام پارامترها، میان‌افزارها و رفتار زبان منطقه‌ای پیش‌فرض را در [`config/localization_routes.php`](./configure.md) تنظیم کنید.

## ثبت گروه مسیر بومی‌شده {#register-a-localized-route-group}

در `routes/web.php` از ماکروی `Route::localizedGroup()` استفاده کنید. نمای `LaravelLang\Routes\Facades\LocalizationRoute::group()` نیز همان متد گروه‌بندی را ارائه می‌کند.

```php
use Illuminate\Support\Facades\Route;

Route::localizedGroup(function (): void {
    Route::get('/products/{product}', fn (string $product) => view('products.show', [
        'product' => $product,
    ]))->name('products.show');
});
```

در پیکربندی پیش‌فرض، تابع بازخوان هر دو الگوی URL را ثبت می‌کند.

| الگو | نام مسیر |
| --- | --- |
| `/products/{product}` | `products.show` |
| `/{locale}/products/{product}` | `localized.products.show` |

میان‌افزار زبان منطقه‌ای پیش از فراخوانی رسیدگی‌کنندهٔ مسیر، پارامتر `locale` را مصرف می‌کند. اگر مسیر نام‌دار باشد، درخواست برای زبان منطقه‌ای ناشناخته یا نصب‌نشده در URL به مسیر بدون پیشوند هدایت می‌شود.

Laravel تابع گروه را دو بار اجرا می‌کند. اگر پیشوند URL گروه بیرونی `admin` و پیشوند نام آن `admin.` باشد، نام مسیر بومی‌شده `admin.localized.products.show` خواهد بود.

## تولید URL بومی‌شده {#generate-a-localized-url}

```php
use function LaravelLang\Routes\localizedRoute;

$url = localizedRoute('products.show', [
    'product' => 42,
    'locale' => 'fr',
], absolute: false);
// /fr/products/42
```

تابع کمکی نام پارامتر زبان منطقه‌ای تنظیم‌شده را به کار می‌برد و اگر مسیر پیشونددار وجود داشته باشد، آن را ترجیح می‌دهد. رشتهٔ زبان منطقه‌ایِ داده‌شده را به فرایند تولید URL می‌سپارد. برای ساخت URL با نام مستعار، آن را مستقیماً بدهید یا ابتدا با [`Locales::toAlias()`](./locales.md) به دست آورید. اگر زبان منطقه‌ای را مشخص نکنید، تابع کمکی از `Locales::fallback()` استفاده می‌کند. برای زبان منطقه‌ای جاری، صریحاً `Locales::current()` را بدهید.

`localization_routes.hide` زبان منطقه‌ای جایگزین را از URLهای ساخته‌شده توسط تابع کمکی حذف می‌کند. `localization_routes.redirect` درخواست‌های زبان منطقه‌ای جاری را از گروه پیشونددار به مسیر بدون پیشوند می‌فرستد. پیش‌فرض هر دو تنظیم `false` است.

برای استفاده از کد معیار `Locale::French->value` و برای استفاده از نام مستعار یک شیء `LocaleData` را بفرستید. پیش از ساخت URL، انتخاب کاربر را با `Locales::isInstalled()` بررسی کنید.

## انتخاب زبان منطقه‌ای با میان‌افزار {#select-a-locale-with-middleware}

هر میان‌افزار را می‌توانید مستقیماً از طریق کلاس آن در `LaravelLang\Routes\Middlewares` اعمال کنید.

| میان‌افزار | منبع |
| --- | --- |
| `LocalizationByParameterMiddleware` | یک پارامتر مسیر، با نام پیش‌فرض `locale` |
| `LocalizationByParameterWithRedirectMiddleware` | یک پارامتر مسیر. اگر مسیر نام‌دار و پارامتر تعریف شده باشد، هنگام فقدان یا نصب نبودن زبان منطقه‌ای درخواستی، زبان منطقه‌ای جاری را جایگزین می‌کند |
| `LocalizationByCookieMiddleware` | به‌طور پیش‌فرض کوکی `Accept-Language` |
| `LocalizationByHeaderMiddleware` | نخستین زبان نصب‌شده به ترتیب اولویت در سرآیند `Accept-Language`. ابتدا برچسب کامل و سپس زبان پایه را بررسی می‌کند و نویسه‌های عام و ورودی‌هایی با مقدار کیفیت صفر را نادیده می‌گیرد. |
| `LocalizationBySessionMiddleware` | به‌طور پیش‌فرض کلید نشست `Accept-Language` |
| `LocalizationByModelMiddleware` | به‌طور پیش‌فرض ویژگی `locale` کاربر احراز هویت‌شدهٔ Eloquent. برای انتخاب guard، `:guard_name` را به کلاس میان‌افزار بیفزایید |

ترتیب پیش‌فرض در گروه پیشونددار، پارامتر URL، کوکی، سرآیند، نشست و سپس مدل است. گروه بدون پیشوند همین ترتیب را بدون مرحلهٔ URL دارد. هر منبع معتبر بعدی می‌تواند بر منبع قبلی اولویت داشته باشد. تغییر زبان منطقه‌ای، Laravel را به‌روز می‌کند و کد استاندارد را در کلید نشست پیکربندی‌شده ذخیره می‌کند. در صورت نیاز به نشست و کوکی از گروه میان‌افزار `web` استفاده کنید. میان‌افزار سرآیند به PHP Intl نیاز دارد.

وقتی میان‌افزار زبان منطقه‌ای را تغییر می‌دهد، رویداد `LaravelLang\Routes\Events\LocaleHasBeenSetEvent` را ارسال می‌کند. ویژگی `locale` آن شیئی از نوع `LaravelLang\LocaleNames\LocaleData` است. برای کد استاندارد از `$event->locale->code` استفاده کنید.

برای اولویت دادن به URL، در `localization_routes.middlewares.prefix` فقط `LocalizationByParameterPrefixMiddlewareMiddleware::class` را نگه دارید. زنجیرهٔ مسیرهای بدون پیشوند را جداگانه تنظیم کنید.
