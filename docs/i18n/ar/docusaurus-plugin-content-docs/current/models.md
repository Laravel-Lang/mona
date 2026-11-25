---
title: "ترجمة النماذج"
description: "خزّن سمات Eloquent المترجمة واقرأها واستعلم عنها باستخدام Laravel-Lang Models."
---

تضيف `laravel-lang/models` سمات مترجمة إلى نموذج Eloquent قائم. يخزّن كل صف ترجمة إعدادًا محليًا وقيم السمات لذلك الإعداد. تندرج الحزمة ضمن Common وتتطلب امتداد Intl في PHP.

```bash
composer require laravel-lang/models
```

## إضافة الترجمات إلى نموذج {#add-translations-to-a-model}

إذا كان لديك نموذج `App\Models\Article` قائم وترحيل للجدول الأساسي، فشغّل:

```bash
php artisan make:model-translation 'App\Models\Article' --columns=title --columns=description
php artisan migrate
```

يضيف المولّد السمة `LaravelLang\Models\HasTranslations` إلى النموذج الأساسي، وينشئ النموذج المرافق `ArticleTranslation` وترحيلًا وملفًا مساعدًا لبيئة التطوير. ينشئ الترحيل مفتاحًا خارجيًا إلى المقالة وزوجًا فريدًا `(item_id, locale)` وأعمدة نصية تقبل `null` للقيم المترجمة. إذا كنت تخزّن هذه القيم في الجدول الأساسي بالفعل، فانقلها إلى صفوف الترجمة قبل الاعتماد على دوال القراءة في النموذج الجديد.

إذا كان النموذج خارج `app/Models`، فأضف مجلده إلى [`localization_models.directories`](./configure.md) قبل تشغيل المولّد.

شغّل `php artisan make:model-translation` لاختيار النموذج والأعمدة تفاعليًا. إذا تركت القائمة فارغة، يستخدم المولّد `title` و`description`. لإضافة حقل لاحقًا، أنشئ ترحيلًا جديدًا وأضف الحقل إلى السمات القابلة للتعبئة والتحويلات في نموذج الترجمة.

## كتابة السمات المترجمة وقراءتها {#write-and-read-translated-attributes}

بعد [تثبيت الفرنسية](./translations.md)، يمكنك تخزين ترجمة بتمرير رمزها المعتمد أو اسمها البديل أو حالة من التعداد `Locale` أو كائن `LocaleData`. استخدم `save()` في النموذج الأساسي لحفظ التغييرات.

```php
use App\Models\Article;
use LaravelLang\LocaleList\Locale;

$article = Article::create(['title' => 'First article']);
$article->setTranslation('title', 'Premier article', Locale::French)->save();

$article->title;                         // الإعداد المحلي الحالي، ثم الاحتياطي إذا كانت القيمة null
$article->getTranslation('title', 'fr');  // 'Premier article'
```

عند تحديد الإعداد المحلي صراحةً، تُقرأ قيمته. وعند عدم تمريره، تستخدم الكتابة الإعداد الحالي وتلجأ القراءة إلى الإعداد الاحتياطي في Laravel إذا كانت القيمة الحالية `null`. تتحوّل الأسماء البديلة إلى رموز معتمدة عند البحث والتخزين. يؤدي تمرير إعداد مجهول أو غير مثبّت صراحةً إلى الاستثناء `LaravelLang\Exceptions\UnknownLocaleException`.

استخدم `setTranslations()` لتعيين عدة قيم لحقل واحد. يمكنك أيضًا تمرير خريطة مفاتيحها إعدادات محلية إلى `create()` أو `fill()` لكل حقل مترجم. يفترض المثال أن الإنجليزية والفرنسية مثبّتتان.

```php
$article->setTranslations('title', [
    'en' => 'First article',
    'fr' => 'Premier article',
])->save();
```

استخدم `isTranslatable('title')` للتحقّق من الحقول المضبوطة في النموذج، و`hasTranslated('title', 'fr')` للتحقّق من وجود قيمة معبّأة. تحذف `forgetTranslation('fr')` صف الفرنسية، وتحذف `forgetAllTranslations()` كل صفوف النموذج.

## الاستعلام عن الترجمات {#query-translations}

تستعلم `translated()` و`whereTranslation()` و`whereTranslationLike()` من خلال العلاقة `translations()`. تصفّي هذه العلاقة الصفوف افتراضيًا إلى الإعدادين المحليين الحالي والاحتياطي. للاستعلام عن إعدادات أخرى عبر هذه النطاقات، اضبط `localization_models.filter` على `false`. ولقراءة جميع صفوف نموذج واحد استخدم `translationsRaw()`.

عندما تكون الفرنسية هي الإعداد المحلي الحالي، استعلم عن المقالات ورتّبها بحسب العنوان الفرنسي:

```php
use App\Models\Article;

$articles = Article::query()
    ->whereTranslationLike('title', 'Premier%', 'fr')
    ->orderByTranslation('title', 'asc', 'fr')
    ->get();
```

تحمّل النماذج علاقة `translations()` مسبقًا. تعمل دوال `orWhereTranslation()` و`orWhereTranslationLike()` بديلًا للشروط السابقة. يفرز `orderByTranslation()` حسب إعداد محلي واحد، ويستخدم الحالي عند إغفال الوسيطة.

## مراقبة التغييرات وتحديث الملفات المساعدة لبيئة التطوير {#observe-changes-and-refresh-ide-helpers}

تحتوي مساحة الأسماء `LaravelLang\Models\Events` على `TranslationHasBeenSetEvent` و`TranslationHasBeenForgetEvent` و`AllTranslationsHasBeenForgetEvent`. يحمل حدث التعيين النموذج والعمود والاسم البديل الاختياري للإعداد المحلي والقيمتين القديمة والجديدة. ينطلق عند تعيين السمة قبل الحفظ اللاحق.

شغّل `php artisan lang:models-helper` لإعادة توليد الملفات المساعدة لجميع النماذج المكتشفة. مجلدها الافتراضي هو `vendor/_laravel_lang` ويمكن تغييره عبر `localization_models.helpers`.
