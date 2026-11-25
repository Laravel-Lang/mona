---
title: "الترقية إلى Mona"
description: "رقِّ تبعيات Laravel-Lang وإعداداته وترجماته المنشورة وعمليات التكامل مع التطبيق."
---

Mona هو المستودع الذي يصدر حزم Laravel-Lang معًا. واصل استخدام حزم Composer الفردية التي يحتاج إليها تطبيقك.

:::info[لماذا الإصدار 20؟]

بدأت منظومة Laravel-Lang بمشروع `laravel-lang/lang`. ظهرت الحزم الأخرى تباعًا، وكان لكل حزمة ترقيمها ودورة إصدارها. تطلّبت الترقيات الرئيسية تنسيقًا بين القائمين على صيانة الحزم، وكان على المستخدمين معرفة الإصدارات التي يمكن تثبيتها معًا.

أنشأنا `laravel-lang/common` لتسهيل ذلك بتحديد مجموعة من التبعيات المتوافقة. وكانت الترقية قد تشمل عدة حزم، لكل منها ترقيمها الخاص.

قبل Mona، لو كان لديك تطبيق يعمل على Laravel 11 مع PHP 8.2، فأي إصدارات الحزم كنت ستثبّت؟

<details>
<summary>إصدارات الحزم قبل Mona</summary>

| الحزمة | الإصدار |
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
| Config | `1.17.1` (إصدار من سلسلة 1.x واصلت حزم Laravel-Lang الأخرى استخدامه) و`2.3.2` (أحدث إصدار مستقر قبل Mona، نادرًا ما استخدمته تلك الحزم) |
| Publisher | `16.9.0` |

</details>

مع Mona، نصدر الحزم معًا برقم إصدار موحّد. كان رقم إصدار Publisher هو الأعلى، ولو تابعنا ترقيمه لكان الإصدار الرئيسي التالي `17.0`. بذلنا كثيرًا من الجهد والعناية لجمع هذه المنظومة في مستودع واحد، واخترنا الرقم 20 ليعبّر عن هذه الخطوة. ويمكنك أيضًا قراءته هكذا **Laravel-Lang 2.0**. 🙂

:::

:::info[رموز الإعدادات المحلية وأسماؤها البديلة]

يستخدم Laravel-Lang الآن الرموز المعتمدة للإعدادات المحلية في القيم التي يخزّنها، بما فيها مسارات ملفات اللغة المنشورة وصفوف ترجمة النماذج. وتستخدم إعدادات Laravel الحالية والاحتياطية الرموز المعتمدة أيضًا. في وقت التشغيل، استخدم الأسماء البديلة في مقاطع عناوين URL ومحدّدات اللغة. تُعيد دوال الواجهة التي ترجع رموز الإعدادات المحلية الاسم البديل المضبوط افتراضيًا، أو الرمز المعتمد عند عدم ضبط اسم بديل. مثلًا، اضبط `'pt_BR' => 'pt-br'` في `localization_main.aliases` للإبقاء على `/pt-br/account` مع النشر في `lang/pt_BR.json` وتخزين `pt_BR` في قاعدة البيانات.

:::

## تحديث التبعيات {#updating-dependencies}

يدعم الإصدار الحالي Laravel 12 و13 ويتطلب PHP `^8.3`. في ملف `composer.json` لتطبيقك، اضبط قيد الإصدار على `^20.0` لكل حزمة من الحزم أدناه مدرجة في `require` أو `require-dev`. إذا ثبّتَّ `laravel-lang/common` وحدها، فهي المتطلب الوحيد الذي تحتاج إلى تغييره.

قبل تثبيت الإصدار `20.x` أو الترقية إليه، تحقّق من توفر إصدار حزمة Composer التي تحتاج إليها على Packagist. قد يتوفر إصدار Mona قبل حزم Composer المطابقة له.

| الاستخدام | حزم Composer |
| --- | --- |
| الحزمة الجامعة | `laravel-lang/common` |
| حزم الترجمة | `laravel-lang/lang`، `laravel-lang/starter-kits`، `laravel-lang/moonshine`، `laravel-lang/actions`، `laravel-lang/attributes`، `laravel-lang/http-statuses` |
| النشر والإعداد | `laravel-lang/publisher`، `laravel-lang/config`، `laravel-lang/json-fallback`, `laravel-lang/library` |
| التكامل مع التطبيق | `laravel-lang/locales`، `laravel-lang/locale-list`، `laravel-lang/routes`، `laravel-lang/models` |
| أدوات مشتركة | `laravel-lang/exceptions`, `laravel-lang/filesystem` |
| قالب الحزمة | `laravel-lang/translations-template` |

إذا كان تثبيتك يعتمد فقط على سير عمل الترجمة القياسي، فطبّق تحديث التبعيات وأعد نشر الترجمات.

```bash
composer update
php artisan lang:update
```

إذا سجّلت مزوّدين أو نشرت إعدادات مخصّصة أو كنت تستخدم أسماء بديلة أو حزم ترجمة من جهات خارجية أو تستدعي واجهات الحزم البرمجية في تطبيقك، فاتبع الأقسام المناسبة أدناه قبل إعادة النشر.

## استبدال التبعيات التي أُعيدت تسميتها أو أصبحت قديمة {#replace-renamed-or-retired-dependencies}

حدّث أي تبعيات مباشرة على حزم الأسماء القديمة في `composer.json` قبل تشغيل `composer update`.

| حزمة Composer السابقة | الحزمة المطلوبة بالإصدار `^20.0` |
| --- | --- |
| `laravel-lang/native-country-names` | `laravel-lang/country-names` |
| `laravel-lang/native-currency-names` | `laravel-lang/currency-names` |
| `laravel-lang/native-locale-names` | `laravel-lang/locale-names` |

