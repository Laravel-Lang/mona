---
title: "البدء"
description: "ثبّت Laravel-Lang وانشر أولى ترجمات Laravel."
---

ينشر Laravel-Lang ترجمات Laravel وحزم منظومته. وتوفّر حزمة `laravel-lang/common` أيضًا [البحث عن الإعدادات المحلية والأسماء](./locales.md) و[المسارات المترجمة](./routes.md) و[ترجمة النماذج](./models.md). إذا كنت تحتاج فقط إلى حزمة ترجمات Laravel واعتماديات النشر، فاستخدم `laravel-lang/lang`.

## البدء السريع {#quick-start}

نفّذ هذه الأوامر من المجلد الجذري لتطبيق Laravel 12 أو 13 باستخدام Composer وPHP `^8.3` وامتدادي `intl` و`json`. قد يتطلب إصدار Laravel لديك إصدارًا أحدث من PHP.

```bash
composer require laravel-lang/common
php artisan lang:add fr
```

استخدم دالة الترجمة المساعدة في تطبيق Laravel لقراءة رسالة منشورة بالفرنسية.

```php
__('auth.failed', locale: 'fr');
// Ces identifiants ne correspondent pas à nos enregistrements.
```

لجعل الفرنسية اللغة الافتراضية، اضبط `APP_LOCALE=fr` في ملف `.env` الخاص بالتطبيق. اضبط `APP_FALLBACK_LOCALE=en` لاستخدام الإنجليزية عندما لا يجد البحث الذي يدعم اللغة البديلة ترجمة باللغة الحالية. إذا كان الإعداد مخزّنًا مؤقتًا، فامسح ذاكرته بتشغيل `php artisan config:clear`. تستخدم استدعاءات مثل `__('auth.failed')` عندئذٍ اللغة الحالية للتطبيق.

- [إصدارات Laravel-Lang السابقة](/legacy)
- [إدارة الترجمات](./translations.md) و[استعراض الإعدادات المحلية المتاحة](./available-locales.md)
- [ضبط إعدادات التوطين](./configure.md)
- [التحقّق من التوافق](./release-notes.md#versioning-and-compatibility) أو [ترقية تثبيت قائم](./upgrade.md)

## تجربة إصدار مرشّح {#try-a-release-candidate}

استخدم تطبيق Laravel منفصلًا للاختبار عند تجربة إصدار مرشّح منشور من 20.x. اضبط الحد الأدنى للاستقرار في ذلك التطبيق على `RC` حتى يتمكن Composer من تثبيت تبعيات Laravel-Lang المطابقة. اختر إصدارًا مدرجًا في [Packagist](https://packagist.org/packages/laravel-lang/common). استبدل `<release-candidate>` بالإصدار الذي اخترته.

```bash
composer config minimum-stability RC
composer config prefer-stable true
composer require "laravel-lang/common:<release-candidate>"
php artisan lang:add fr
```
