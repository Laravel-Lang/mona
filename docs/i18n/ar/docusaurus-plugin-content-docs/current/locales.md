---
title: "الإعدادات المحلية والأسماء"
description: "اعثر على الإعدادات المحلية المدعومة والمثبّتة، وحوّل الأسماء البديلة وابحث عن الأسماء المترجمة."
---

يستخدم Laravel-Lang الرموز المعتمدة، مثل `en` و`fr` و`pt_BR`، في Laravel وأسماء ملفات الترجمة وصفوف ترجمة النماذج. الاسم البديل للإعداد المحلي اسم يقدّمه التطبيق، مثل `pt-br` في عنوان URL. عرّف الأسماء البديلة بربط كل رمز معتمد باسمه البديل في [`localization_main.aliases`](./configure.md).

## العثور على الإعدادات المحلية المدعومة والمثبّتة {#find-supported-and-installed-locales}

توفر حزمة `laravel-lang/locale-list` نوع تعداد (`enum`) ذا قيم مُسنَدة هو `LaravelLang\LocaleList\Locale`. وتطابق حالاته [الرموز المعتمدة المدعومة](./available-locales.md).

```php
use LaravelLang\LocaleList\Locale;

Locale::French->value;        // 'fr'
Locale::tryFrom('pt_BR');     // Locale::PortugueseBrazil
Locale::values();            // مصفوفة من الرموز المعتمدة
Locale::collection();        // مجموعة من الرموز المعتمدة
Locale::collection(false);   // مجموعة من حالات التعداد
```

تضيف `laravel-lang/locales` الواجهة `LaravelLang\Locales\Facades\Locales` التي تراعي إعدادات التطبيق. تُعيد هذه الدوال مجموعات من السلاسل النصية. مرّر `false` للحصول على الرموز المعتمدة. افتراضيًا، يمثّل كل رمز اسمه البديل إن كان مُعرّفًا.

| الدالة | القيمة المعادة |
| --- | --- |
| `Locales::available()` | جميع الإعدادات المحلية المعروفة |
| `Locales::installed()` | الإعدادات المحلية المستدل عليها من ملفات لغة التطبيق، بالإضافة إلى الإعدادات المحمية |
| `Locales::uninstalled()` | الإعدادات المحلية المتاحة خارج مجموعة الإعدادات المثبّتة |
| `Locales::protects()` | الإعدادان المحليان الفعليان، الحالي والاحتياطي، بعد إزالة التكرار |

تقرأ آلية الاكتشاف المدخلات المباشرة في `lang_path()` وملفات JSON المطابقة للنمط `vendor/*/*.json` نسبةً إلى ذلك المسار. يجب أن يُطابق اسم الملف أو المجلد رمزًا مدعومًا. وتقع مجلدات الترجمة التي تحتوي على ملفات PHP فقط ضمن `lang/vendor/<package>/` خارج أنماط الاكتشاف هذه. راجع [إدارة الترجمات](./translations.md) لمعرفة طريقة نشر إعداد محلي.

## قراءة الإعداد المحلي للتطبيق أو ضبطه {#read-or-set-the-application-locale}

```php
use LaravelLang\Locales\Facades\Locales;

if (Locales::isInstalled('fr')) {
    Locales::set('fr');
}

$canonicalCode = Locales::current(false);
$displayCode = Locales::current();
```

تقبل `set()` رمزًا أو اسمًا بديلًا أو حالة من التعداد `Locale` أو كائنًا من نوع `LocaleData`. وتضبط إعداد Laravel المحلي على الرمز المعتمد إذا كان الإعداد مثبّتًا. استخدم `isAvailable()` للتحقّق من دعم الرمز، و`isInstalled()` عندما تُشترط الملفات أو الحماية. تُحدّد `isProtected()` الإعدادين المحليين الحالي والاحتياطي.

تستخدم `current()` إعداد Laravel المحلي الحالي إن كان معروفًا، ثم تلجأ إلى `fallback()`. تستخلص `fallback()` إعداد Laravel الاحتياطي المضبوط وتستخدم `en` افتراضيًا إذا كان مجهولًا. وتقبل كلتاهما الوسيطة `false` للحصول على الرموز المعتمدة.

استخدم `fromAlias()` للحصول على الرمز المعتمد من الاسم البديل، و`toAlias()` للحصول على الاسم البديل من الرمز المعتمد. تُبقي هذه التحويلات السلاسل غير الفارغة وغير المعروفة كما هي. استخدم `isAvailable()` للتحقّق من المدخلات.

## عرض خيارات الإعداد المحلي {#display-locale-choices}

تُعيد `Locales::info('fr')` كائنًا من النوع `LaravelLang\LocaleNames\LocaleData` بخصائص `code` و`alias` و`native` و`localized`. ولغة العرض هي الإعداد المحلي الحالي للتطبيق. احذف الوسيطة للبحث عنه مباشرةً. يمكنك البحث عن إعداد معروف قبل تثبيته. يؤدي الرمز المجهول إلى الاستثناء `LaravelLang\Exceptions\UnknownLocaleException`.

استخدم `Locales::data()->installed()` للحصول على الإعدادات المحلية المثبّتة في صورة كائنات `LocaleData`. يوفّر `data()` أيضًا `available()` و`uninstalled()` و`protects()` للمجموعات الأخرى، و`current()` و`fallback()` لكائنات منفردة. يمكن لمحدّد اللغة إرسال `alias` وعرض الاسم `native`.

## البحث عن أسماء الإعدادات المحلية والبلدان والعملات {#look-up-locale-country-and-currency-names}

توفّر كل من حزم `laravel-lang/locale-names` و`laravel-lang/country-names` و`laravel-lang/currency-names` خدمة يمكنك حقنها من حاوية Laravel. تتضمن Common الحزم الثلاث.

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

الوسيطة الأولى هي لغة العرض. استخدم `null` للحصول على الأسماء بلغتها الأصلية. الوسيطة الثانية في `one()` هي الإعداد المحلي للسجل، مثل `fr` في الأمثلة أعلاه. وعند البحث عن بلد أو عملة، تجد رمز ISO في الكائن المُعاد. تقبل الوسيطتان الرموز المعتمدة والأسماء البديلة المضبوطة وحالات التعداد `Locale`.

تُعيد `all($forLocale)` مجموعة مفاتيحها رموز الإعدادات المحلية المعتمدة. مرّر `SortDirection::Ascending` أو `SortDirection::Descending` للفرز بحسب الخاصية `localized`. وكل قيمة كائن للقراءة فقط. تتضمن قيم البلدان والعملات أيضًا الخاصية `locale` للسجل.