تعتمد Common بالفعل على حزم الأسماء الثلاث. حدّث شيفرة PHP أيضًا. راجع [استبدال واجهات البحث عن الأسماء](#replace-the-name-lookup-apis).

تحقّق من توفر إصدار متوافق مع Publisher الجديدة لكل إضافة من جهة خارجية. رحّل أي استخدام لـ `laravel-lang/translator` أو `laravel-lang/status-generator` قبل إزالة هاتين التبعيتين. راجع [أدوات الترجمة](#update-json-fallback-and-translation-tooling).

## تحديث مزوّدي الخدمات {#update-service-providers}

يكتشف Laravel مزوّدي Config وPublisher وRoutes وModels من ملفات تعريف الحزم. إذا كنت تعتمد على الاكتشاف، فحدّث شيفرة التطبيق أو الاختبارات التي تشير إلى فئات المزوّدين القديمة. وإذا كان التطبيق يسجّل الحزم يدويًا، فطبّق الاستبدالات التالية في قائمة المزوّدين نفسها، مثل `bootstrap/providers.php` أو `config/app.php`.

- `LaravelLang\Config\ServiceProvider` → `LaravelLang\Config\LangConfigServiceProvider`
- `LaravelLang\Publisher\ServiceProvider` → `LaravelLang\Publisher\LangPublisherServiceProvider`
- `LaravelLang\Routes\ServiceProvider` → `LaravelLang\Routes\LangRoutesServiceProvider`
- `LaravelLang\Models\ServiceProvider` → `LaravelLang\Models\LangModelsServiceProvider`

أزِل التسجيل اليدوي لـ `LaravelLang\Locales\LaravelLangLocalesServiceProvider` وفئات `ServiceProvider` القديمة في `LaravelLang\Actions` و`LaravelLang\Attributes` و`LaravelLang\HttpStatuses` و`LaravelLang\Lang` و`LaravelLang\MoonShine` و`LaravelLang\StarterKits`. تُكتشَف حزم الترجمة هذه عبر ملفات تعريفها.

إذا كنت تستخدم ترجمات JSON الاحتياطية، فاستبدل `LaravelLang\JsonFallback\TranslationServiceProvider` بـ `LaravelLang\JsonFallback\LangJsonFallbackServiceProvider` حيث سجّلته. وبذلك يظل استبدال مزوّد الترجمة في Laravel ساريًا.

حدّث الإشارات القديمة إلى المزوّدين قبل تشغيل `composer update` كي تتمكّن آلية اكتشاف الحزم من تهيئة التطبيق. إذا استمر فشل برنامج Composer نصي لأنه يحمّل فئة قديمة، فشغّل `composer update --no-scripts` ثم حدّث الإشارات وشغّل `composer dump-autoload` لإعادة الاكتشاف.

## ترحيل الإعدادات {#migrate-configuration}

إذا كنت قد خصّصت `config/localization.php`، فاحتفظ به مرجعًا أثناء نقل تغييراتك. تحمّل الحزم الإعدادات الافتراضية الجديدة تلقائيًا. انشر [ملفات الإعداد](./configure.md) التي تحتاج إلى تعديلها. تنشر العلامة `localization` الملفات الخمسة كلها، ويمكنك نشر ملف واحد باستخدام اسمه دون `.php` بوصفه العلامة.

```bash
php artisan vendor:publish --tag=localization
```

### الأسماء البديلة وأساليب الترجمة وتنسيق الملفات {#aliases-translation-styles-and-file-formatting}

- انسخ خريطة `aliases` إلى `config/localization_main.php`. احتفظ بالرموز المعتمدة مفاتيح وبالأسماء البديلة قيمًا، مثل `'de_CH' => 'de-CH'`.
- استبدل `inline` بـ `styles` في `config/localization_main.php`. اضبط `'styles' => ['default']` للإبقاء على الإعداد الافتراضي السابق مع رسائل التحقّق القياسية. وأبقِ `['default', 'inline']`، وهو الإعداد الافتراضي الجديد، إذا كنت تستخدم رسائل لا تذكر اسم الحقل. أزِل `LOCALIZATION_INLINE` و`LANG_PUBLISHER_INLINE` من البيئات التي ضبطتهما فيها.
- أزِل المفتاح `align` ومتغيّري البيئة `LOCALIZATION_ALIGN` و`LANG_PUBLISHER_ALIGN`. ولمحاذاة عوامل `=>` في مصفوفات PHP المنشورة، اضبط منسّق شيفرة PHP في مشروعك وشغّله على ملفات اللغة بعد النشر. مثلًا، يمكن لقاعدة `binary_operator_spaces` في Laravel Pint تطبيق `align_single_space_minimal` على `=>`.

### علامات الترقيم الذكية {#smart-punctuation}

إذا فعّلت علامات الترقيم الذكية، فأعد تسمية `LOCALIZATION_SMART_ENABLED` إلى `LOCALIZATION_PUNCTUATION_ENABLED` وأبقِ قيمته `true`. وإذا ضبطت `smart_punctuation.enable` مباشرةً في PHP، فانقل القيمة المنطقية إلى المفتاح `enabled` في المستوى الأعلى من `config/localization_punctuation.php`. علامات الترقيم الذكية معطّلة افتراضيًا.

انقل علامات الاقتباس المخصّصة من `smart_punctuation.common` و`smart_punctuation.locales` إلى المصفوفتين `common` و`locales` في المستوى الأعلى من الملف نفسه. استخدم الرموز المعتمدة مفاتيح لـ `locales`. تتجاوز علامات الاقتباس لكل إعداد محلي المفاتيح المناظرة في `common`. وإذا نسخت مثالًا فرنسيًا قديمًا يستخدم `&nbsp;`، فاستخدم مسافة غير قابلة للكسر فعلية أو `"\u{00A0}"` في سلاسل الاقتباس.

### المسارات الموطّنة {#localized-routes}

انقل إعداداتك المخصّصة من `routes` في الإعداد القديم إلى `config/localization_routes.php`.

| الإعداد المخصّص | التغيير في `config/localization_routes.php` | متغيّر البيئة الذي يُعاد تسميته |
| --- | --- | --- |
| معامل الإعداد المحلي أو الترويسة أو ملف تعريف الارتباط أو مفتاح الجلسة أو عمود نموذج المستخدم | انسخ إعدادات `names` المخصّصة وأبقِ المدخلات الافتراضية الأخرى. | |
| البادئة المضافة إلى أسماء المسارات الموطّنة | انقل `name_prefix` إلى `prefix`. إذا كانت القيمة فارغة، يُستخدم `localized.`. | `LOCALIZATION_NAME_PREFIX` → `LOCALIZATION_PREFIX` |
| إعادة التوجيه من المسار ذي البادئة | انقل `redirect_default` إلى `redirect`. | `LOCALIZATION_REDIRECT_DEFAULT` → `LOCALIZATION_REDIRECT` |
| إخفاء الإعداد المحلي الاحتياطي في دالة توليد العنوان | انقل `hide_default` إلى `hide`. | `LOCALIZATION_HIDE_DEFAULT` → `LOCALIZATION_HIDE` |
| البرمجيات الوسيطة المطبّقة على مجموعات المسارات الموطّنة | انقل `group.middlewares.default` إلى `middlewares.default` للمسارات دون بادئة، و`group.middlewares.prefix` إلى `middlewares.prefix` للمسارات ذات البادئة. حدّث أسماء فئات البرمجيات الوسيطة كما هو موضّح في [ترحيل المسارات](#update-localized-routes-and-middleware). | |

### ترجمة النماذج {#model-translations}

انقل إعداداتك المخصّصة من `models` إلى `config/localization_models.php`. انسخ القيم المخصّصة لـ `suffix` و`helpers` إلى المستوى الأعلى. استخدم قيمة منطقية للمفتاح `filter` بدلًا من المفتاح القديم `filter.enabled`. إذا كنت تتحكّم في التصفية ومسار الملفات المساعدة لبيئة التطوير عبر متغيّرات البيئة، فأبقِ `LOCALIZATION_FILTER_ENABLED` و`VENDOR_PATH`.

تفحص أوامر النماذج الآن `app/Models` افتراضيًا. إذا كنت تعتمد على الفحص القديم لكل `app/` أو كنت قد ضبطت `localization-private.models.directory` بنفسك، فضع كل مجلد تريد فحصه في المصفوفة `directories`. مثلًا، اضبط `'directories' => [app_path()]` للإبقاء على الفحص القديم.

### الإعدادات التي تستخدمها شيفرة التطبيق {#configuration-used-by-application-code}

- استبدل القراءة والكتابة عبر `LaravelLang\Config\Facades\Config` باستدعاءات إعدادات Laravel باستخدام المفاتيح الجديدة. مثلًا، استبدل `Config::shared()->models->filter->enabled` بـ `config('localization_models.filter')`. وإذا كنت تفضّل القراءة بأنواع محدّدة، فاحقن `LaravelLang\Config\Services\ModelLangConfig` واقرأ الخاصية `filter`، أو احقن `RouteLangConfig` من مساحة الأسماء نفسها لإعدادات المسارات.
- إذا كانت شيفرتك تقرأ بيانات الإعدادات المحلية من `localization-private.map`، فاقرأها من `localization_map.locales`. انسخ أي تعديلات إلى المصفوفة `locales` في `config/localization_map.php`.
- استبدل التسجيل عبر `localization-private.plugins` و`localization-private.packages` بـ [ملف تعريف إضافة](./plugins.md).
- أزِل كتلة `translators` القديمة وأي إعدادات قديمة من نوع `TRANSLATION_*` لم تكن تستخدمها سوى `laravel-lang/translator`.

بعد نقل الإعدادات المخصّصة وتحديث مراجع التطبيق، أزِل `config/localization.php` وشغّل `php artisan config:clear`.

## نقل ملفات اللغة إلى مسارات تستخدم الرموز المعتمدة {#move-language-files-to-canonical-paths}

1. اضبط `APP_LOCALE` و`APP_FALLBACK_LOCALE` على الرموز المعتمدة. انقل أسماءك البديلة إلى `localization_main.aliases` حتى تواصل عناوين URL ومدخلات الإعدادات المحلية العمل.
2. أعد تسمية الملفات والمجلدات التي تستخدم أسماء بديلة، مثل `lang/pt-br.json` و`lang/pt-br/`، إلى `lang/pt_BR.json` و`lang/pt_BR/`. تحقّق من الأسماء البديلة نفسها ضمن `lang/vendor/<package>/`. وعند وجود الاسمين معًا، ادمج محتوياتهما وسوِّ التعارض بين الترجمات المخصّصة قبل إزالة مسار الاسم البديل.
3. ثبّت كل إعداد محلي لا تمثّله سوى ترجمات PHP التابعة للحزم باستخدام `php artisan lang:add <locale>`. تستند قائمة الإعدادات المثبّتة إلى المدخلات المباشرة في مجلد اللغة وملفات JSON ضمن `lang/vendor/<package>/` والإعدادين المحليين الفعليين، الحالي والاحتياطي.
4. شغّل `php artisan lang:update` بعد ترحيل الإعدادات والإضافات، ثم راجع الفروق. قد تُبقي Publisher مفاتيح من عمليات تكامل لم تعد تنشر ملفًا. احتفظ بالمفاتيح التي لا يزال تطبيقك يستخدمها وأزِل المتقادمة بعد مراجعة مواضع استدعائها. راجع [برامج النشر والترجمات المخصّصة](#update-publication-scripts-and-keep-custom-translations) لإعادة توليد الملفات أو الاحتفاظ بها.

## تحديث عمليات التكامل {#update-integrations}

### الإعدادات المحلية {#locales}

أبقِ استيراد `LaravelLang\Locales\Facades\Locales` وحدّث الاستدعاءات بحسب البيانات التي يستخدمها كل موضع استدعاء.

| الاستدعاء السابق | البديل |
| --- | --- |
| `Locales::available()` أو `installed()` أو `protects()` التي تُعيد كائنات | استدعِ الدالة نفسها على `Locales::data()`. |
| `Locales::raw()->available()` أو `installed()` أو `protects()` | استدعِ الدالة نفسها مباشرةً على `Locales` للحصول على مجموعة من الأسماء البديلة النصية. مرّر `false` للرموز المعتمدة واستدعِ `->all()` إذا كان موضع الاستدعاء يتطلب مصفوفة. |
| `Locales::installed(false)` أو `Locales::raw()->installed(false)` | تختار `false` الآن الرموز المعتمدة. ويُدرج الإعدادان الحالي والاحتياطي حتى دون ملفات لغة. استبدل أيضًا الوسيطة المسماة القديمة `withProtects: false`. استخدم `Locales::installed(false)->diff(Locales::protects(false))->values()` لاستبعادهما. وللكائنات، استخدم `Locales::data()->installed()->reject(fn ($locale) => Locales::isProtected($locale))->values()`. |
| `Locales::notInstalled()` أو `Locales::raw()->notInstalled()` | استخدم `Locales::data()->uninstalled()` للكائنات، أو `Locales::uninstalled()` للأسماء البديلة النصية. مرّر `false` للحصول على الرموز المعتمدة النصية. |
| `Locales::getCurrent()` أو `Locales::getDefault()` | استخدم `Locales::data()->current()` لكائن نقل بيانات (DTO)، أو `Locales::current()` لسلسلة نصية. استبدل `Locales::raw()->getDefault()` بـ `Locales::current()`. إذا كنت تعتمد على `Locales::raw()->getCurrent()` لقيمة Laravel، فاستخدم `app()->getLocale()`. |
| `Locales::getFallback()` أو `Locales::raw()->getFallback()` | استخدم `Locales::data()->fallback()` لكائن نقل بيانات، أو `Locales::fallback()` لسلسلة نصية. يُحوّل الإعداد الاحتياطي المجهول الآن إلى الإنجليزية، والإعداد النشط المجهول إلى الإعداد الاحتياطي الفعلي. |
| `Locales::get($locale)` أو `Locales::raw()->get($locale)` | استخدم `isInstalled()` واختر الاحتياطي صراحةً. ولإعداد مدعوم قد يكون غير مثبّت، استخدم `isAvailable()` ثم `info()`. |
| `Locales::info($locale)` أو `Locales::raw()->info($locale)` | استخدم `Locales::info($locale)` واقرأ `->alias` أو `->code` عندما تحتاج إلى سلسلة نصية. مرّر `null` للإعداد المحلي الحالي. وعند وجود رمز مجهول أو فارغ، عالج `LaravelLang\Exceptions\UnknownLocaleException` أو تحقّق من `isAvailable()` قبل البحث. |

للحفاظ على سلوك `get()` القديم الذي يرجع إلى الإعداد المحلي الحالي عند إدخال قيمة غير صالحة أو غير مثبّتة، اتخذ هذا القرار في موضع الاستدعاء.

```php
use LaravelLang\Locales\Facades\Locales;

$locale = Locales::acceptsInput($requested) && Locales::isInstalled($requested)
    ? Locales::info($requested)
    : Locales::data()->current();

$code = $locale->code;   // استخدمه في المسارات وصفوف قاعدة البيانات وغيرها من البيانات المحفوظة.
$alias = $locale->alias; // استخدمه في عناوين URL ومحدّدات اللغة.
```

حدّث معالجات الطلبات التي كانت تمرّر نتيجة `raw()->get()` إلى `app()->setLocale()`. استخدم `Locales::set($locale)` بعد قبول قيمة الطلب. تقبل الدالة رموز الإعدادات المثبّتة والأسماء البديلة المضبوطة وحالات التعداد وكائن نقل البيانات الجديد، وتعيّن الرمز المعتمد لـ Laravel. فإذا كانت قيمة الطلب `pt-br`، يستقبل Laravel الرمز `pt_BR` بينما تُعيد `Locales::current()` السلسلة `pt-br`. استدعِ `Locales::current(false)` أو `Locales::fallback(false)` عندما تحتاج إلى الرموز المعتمدة.

عند حفظ قيم أخرى في تطبيقك، تحقّق من القيمة عبر `isAvailable()` أو `isInstalled()` ثم استخدم `fromAlias()` لاستخراج رمزها المعتمد. استخدم `toAlias()` لتنسيق الرمز المعتمد للعرض. استبدل استخدام `Services\Resolver` القديمة بـ `acceptsInput()` للتحقّق من أنواع PHP المقبولة أو `normalize()` لاستخراج سلسلة من قيمة ذات نوع محدّد. وتُبقي `normalize()` السلاسل كما قُدّمت، بما فيها الأسماء البديلة مثل `pt-br`.

استبدل `LaravelLang\Locales\Data\LocaleData` بـ `LaravelLang\LocaleNames\LocaleData` في الاستيرادات وتصريحات الأنواع والاختبارات. يحتوي الكائن الجديد للقراءة فقط على الخصائص النصية `code` و`alias` و`native` و`localized`. بدّل استخدام الخاصية القديمة `code` إلى `alias` في عناوين URL وقيم الواجهة. استخدم `Locale::from($data->code)` حيث كنت تقرأ `$data->locale`، مع استيراد `LaravelLang\LocaleList\Locale`. اقرأ `type` و`regional` و`direction` من `config("localization_map.locales.{$data->code}", [])` إذا كانت قوالبك أو منسّقاتك تستخدمها. حدّد قيمك الافتراضية للمدخلات المفقودة. وللإبقاء على القيم الافتراضية القديمة، استخدم `Latn` لنظام الكتابة والرمز المعتمد للمنطقة و`Direction::LeftToRight` للاتجاه.

أزِل الخيارات `withCountry` و`withCurrency` و`withCountries` و`withCurrencies`. واجلب البلد والعملة صراحةً لسجل الإعداد المحلي. حدّث استيرادات النوعين القديمين `LaravelLang\Locales\Data\CountryData` و`CurrencyData` إلى فئات البيانات في حزم الأسماء المناظرة.

```php
use LaravelLang\CountryNames\CountryNames;
use LaravelLang\CurrencyNames\CurrencyNames;
use LaravelLang\Locales\Facades\Locales;

$locale = Locales::info('pt_BR');
$displayLanguage = Locales::current(false);

$country = app(CountryNames::class)->one($displayLanguage, $locale->code);
$currency = app(CurrencyNames::class)->one($displayLanguage, $locale->code);
```

إذا كنت تستدعي `localizedLocales()` أو `localizedCountries()` أو `localizedCurrencies()` في الخدمة القديمة، فاستخدم `all(null)` و`all($displayLanguage)` في خدمة الأسماء المناظرة للحصول على الأسماء الأصلية والمترجمة، على الترتيب. استبدل الحقن المباشر للفئتين السابقتين `Services\Locales` أو `Services\RawLocales` بالواجهة أو بالفئتين المناظرتين `Services\MainService` و`Services\DataService`. راجع [الإعدادات المحلية والأسماء](./locales.md) لمعرفة واجهات الخدمات الجديدة.

#### الدوال المساعدة لنوع `enum` الخاص بالإعدادات المحلية {#locale-enum-helpers}

تحتفظ حالات التعداد `Locale` و`Direction` بقيمها. استبدل استخدام الدوال المساعدة المحذوفة من `archtechx/enums` عند ترقية قوائم الإعدادات المحلية أو محدداتها المخصّصة.

| الدالة المساعدة السابقة | البديل |
| --- | --- |
| `names()` | `array_column(Locale::cases(), 'name')` |
| `options()` | أنشئ خريطة الأسماء إلى القيم باستخدام `array_column(Locale::cases(), 'value', 'name')`. |
| `stringOptions()` | ولّد الخيارات في العرض انطلاقًا من `Locale::cases()`. استخدم قيمة الحالة للرمز المعتمد، أو `Locales::toAlias($case)` للاسم البديل المعروض. |
| `fromName()` أو `tryFromName()` | اعثر على الحالة بحسب `name` في `Locale::cases()` وتعامل مع عدم وجود مطابقة. وللقيمة المسندة مثل `pt_BR`، استخدم `Locale::from()` أو `Locale::tryFrom()`. |
| `Locale::onlyValues()` | أنشئ خريطة القيم إلى نفسها باستخدام `array_column(Locale::cases(), 'value', 'value')`. وللحصول على قائمة القيم، استخدم `Locale::values()`. |
| `Direction::values()` | استخدم `array_column(Direction::cases(), 'value')`. |

### استبدال واجهات البحث عن الأسماء {#replace-the-name-lookup-apis}

حدّث الاستيرادات من `LaravelLang\NativeCountryNames` و`LaravelLang\NativeCurrencyNames` و`LaravelLang\NativeLocaleNames` إلى `LaravelLang\CountryNames` و`LaravelLang\CurrencyNames` و`LaravelLang\LocaleNames`. احصل على `CountryNames` أو `CurrencyNames` أو `LocaleNames` من حاوية Laravel أو احقن الخدمة في فئتك.

| الاستخدام السابق | البديل |
| --- | --- |
| `SomeNames::get($displayLocale)` | `$names->all($displayLocale)` |
| `SomeNames::get()` للأسماء بلغتها الأصلية | `$names->all(null)` |
| `SortBy::None` | احذف وسيطة الفرز. |
| `SortBy::Key` | استدعِ `->sortKeys()` على المجموعة المعادة. |
| `SortBy::Value` | مرّر `SortDirection::Ascending` إلى `all()` للفرز بحسب الاسم المترجم. |

استخدم `one($displayLocale, $entryLocale)` لجلب مدخل معروف. مفتاح البحث هو إعداد محلي مثل `fr`. مثلًا، تُعيد `app(CountryNames::class)->one('de', 'fr')` بلد الإعداد الفرنسي باسمه بالألمانية وبقيمة `FR` في `code`. توجد كائنات نقل البيانات للبلدان والعملات في مساحة الأسماء الجذرية لحزمها، وتعرض مفتاح البحث في `locale`. ولا تزال حقول `code` تحمل رمز البلد أو العملة.

تُعيد الخدمات الثلاث مجموعات مفاتيحها رموز الإعدادات المحلية المعتمدة. إذا كنت تستخدم مصفوفة السلاسل من حزمة أسماء الإعدادات المحلية القديمة، فحوّل كل كائن نقل بيانات إلى القيمة `localized`. وإذا كنت تستدعي `toArray()` على كائنات البلدان أو العملات القديمة أو تغيّر خصائصها، فأنشئ مصفوفة التطبيق صراحةً. كائنات نقل البيانات الجديدة للقراءة فقط.

```php
use LaravelLang\LocaleNames\LocaleData;
use LaravelLang\LocaleNames\LocaleNames;

$options = app(LocaleNames::class)
    ->all('de', SortDirection::Ascending)
    ->map(fn (LocaleData $locale): string => $locale->localized)
    ->all();
```

تحقّق من الإعدادات المحلية التي يختارها المستخدم في الفهرس المعني قبل استدعاء `one()`. قد تؤدي لغة عرض مجهولة إلى قائمة فارغة من `all()`. مرّر `null` إذا كان التطبيق يحتاج إلى الأسماء بلغتها الأصلية. وإذا كانت شيفرتك تستخدم تعدادًا مخصّصًا ذا قيم مُسنَدة، فمرّر قيمته النصية. [الإعدادات المحلية والأسماء](./locales.md)

### تحديث المسارات الموطّنة والبرمجيات الوسيطة {#update-localized-routes-and-middleware}

استورد الدالة المساعدة من مساحة أسمائها في ملفات PHP وقوالب Blade التي تولّد عناوين URL موطّنة. يمكنك أيضًا استدعاء `\LaravelLang\Routes\localizedRoute()` مباشرةً.

```php
use LaravelLang\Locales\Facades\Locales;

use function LaravelLang\Routes\localizedRoute;

$url = localizedRoute('products.show', [
    'product' => 42,
    'locale' => Locales::current(),
]);
```

تسجّل `Route::localizedGroup()` و`LocalizationRoute::group()` المسارات دون بادئة وتلك التي تحمل بادئة الإعداد المحلي. أبقِ المجموعة القائمة وراجع استدعاءات دالة توليد العنوان. تستخدم الدالة الاسم البديل للإعداد الاحتياطي عند حذف معامل الإعداد المحلي. ولمبدّل اللغة، مرّر الاسم المطلوب صراحةً باستخدام `Locales::toAlias($locale)` أو `$localeData->alias`. تُستخدم سلسلة الإعداد المحلي المقدّمة كما هي. طابِق مفتاح المصفوفة مع `localization_routes.names.parameter` إذا كنت قد أعدت تسميته.

استبدل الاستيرادات والتسجيلات في المسارات و`bootstrap/app.php` وقوائم البرمجيات الوسيطة المخصّصة. توجد جميع الفئات في هذا الجدول ضمن `LaravelLang\Routes\Middlewares`.

| البرمجية الوسيطة السابقة | البديل |
| --- | --- |
| `LocalizationByCookie` | `LocalizationByCookieMiddleware` |
| `LocalizationByHeader` | `LocalizationByHeaderMiddleware` |
| `LocalizationByModel` | `LocalizationByModelMiddleware` |
| `LocalizationBySession` | `LocalizationBySessionMiddleware` |
| `LocalizationByParameter` | `LocalizationByParameterMiddleware` |
| `LocalizationByParameterWithRedirect` | `LocalizationByParameterWithRedirectMiddleware` |
| `LocalizationByParameterPrefix` | `LocalizationByParameterPrefixMiddlewareMiddleware` |

إذا أنشأت برمجية النموذج الوسيطة مع حارس مصادقة، فمرّره كمعامل للبرمجية الوسيطة في Laravel، مثل `LocalizationByModelMiddleware::class . ':admin'`. وتتوقع آلية تحديد المستخدم فيها نموذج Eloquent. في الفئات الفرعية المخصّصة للبرمجيات الوسيطة، احقن `LaravelLang\Config\Services\RouteLangConfig` واقرأ أسماء المصادر من `$this->routeConfig->name` وأعِد `int|string|null` من `detect()`. استبدل المراجع المباشرة إلى `LaravelLang\Routes\Services\Route` بـ `LaravelLang\Routes\Services\RouteService`.

اختبر كل مصدر يستخدمه تطبيقك. يرتّب امتداد PHP Intl اللغات في `Accept-Language` حسب تفضيلاتها ويجرّب الوسم الكامل قبل اللغة الأساسية. قد تتجاوز برمجية وسيطة لاحقة إعدادًا محليًا اختاره مصدر سابق. لجعل عنوان URL هو المرجع في المسارات ذات البادئة، أبقِ برمجية تحديد الإعداد المحلي من عنوان URL وحدها في سلسلة تلك المسارات.

اختبر توليد العناوين والطلبات كلًا على حدة. يحذف `localization_routes.hide` مقطع الإعداد المحلي الاحتياطي عند توليد عنوان URL. ويعيد `localization_routes.redirect` توجيه طلبات الإعداد الحالي ذات البادئة إلى المسار دون بادئة. ويعيد المسار المسمّى ذو البادئة أيضًا توجيه الطلب بإعداد غير صالح أو غير مثبّت إلى المسار دون بادئة.

في مستمعي `LocaleHasBeenSetEvent`، استخدم `$event->locale->code` للرمز المعتمد و`$event->locale->alias` للعرض. بيانات الحدث الآن من النوع `LaravelLang\LocaleNames\LocaleData`. ترسل البرمجية الوسيطة الحدث عندما يتغيّر الإعداد المحلي المختار، وتحفظ الرمز المعتمد في الجلسة. استبدل الاستدعاءات اليدوية لـ `LocaleHasBeenSetEvent::dispatch($data)` بـ `event(new LocaleHasBeenSetEvent($data))`. [المسارات الموطّنة](./routes.md)

### ترحيل شيفرة ترجمة النماذج وبياناتها {#migrate-model-translation-code-and-data}

افحص العمود `locale` في كل جدول ترجمة بحثًا عن صفوف تستخدم أسماء بديلة. حدّث تلك الصفوف إلى الرمز المعتمد لكل اسم بديل في ترحيل للبيانات. قبل التحديث، تحقّق مما إذا كان `item_id` نفسه يحتوي بالفعل على صف للرمز المعتمد. ادمج القيم التي تريد الاحتفاظ بها في صف واحد لاستيفاء قيد التفرّد `(item_id, locale)`. كانت إصدارات من السمة القديمة تكتب الرموز المعتمدة بالفعل، لذا استند في هذا الترحيل إلى القيم المخزّنة فعليًا في جداولك.

حدّث الاستيرادات والفئات المولّدة.

| الاستخدام السابق | البديل |
| --- | --- |
| `LaravelLang\Locales\Data\LocaleData` | `LaravelLang\LocaleNames\LocaleData` |
| `LaravelLang\Models\Casts\TrimCast` | `LaravelLang\Models\Casts\TrimStringCast` |
| `LaravelLang\Models\Exceptions\UnavailableLocaleException` | `LaravelLang\Exceptions\UnknownLocaleException` |
| `LaravelLang\Models\Exceptions\AttributeIsNotTranslatableException` أو `UnknownModelPathException` | اسم الفئة نفسه في `LaravelLang\Exceptions`. |
| `translationModelName()` | `translationModel(): string`. حدّث الدوال المخصّصة التي تختار فئة ترجمة خاصة. |
| `getTranslationTable()` | استخدم `translationQualifyColumn($column)` لاسم عمود مؤهّل باسم الجدول، أو `translationsRaw()->getRelated()->getTable()` لاسم الجدول. |

تقبل `getTranslation()` و`setTranslation()` و`hasTranslated()` و`forgetTranslation()` سلسلة إعداد محلي أو `Locale` أو `LocaleData` الجديد. ثبّت كل إعداد محلي تمرّره إليها صراحةً قبل الاستدعاء. وتستخدم القراءة دون إعداد صريح الإعداد الاحتياطي عندما تكون القيمة الحالية `null`. أما الإعداد الصريح فيقرأ قيمته مباشرةً. وإذا استدعيت الدالة الأقل مستوى `translation()`، فمرّر سلسلة إعداد محلي أو `null`. وحوّل كائن نقل البيانات باستخدام `$locale->code`.

```php
$article->setTranslation('title', 'Schweizer Titel', 'de-CH')->save();
$article->translation('de_CH');
```

عند ضبط `de-CH` اسمًا بديلًا للرمز `de_CH` وتثبيت الألمانية السويسرية، يشير الاستدعاءان إلى الصف المخزّن بالرمز `de_CH`. لتعبئة عدة قيم في حقل مترجم، مرّر خريطة مفاتيحها إعدادات محلية، مثل `['en' => 'Title', 'de-CH' => 'Schweizer Titel']`. تؤدي القوائم ذات الفهارس الرقمية الآن إلى الاستثناء `InvalidLocaleInputException`. على الشيفرة المخصّصة التي تضبط العلاقة المحمّلة `translations` تمرير مجموعة Laravel من نماذج الترجمة.

استدعِ `translated()` و`whereTranslation()` والنطاقات ذات الصلة عبر منشئ استعلامات Eloquent. مرّر رمزًا أو اسمًا بديلًا كسلسلة الإعداد المحلي للنطاق. ومع `orderByTranslation()`، استخدم `'asc'` أو `'desc'` أو `SortDirection`. عند الاستعلام عن إعداد غير الحالي والاحتياطي عبر `whereTranslation()` أو `whereTranslationLike()`، اضبط `localization_models.filter` على `false` أو استعلم مباشرةً عبر `translationsRaw()`.

حدّث مستهلكي الأحداث الذين يستدعون `$event->locale->value` أو يقارنون `$event->locale` بتعداد `Locale`. يحمل `TranslationHasBeenSetEvent` و`TranslationHasBeenForgetEvent` الآن اسمًا بديلًا نصيًا عند تحديد إعداد محلي صراحةً. يحمل حدث التعيين `null` عند إغفال الإعداد المحلي. حوّل الاسم البديل باستخدام `Locales::fromAlias()` قبل استخدامه مفتاحًا محفوظًا. يُرسل حدث التعيين عند تعيين السمة قبل `save()`. وللإرسال اليدوي لأيّ من أحداث ترجمة النموذج الثلاثة، استخدم `event(new …)`.

إذا كنت تحتفظ بالملفات المساعدة المولّدة لبيئة التطوير، فشغّل `php artisan lang:models-helper` بعد تحديث الاستيرادات ومسارات اكتشاف النماذج. [ترجمة النماذج](./models.md)

### تحديث برامج النشر والاحتفاظ بالترجمات المخصّصة {#update-publication-scripts-and-keep-custom-translations}

| الأمر أو الافتراض السابق | طريقة الترحيل |
| --- | --- |
| كان `lang:add <locale>` يحدّث الإعدادات المحلية الجاري إضافتها فقط | يحدّث الآن جميع الإعدادات المحلية المثبّتة. مرّر الرموز صراحةً في البرامج النصية وضع في الحسبان تحديث ملفات الإعدادات المحلية القائمة. |
| `lang:reset` | استخدم `lang:update` للدمج. لإعادة البناء من الصفر، احتفظ بنسخة احتياطية من المدخلات المخصّصة وأزِل الملفات التي تريد إعادة توليدها، ثم شغّل `lang:add` مع الرموز الصريحة للإعدادات التي تريد إعادة بنائها. |
| `lang:rm <locale> --force` | استخدم `lang:rm <locale>`. إعدادا Laravel المحليان الحالي والاحتياطي محميان. |
| الاكتفاء بفحص رمز خروج `lang:rm` | افحص كل نتيجة. تؤكد `DONE` الإزالة، وتشير `FAILED` إلى بقاء مسار بعد محاولة الإزالة. وقد تظهر `PROTECTED` و`INVALID` و`SKIP` مع رمز خروج يدل على النجاح. |

إذا كنت تستدعي Artisan بفئة الأمر، فانتقل من `LaravelLang\Publisher\Console\Add` و`Remove` و`Update` إلى `AddCommand` و`RemoveCommand` و`UpdateCommand`. ولمزيّن نصوص مخصّص، نفّذ `LaravelLang\Publisher\Contracts\Decorator` واربطه بالحاوية بدلًا من `Contracts\TextDecorator`. تتلقّى دالته `convert(string $locale, string $value): string` الرمز المحلي المعتمد.

شغّل `lang:update` للمرة الأولى بينما تخضع ملفات اللغة القائمة لنظام التحكّم في الإصدارات. تدمج Publisher الترجمات في الأهداف القائمة وتستبدل قيم المفاتيح المطابقة، بما فيها الرقمية. احتفظ بالترجمات المخصّصة في ملف ترجمة مستقل للتطبيق حيثما أمكن. ولصيانة ملف مولّد كامل بنفسك، أضف نمط مساره إلى `localization_main.skip`. تحقّق من أي قيمة تعود إلى العبارة المصدرية، إذ تستخدم Publisher تلك العبارة عند غياب الترجمة عن القواميس.

إذا أعاد تحديث سابق فهرسة مفاتيح ملفات `http-statuses.php`، فاحتفظ بنسخة احتياطية من الملفات المولّدة المتأثرة ثم أزلها. أزل إدخال `'*/http-statuses.php'` من `localization_main.skip` إذا كنت قد أضفته لتجاوز المشكلة. شغّل `php artisan lang:update` لإعادة إنشاء الملفات، ثم استعد الترجمات المخصّصة باستخدام رموز الحالة المقصودة. [إدارة الترجمات](./translations.md)

### استبدال تسجيل الإضافات بالطريقة القديمة {#replace-legacy-plugin-registration}

إذا كنت تستخدم حزمة ترجمة من جهة خارجية، فرقِّها إلى إصدار يعتمد على ملف التعريف. وإذا كنت تصون حزمة، فأزِل المزوّد ومدخل اكتشاف Composer اللذين يسجّلان `LaravelLang\Publisher\Plugins\Provider` القديم. وإذا كان هذا المزوّد يضبط أيضًا خدمات Laravel غير مرتبطة، فأبقِ تسجيلاتها في مزوّد خدمة Laravel عادي.

1. أضف `laravel_lang.json` إلى جذر الحزمة المثبّتة ضمن `vendor/`. حوّل كل مدخل من `Plugin::files()` إلى مسار `source` نسبي إلى مجلد `source/` للحزمة، ومسار `target` نسبي إلى مجلد اللغة للتطبيق. استخدم `{locale}` في المسار الهدف.
2. انقل أي قواعد مطابقة لـ Composer إلى ملف التعريف. استخدم `vendor` و`version` لتبعية مثبّتة ومدخلًا منفصلًا مع `project: true` لمشروع Composer جذر. إذا كانت إضافتك القديمة تستخدم `$with_project_name = true` وتدعم الوضعين، فأنشئ المدخلين.
3. حوّل القواميس إلى `locales/<code>/<style>.json`. القيم في ملفاتك المصدرية هي مفاتيح البحث في القاموس. مثلًا، لا يزال المدخل `'enable' => 'Enable audit trail'` في PHP ينشئ مفتاح التطبيق `audit.enable` عند النشر إلى `{locale}/audit.php`. خزّن ترجمته تحت `"Enable audit trail"` في القاموس.
4. من نسخة عمل لمستودع Mona، شغّل أمر Toolkit أدناه على الحزمة القديمة. ينشئ ذلك `default.json` و`inline.json` من قواميس JSON وPHP القديمة وينقل استثناءات الإعدادات المحلية. راجع الناتج وأضفه إلى الحزمة الجديدة مع ملف التعريف والملفات المصدرية. أعد تسمية أي مجلدات للقواميس تستخدم أسماء بديلة إلى الرموز المعتمدة.
5. أضف الإصدار الحالي من Publisher إلى متطلبات Composer في الحزمة. وأضف `laravel-lang/library` أيضًا إذا كانت الحزمة تتوقع ترجمات من القاموس المشترك. ثبّت الحزمة الناتجة في تطبيق وشغّل `php artisan lang:update` للتحقّق من الناتج المنشور.

```bash
php src/Toolkit/bin/toolkit migrate-locales path/to/legacy-pack --target=path/to/new-pack/locales
```

في التطبيقات القائمة، تحقّق من قواعد الحزم المضمّنة التي تنطبق بعد الترقية.

- إذا كنت لا تزال تحتاج إلى ترجمات Laravel-Lang المنشورة سابقًا لـ Spark Paddle أو Spark Stripe أو Nova Dusk Suite أو Nova Log Viewer، فاحتفظ بتلك المفاتيح في الملفات التي يديرها التطبيق أو انقل المصادر والقواميس القديمة إلى حزمة مخصّصة.
- تطابق ترجمات حزم البدء الآن `name` لمشروع Composer. إذا أعدت تسمية المشروع أو كنت تستخدم حزمة بدء كتبعية، فأضف اسم Composer الخاص بها، مثل `laravel/livewire-starter-kit`، إلى `localization_main.force` لنشر ترجماتها. [إضافات الترجمة](./plugins.md)

### تحديث آلية الرجوع إلى ترجمات JSON الاحتياطية وأدوات الترجمة {#update-json-fallback-and-translation-tooling}

إذا كانت شيفرة التطبيق تستورد `LaravelLang\JsonFallback\Translator` أو تصرّح به نوعًا، فغيّر النوع إلى `LaravelLang\JsonFallback\OverridedTranslator` أو اعتمد على عقد المترجم في Laravel. وإذا أنشأت فئة فرعية من المترجم القديم، فانقل السلوك المخصّص إلى مترجم للتطبيق أو خدمة ترجمة خاصة بك. عند تعذّر إيجاد مفتاح، يستدعي المترجم البديل دالة رد النداء للترجمات المفقودة في Laravel. راجع تلك الدالة إذا كان تطبيقك يسجّلها، واختبر المفاتيح الموجودة في JSON الاحتياطي وفي PHP للغة الحالية، إذ يُفحص JSON الاحتياطي أولًا. [الرجوع إلى ترجمات JSON الاحتياطية](./translations.md#json-fallback)

إذا كنت تستخدم الحزمة المستقلة `laravel-lang/translator` للترجمة الآلية، فانقل استخدام واجهتها `Translate` وأي تكامل مع Google أو DeepL أو Yandex إلى خدمة الترجمة التي تختارها قبل إزالة تلك التبعية. احتفظ بالترجمات التي سبق أن ولّدتها هذه البرامج النصية في قواميس تطبيقك أو حزمتك.

إذا كنت تصون فهارس الحزم باستخدام `vendor/bin/lang` من `laravel-lang/status-generator`، فشغّل الأوامر الجديدة من نسخة عمل لمستودع Mona بعد `composer install`.

| البرنامج النصي السابق | أمر Toolkit البديل |
| --- | --- |
| `vendor/bin/lang status --path=path/to/pack` | `php src/Toolkit/bin/toolkit status path/to/pack`. اقرأ التقارير المولّدة `locales/<code>/_status.json`. |
| `vendor/bin/lang sync --path=path/to/pack` | `php src/Toolkit/bin/toolkit sync:package path/to/pack`. استخدم `sync:library` لصيانة Library المشتركة. |
| `vendor/bin/lang create` | `php src/Toolkit/bin/toolkit create-locale path/to/pack`. ينشئ هذا مجلدات للإعدادات المحلية المعروفة. |
| `vendor/bin/lang download` | مرّر عنوان URL لأرشيف ZIP باستخدام `--url`، والوجهة الكاملة باستخدام `--directory`، وأي مسارات من الأرشيف باستخدام `--paths` القابل للتكرار. |
| `vendor/bin/lang translate` | انقل الترجمة الآلية إلى خدمة مستقلة. شغّل `status` في Toolkit للعثور على السلاسل المحفوظة التي لا تزال تطابق المصدر. |

راجع [أوامر Toolkit](./toolkit.md) لأمثلة كاملة.

## التحقّق من الترقية {#verify-the-upgrade}

شغّل اختبارات تطبيقك. تحقّق من رسالة منشورة في كل إعداد محلي مهم، ومن ترجمة احتياطية، وعنوان URL موطّن بالاسم البديل المضبوط، وترجمة نموذج مخزّنة بالرمز المعتمد. راجع الملفات المولّدة والقيم المحلية المحفوظة إلى جانب تغييرات الشيفرة المصدرية.
