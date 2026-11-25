---
title: "المساهمة"
description: "ساهم في الترجمات وحزم PHP والتوثيق من نسخة محلية من Mona."
---

أنشئ نسخة متفرعة من [مستودع Mona](https://github.com/Laravel-Lang/mona) وافتح طلب السحب إلى فرع `main` فيه. يضم Mona قوالب المصادر والترجمات المشتركة وحزم PHP والتوثيق. تستمد مستودعات الحزم محتواها من Mona.

## تصحيح ترجمة أو إضافتها {#fix-or-add-a-translation}

1. ابحث عن العبارة الإنجليزية في مجلد `src/<Package>/source/` الخاص بالحزمة. مثلًا، توجد مصادر إطار Laravel وحزمه الرسمية في `src/Lang/source/`. يسرد ملف `laravel_lang.json` الخاص بالحزمة ملفات المصدر المستخدمة لكل إصدار من الحزمة الأصلية.
2. افتح `src/Library/locales/<locale>/default.json`. اعثر على العبارة الإنجليزية بوصفها مفتاح JSON وحرّر قيمته. أضف مدخلًا إذا كانت العبارة المصدرية موجودة في الحزمة وغائبة عن القاموس.
3. أبقِ العبارة المصدرية كما هي وحافظ على العناصر النائبة مثل `:count` و`:other` و`:attribute`. لرسالة تحقق لا تذكر اسم الحقل، استخدم المفتاح المصدري نفسه في `inline.json` مع صياغة تتجنّب تسمية الحقل. حافظ على بقية العناصر النائبة وصيغ الجمع.
4. إذا كانت الترجمة مطابقة عمدًا للعبارة الإنجليزية، أضف العبارة إلى ملف `_settings.json` للإعداد المحلي ضمن `excludes`. تستخدم Toolkit هذه القائمة عند توليد تقارير حالة الترجمة.
5. افتح طلب سحب يتضمن عنوانه رمز الإعداد المحلي، مثل `[fr]: Updated validation messages`.

إذا وجدت خطأً إملائيًا في عبارة مصدرية تابعة لمشروع أصلي، أبلغ عنه هناك ليُحدّث المصدر ومفتاح البحث معًا.

مثلًا، المدخل الافتراضي للفرنسية في `src/Library/locales/fr/default.json` هو:

```json
{
  "The :attribute field is invalid.": "Le champ :attribute est invalide."
}
```

والمدخل المقابل الذي لا يذكر اسم الحقل في `src/Library/locales/fr/inline.json` هو:

```json
{
  "The :attribute field is invalid.": "Ce champ est invalide."
}
```

لترجمات Mona المنشورة، عدّل `default.json` و`inline.json` في Library. لا تزال بعض الحزم تحتوي على قاموسَي `php.json` و`json.json` من الإصدارات السابقة، ويستخدمهما سير العمل الآلي لحالة ترجمات الحزم.

لتحديث أسماء البلدان أو العملات أو الإعدادات المحلية، حرّر القاموس في مجلد `data/` الخاص بالحزمة المعنية. راجع [الإعدادات المحلية والأسماء المترجمة](./locales.md).

## اقتراح إعداد محلي {#propose-a-locale}

راجع [الرموز المعتمدة للإعدادات المحلية](./available-locales.md). إذا كان الرمز في القائمة، فأضف قاموسه أو حدّثه ضمن `src/Library/locales/`. تستطيع Toolkit [إنشاء المجلدات الناقصة](./toolkit.md#manage-locale-directories). لاقتراح رمز جديد، افتح [نقاشًا في Laravel-Lang](https://github.com/orgs/Laravel-Lang/discussions/new?category=ideas&title=I%20want%20to%20add%20a%20new%20localization).

## تطوير الحزم {#develop-packages}

استخدم Git وComposer 2 وPHP بالإصدار `^8.3` مع امتدادات `intl` و`json` و`zip`. فعّل `pdo_sqlite` للاختبارات التي تستخدم قاعدة بيانات في الذاكرة. نفّذ الأوامر من جذر المستودع.

```bash
composer install
composer check-platform-reqs
```

يحمّل مشروع Composer في الجذر الشيفرة من `src/<Package>/src/` تلقائيًا. ضع تبعيات كل حزمة منشورة في ملف `src/<Package>/composer.json` الخاص بها. تستخدم التبعيات الداخلية القيد `self.version` لتثبيت حزم الإصدار نفسه معًا.

استخدم تطبيق Testbench في `workbench/` لتجربة الحزم محليًا. يبني `composer serve` التطبيق ويشغّل خادم التطوير. لفحص المسارات وأوامر Artisan من جذر المستودع، استخدم واجهة Testbench.

```bash
composer serve
```

```bash
php vendor/bin/testbench route:list
php vendor/bin/testbench help lang:update
```

يثبّت سير عمل التحقق من التبعيات كل حزمة على حدة باستخدام مستودع Composer من نوع `path` يشير إلى الحزم الأخرى في النسخة المحلية. إذا غيّرت تبعيات حزمة، فتحقّق من تثبيتها منفردة وشغّل اختبارات المستودع. يتضمن سير العمل خطوات الإعداد وإصدارات Laravel المستخدمة. [سير عمل اختبار التبعيات](https://github.com/Laravel-Lang/mona/blob/main/.github/workflows/test-dependencies.yml)

## تشغيل الفحوصات محليًا {#run-local-checks}

لتغييرات القواميس، ثبّت تبعيات JavaScript في جذر المستودع وشغّل فحوص Biome. يكتب أمر `status` تقارير عن المدخلات التي تطابق قيمتها مفتاح المصدر. راجع ملفات مصدر الحزمة للعثور على العبارات الغائبة عن القاموس. افحص التقارير الناتجة باستخدام `git status --short`. [أوامر Toolkit](./toolkit.md)

```bash
npm install
npx biome check src/Library/locales/fr/default.json
php src/Toolkit/bin/toolkit status src/Library
```

لتغييرات PHP، شغّل Pest وPHPStan من جذر المستودع. شغّل اختبارات السلوك بالتسلسل كما في CI، لأنها تتشارك مجلد تطبيق Testbench عند توليد النماذج. يمكن تشغيل اختبارات البنية بالتوازي. لاختبار ميزة واحدة، مرّر مسار ملفها أو مجلدها إلى Pest.

```bash
php vendor/bin/pest --exclude-group=arch
php vendor/bin/pest --group=arch --parallel
composer test:types
```

إذا ولّدت اختبارات المسارات عناوين تبدأ بـ `http://localhost:8000` من إعدادات تطوير Testbench، فاضبط `APP_URL` على `http://localhost` في عملية الاختبار قبل تشغيل Pest.

يطبّق `composer style` تسوية ملفات Composer وإصلاحات Rector وPint. راجع التغييرات قبل إرسال طلب السحب. لقياس تغطية الأسطر، فعّل Xdebug وشغّل `composer run-script --timeout=0 test:coverage`.

عند تعديل ملفات التعريف، شغّل فحص المخطط المستخدم في CI.

```bash
npx --yes ajv-cli@5.0.0 validate --spec=draft2020 -s resources/laravel-lang.schema.json -d 'src/*/laravel_lang.json'
```

راجع [إضافات الترجمة](./plugins.md) لصيغة ملف التعريف.

## تعديل التوثيق {#edit-the-documentation}

توجد الصفحات الإنجليزية الحالية في `docs/docs/`، وترجماتها في `docs/i18n/<language>/docusaurus-plugin-content-docs/current/`. حدّث الصفحات المقابلة عند تغيير التعليمات، وحافظ على معرّفات العناوين لتعمل الروابط عبر اللغات. توجد النسخة المؤرشفة لما قبل Mona في `docs/versioned_docs/version-legacy/`، وترجماتها في مجلدات `version-legacy/` المقابلة ضمن `docs/i18n/`.

استخدم إصدار Node.js المطلوب في `docs/package.json`. نفّذ أوامر التوثيق من `docs/`.

```bash
cd docs
npm ci
npm start
```

لمعاينة ترجمة واحدة، استخدم `npm start -- --locale fr`. أوقف خادم التطوير ثم ابنِ نسخة الإنتاج للتحقق من الروابط ومعرّفات العناوين وفهرسة البحث بكل اللغات.

```bash
npm run typecheck
npm run build
npm run serve
```

افتح العنوان الذي يطبعه `serve` لاختبار البحث وملفات Markdown الناتجة. يحفظ البناء الموقع الثابت في `docs/build/`.

## نشر إصدار {#publish-a-release}

يصدّر الدمج في `main` كل حزمة إلى فرع `main` في مستودعها ويحدّث مسودة إصدار Mona. راجع المسودة وانشرها من Mona بالوسم المشترك. لا يشغّل إنشاء الوسم وحده إجراءات النشر.

يؤدي نشر الإصدار إلى تصدير وسمه وإنشاء الإصدار المطابق في مستودعات الحزم، وإلى بناء موقع التوثيق ونشره. تحقّق من سيرَي العمل بعد النشر. لمعاينة تقسيم الحزم، شغّل **Export Packages** يدويًا من `main` مع تفعيل `dry-run`. [تصدير الحزم](https://github.com/Laravel-Lang/mona/actions/workflows/export-packages.yml) · [التوثيق](https://github.com/Laravel-Lang/mona/actions/workflows/docs.yml)
