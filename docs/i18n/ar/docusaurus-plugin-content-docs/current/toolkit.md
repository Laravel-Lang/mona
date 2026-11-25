---
title: "أدوات الترجمة"
description: "صُن مصادر الترجمة وقواميسها ورحّلها واستوردها من نسخة عمل لمستودع Mona."
---

Toolkit هي أداة سطر الأوامر لصيانة حزم الترجمة. شغّلها من نسخة عمل لمستودع Mona بعد تثبيت تبعيات Composer الجذرية. لنشر الترجمات في تطبيق Laravel، استخدم [أوامر Artisan](./translations.md).

```bash
composer install
php src/Toolkit/bin/toolkit list
php src/Toolkit/bin/toolkit migrate-locales --help
```

## التحقّق من حالة الترجمة {#check-translation-status}

```bash
php src/Toolkit/bin/toolkit status src/Lang
```

افتراضيًا، يكتب الأمر `locales/<locale>/_status.json` داخل المشروع. يسرد المدخلات التي تطابق ترجمتها المحفوظة العبارة المصدرية، ويجمعها بحسب الأسلوب. يدمج الاستثناءات من `locales/_settings.json` و`locales/<locale>/_settings.json` ويحذف التقارير الفارغة. للعثور على العبارات الغائبة تمامًا عن قاموس، قارنه بملفات الحزمة المصدرية.

استخدم الخيار `--output-dir` لكتابة التقارير في مجلد منفصل بأسماء على النمط `<locale>.json`. يفسّر الأمر المسارات النسبية انطلاقًا من مجلد العمل الحالي، وينشئ مجلد الإخراج عند الحاجة إلى كتابة تقرير.

```bash
php src/Toolkit/bin/toolkit status src/Library --output-dir=workbench/status
```

أضف العبارات المترجمة بالنص نفسه عمدًا إلى `excludes` في ملف `_settings.json` المناسب.

```json
{
  "excludes": ["Laravel"]
}
```

## إدارة مجلدات الإعدادات المحلية {#manage-locale-directories}

```bash
php src/Toolkit/bin/toolkit create-locale src/Library
php src/Toolkit/bin/toolkit delete-locale src/Library
```

ينشئ `create-locale` المجلدات الناقصة لجميع [رموز الإعدادات المحلية المعتمدة](./available-locales.md). تبدأ المجلدات الجديدة فارغة. ويحذف `delete-locale` كل مجلد فرعي مباشر في `locales/` لا يرد اسمه في القائمة. افحص الهدف قبل التشغيل، خصوصًا إذا كان يتضمن أسماء بديلة أو رموزًا مخصّصة.

## إزالة مدخلات القواميس القديمة {#remove-obsolete-dictionary-entries}

```bash
php src/Toolkit/bin/toolkit sync:library src
php src/Toolkit/bin/toolkit sync:package path/to/translation-pack
```

- يجمع `sync:library` العبارات المصدرية من الحزم التي تحتوي على ملف تعريف `laravel_lang.json` في `src/` ويعيد كتابة القواميس في `src/Library/locales/`.
- يقرأ `sync:package` مجلد `source/` لحزمة واحدة ويزيل المدخلات غير المستخدمة من مجلد `locales/` الخاص بها. ويحذف القواميس التي فرغت بعد الإزالة.

يحتفظ الأمران بالترجمات الموجودة للعبارات النشطة ويحافظان على الملفات التي تبدأ بشرطة سفلية مثل `_settings.json`. أضف ترجمات العبارات الجديدة إلى القاموس مباشرةً.

عند إغفال المسار، يستخدم `sync:library` دليل `src/` الخاص بالمستودع، ويستخدم `sync:package` دليل العمل الحالي. يعالج كلاهما أدلة الإعدادات المحلية ذات الرموز القياسية.

## ترحيل القواميس القديمة {#migrate-legacy-dictionaries}

```bash
php src/Toolkit/bin/toolkit migrate-locales path/to/legacy-pack --target=path/to/new-pack/locales
```

مرّر حزمة قديمة تضم `source/` و`locales/`. يحدّد الخيار `--target` المجلد الذي سيحتوي على المجلدات الفرعية للإعدادات المحلية. عند حذف الخيار، يكتب الأمر النتائج في `locales/` داخل المشروع القديم.

- تُنقل ترجمات `json.json` إلى `default.json` وترجمات `json-inline.json` إلى `inline.json` مع الإبقاء على العبارات المصدرية المستخدمة مفاتيحَ لها.
- تُنقل ترجمات `php.json` إلى `default.json` وترجمات `php-inline.json` إلى `inline.json` بعد مطابقة المفاتيح المتداخلة بالعبارات المصدرية في ملفات PHP ضمن `source/`.
- تنتقل مدخلات `_excludes.json` إلى ملف `_settings.json` للإعداد المحلي الهدف ضمن `excludes`.

تدمج Toolkit الترجمات في القواميس المستهدفة. وعند تطابق المفاتيح، تكون الأولوية للترجمة المرحّلة. وإذا وفّر التنسيقان القديمان ترجمة للعبارة نفسها، تكون الأولوية لقاموس PHP.

تحتفظ Toolkit بالملفات في `source/` وتحذف ملفات القواميس والاستثناءات القديمة التي نجح ترحيلها، حتى عند استخدام وجهة منفصلة. راجع [دليل الترقية](./upgrade.md) لتغييرات التطبيق أو ملف تعريف الإضافة.

## استيراد المصادر من أرشيف ZIP {#import-sources-from-a-zip-archive}

```bash
php src/Toolkit/bin/toolkit download \
  --url=https://github.com/laravel/framework/archive/refs/heads/12.x.zip \
  --directory=workbench/downloads/framework/12.x \
  --paths=src/Illuminate/Translation/lang/en \
  --only-copy
```

- يحدّد `--directory` الوجهة النهائية. ويمكن تكرار `--paths` للمجلدات النسبية إلى جذر الأرشيف.
- تُنسخ ملفات PHP وJSON المحدّدة إلى الوجهة بأسمائها. تُنسخ ملفات PHP بايتًا ببايت، وتُدمج ملفات JSON ذات الاسم نفسه مع إعطاء الأولوية للقيم الواردة.
- في غياب `--only-copy`، تبحث Toolkit عن استدعاءات الترجمة المدعومة ذات المفاتيح الحرفية. وإذا وجدت عبارات، تكتب مصدر JSON باسم المجلد الأب للوجهة، مثل `framework.json` في المثال.
- يتطلب الأمر امتداد ZIP في PHP. يجهّز التغييرات قبل النشر ويحاول استعادة الوجهة السابقة إذا فشل النشر.

يلزم توفير `--url` و`--directory`. تفحص الأداة الدلائل المحددة في `--paths` وأدلتها الفرعية. احذف `--paths` للبحث عن مفاتيح الترجمة فقط، وراجع المفاتيح الناتجة قبل إضافتها إلى الحزمة.
