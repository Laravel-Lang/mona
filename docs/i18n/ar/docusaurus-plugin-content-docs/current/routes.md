---
title: "المسارات الموطّنة"
description: "سجّل عناوين URL موطّنة وحدّد الإعداد المحلي في Laravel من الطلب."
---

تختار `laravel-lang/routes` الإعداد المحلي عند معالجة الطلب وتولّد عناوين URL لمجموعات المسارات الموطّنة. تندرج الحزمة ضمن Common.

```bash
composer require laravel-lang/routes
```

تأكّد من [تثبيت الإعدادات المحلية المستخدمة في عناوين URL](./translations.md). اضبط أسماء المعاملات والبرمجيات الوسيطة وسلوك الإعداد المحلي الافتراضي في [`config/localization_routes.php`](./configure.md).

## تسجيل مجموعة مسارات موطّنة {#register-a-localized-route-group}

استخدم الماكرو `Route::localizedGroup()` في `routes/web.php`. وتوفر الواجهة `LaravelLang\Routes\Facades\LocalizationRoute::group()` دالة التجميع نفسها.

```php
use Illuminate\Support\Facades\Route;

Route::localizedGroup(function (): void {
    Route::get('/products/{product}', fn (string $product) => view('products.show', [
        'product' => $product,
    ]))->name('products.show');
});
```

تسجّل الدالة المستدعاة نمطي عناوين URL في الإعداد الافتراضي.

| النمط | اسم المسار |
| --- | --- |
| `/products/{product}` | `products.show` |
| `/{locale}/products/{product}` | `localized.products.show` |

تستهلك البرمجية الوسيطة للإعداد المحلي المعامل `locale` قبل استدعاء معالج المسار. وإذا كان المسار مسمّى، يعاد توجيه طلب عنوان URL بإعداد محلي مجهول أو غير مثبّت إلى المسار دون البادئة.

## توليد عنوان URL موطّن {#generate-a-localized-url}

```php
use function LaravelLang\Routes\localizedRoute;

$url = localizedRoute('products.show', [
    'product' => 42,
    'locale' => 'fr',
], absolute: false);
// /fr/products/42
```

تستخدم الدالة المساعدة اسم معامل الإعداد المحلي المضبوط وتفضّل المسار ذا البادئة إن وُجد. تمرّر سلسلة الإعداد المحلي المقدّمة كما هي إلى آلية توليد العنوان. ولتوليد العنوان باسم بديل، مرّر الاسم أو استخرجه أولًا باستخدام [`Locales::toAlias()`](./locales.md). عند حذف الإعداد المحلي تستخدم الدالة `Locales::fallback()`. ولاستخدام الإعداد الحالي، مرّر `Locales::current()` صراحةً.

يحذف `localization_routes.hide` الإعداد المحلي الاحتياطي من العناوين التي تولّدها الدالة المساعدة. ويعيد `localization_routes.redirect` توجيه طلبات الإعداد الحالي من المجموعة ذات البادئة إلى المسار دون البادئة. القيمة الافتراضية لكلا الإعدادين هي `false`.

## اختيار الإعداد المحلي بالبرمجيات الوسيطة {#select-a-locale-with-middleware}

يمكن تطبيق كل برمجية وسيطة مباشرةً بفئتها من `LaravelLang\Routes\Middlewares`.

| البرمجية الوسيطة | المصدر |
| --- | --- |
| `LocalizationByParameterMiddleware` | معامل المسار، وهو `locale` افتراضيًا |
| `LocalizationByParameterWithRedirectMiddleware` | معامل المسار. عند وجود مسار مسمّى ومعامل معرّف، تستخدم الإعداد الحالي إذا كان المطلوب مفقودًا أو غير مثبّت |
| `LocalizationByCookieMiddleware` | ملف تعريف ارتباط اسمه `Accept-Language` افتراضيًا |
| `LocalizationByHeaderMiddleware` | أول إعدادات محلية مثبّتة توافق ترتيب التفضيل في ترويسة `Accept-Language`. يبدأ بالوسم الكامل ثم يجرّب اللغة الأساسية، ويتجاهل أحرف البدل والمدخلات ذات قيمة الجودة صفر. |
| `LocalizationBySessionMiddleware` | مفتاح الجلسة `Accept-Language` افتراضيًا |
| `LocalizationByModelMiddleware` | السمة `locale` للمستخدم المصادَق عليه في Eloquent افتراضيًا. ألحِق `:guard_name` بفئة البرمجية الوسيطة لاختيار حارس المصادقة |

الترتيب الافتراضي للمجموعة ذات البادئة هو معامل عنوان URL، ثم ملف تعريف الارتباط، ثم الترويسة، ثم الجلسة، ثم النموذج. تستخدم المجموعة دون البادئة الترتيب نفسه من دون خطوة عنوان URL. يمكن لمصدر صالح لاحق أن يتجاوز مصدرًا سابقًا. عند تغيير الإعداد المحلي، يُحدّث Laravel ويُحفَظ الرمز المعتمد في مفتاح الجلسة المضبوط. استخدم مجموعة البرمجيات الوسيطة `web` إذا احتجت إلى الجلسات وملفات تعريف الارتباط. تتطلب برمجية الترويسة الوسيطة امتداد PHP Intl.

عند تغيير الإعداد المحلي ترسل البرمجية الوسيطة الحدث `LaravelLang\Routes\Events\LocaleHasBeenSetEvent`. خاصيته `locale` كائن من نوع `LaravelLang\LocaleNames\LocaleData`. استخدم `$event->locale->code` للحصول على الرمز المعتمد.
