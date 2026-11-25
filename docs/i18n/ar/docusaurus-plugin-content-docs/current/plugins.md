---
title: "إضافات الترجمة"
description: "ثبّت حزم الترجمة أو انشر إضافتك الخاصة بـ Laravel-Lang عبر Composer."
---

تكتشف Publisher حزم الترجمة المثبّتة عبر Composer. توفّر الحزمة ملف تعريف `laravel_lang.json` وملفات مصدر PHP أو JSON، ويمكن أن تتضمن قواميسها الخاصة أيضًا.

## تثبيت حزمة {#install-a-pack}

ثبّت الحزمة في تطبيق Laravel وحدّث الإعدادات المحلية التي تستخدمها بالفعل. مثلًا، تضيف حزمة Actions تسميات للأزرار وغيرها من الإجراءات.

```bash
composer require laravel-lang/actions
php artisan lang:update
```

راجع [إدارة الترجمات](./translations.md) لإضافة إعداد محلي و[إضافات المجتمع](./plugins-community.md) لحزم الجهات الخارجية.

### حزم الترجمة الرسمية {#official-translation-packs}

تتضمن Common هذه الحزم. ويمكنك أيضًا تثبيت كل واحدة على حدة عبر Composer.

| الحزمة | الترجمات |
| --- | --- |
| `laravel-lang/lang` | إطار Laravel وحزم مثل Breeze وCashier وFortify وJetstream وNova وPasskeys وLaravel UI |
| `laravel-lang/starter-kits` | حزم Laravel للبدء باستخدام Livewire وReact وSvelte وVue |
| `laravel-lang/moonshine` | لوحة إدارة MoonShine |
| `laravel-lang/actions` | الأزرار وتسميات الإجراءات الأخرى |
| `laravel-lang/attributes` | أسماء سمات التحقّق بصياغة مفهومة للقارئ |
| `laravel-lang/http-statuses` | عبارات حالة HTTP |

تتضمن تبعيات كل حزمة Publisher وLibrary المشتركة. ويحدّد ملف تعريفها عمليات التكامل والإصدارات التي يمكنها نشر ترجمات لها.

## إنشاء حزمة {#create-a-pack}

ابدأ بقالب الترجمة. أضف `laravel-lang/publisher` إلى متطلبات ملف `composer.json` لحزمتك وحدّد الإصدارات المدعومة. أضف `laravel-lang/library` إذا كانت الحزمة تعتمد على الترجمات المشتركة. تبحث Publisher عن ملفات التعريف في المسار `vendor/*/*/laravel_lang.json` داخل التطبيق. [قالب الترجمة](https://github.com/Laravel-Lang/mona/tree/main/src/TranslationsTemplate)

ضع ملف التعريف في جذر الحزمة. ينشر هذا المثال ملف JSON ومجموعة ترجمة PHP لكل إعداد محلي مثبّت.

```json
{
  "$schema": "https://raw.githubusercontent.com/Laravel-Lang/mona/main/resources/laravel-lang.schema.json",
  "plugins": [
    {
      "files": [
        {"source": "audit.json", "target": "{locale}.json"},
        {"source": "audit.php", "target": "{locale}/audit.php"}
      ]
    }
  ]
}
```

كل مسار `source` نسبي إلى مجلد `source/` في الحزمة. وكل مسار `target` نسبي إلى مجلد اللغة في تطبيق Laravel ويتضمن `{locale}`.

مثلًا، ضع التالي في `source/audit.json`:

```json
{
  "Enable audit trail": "Enable audit trail"
}
```

وضع التالي في `source/audit.php`:

```php
<?php

return [
    'enable' => 'Enable audit trail',
];
```

عند نشر الفرنسية، يبحث المصدران عن العبارة الإنجليزية في `locales/fr/default.json` داخل الحزمة.

```json
{
  "Enable audit trail": "Activer le journal d’audit"
}
```

في التطبيق، تقرأ `__('Enable audit trail')` ترجمة JSON، وتقرأ `__('audit.enable')` ترجمة PHP. تكون الأولوية لترجمات الحزمة على مدخلات Library المشتركة للعبارة نفسها. وإذا غابت العبارة عن القاموسين، تستخدم Publisher القيمة المصدرية.

استخدم اسم Composer خاصًا بحزمتك واستبدل متطلبات `self.version` في القالب بقيود مناسبة للإصدار. استخدم رموز الإعدادات المحلية القياسية في أسماء أدلة القواميس. ضع الترجمات الأساسية في `default.json` وأشكالها البديلة في ملفات مثل `inline.json`.

### تطبيق الترجمات بحسب الشروط {#apply-translations-conditionally}

| خاصية ملف التعريف | متى ينطبق الربط |
| --- | --- |
| دون شرط | تُضمّنه Publisher دائمًا. |
| `vendor` | حزمة Composer المسماة مثبّتة. |
| `vendor` و`version` | إصدار الحزمة المثبّتة يطابق قيد إصدار Composer. |
| `vendor` و`project: true` | اسم مشروع Composer الجذر يطابق `vendor`. تتحقّق Publisher من الاسم في هذا الوضع. |

استخدم مدخلين في ملف التعريف إذا وجب أن ينطبق الربط على تبعية مثبّتة وعلى مشروع جذر يحمل الاسم نفسه. راجع [دليل الترقية](./upgrade.md) لتحويل مزوّد قديم و[الإعداد](./configure.md) لقواعد الاستثناء وفرض مطابقة الحزم.

### اختبار الحزمة في تطبيق Laravel {#test-a-pack-in-a-laravel-application}

شغّل الأوامر من جذر تطبيق الاختبار، على افتراض أن الحزمة موجودة في دليل مجاور. تحقّق من `lang/fr.json` و`lang/fr/audit.php`، واضبط لغة التطبيق على `fr` واختبر مفتاحي الترجمة. بعد تعديل الحزمة، انشر الترجمات مجددًا باستخدام `php artisan lang:update`.

```bash
composer config repositories.audit-translations path ../audit-translations
composer require "acme/audit-translations:*@dev"
php artisan lang:add fr
```
