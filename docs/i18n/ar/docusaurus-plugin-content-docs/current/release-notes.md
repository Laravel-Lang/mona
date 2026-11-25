---
title: "ملاحظات الإصدار"
description: "ترقيم إصدارات Laravel-Lang وتوافق المنصات."
---

## ترقيم الإصدارات والتوافق {#versioning-and-compatibility}

يتبع Laravel-Lang الترقيم الدلالي للإصدارات. تندرج التغييرات غير المتوافقة وإيقاف دعم المنصات ضمن الإصدارات الرئيسية. تحمل الحزم الرسمية رقم الإصدار نفسه. [الترقيم الدلالي للإصدارات](https://semver.org/)

يتطلب الإصدار الحالي PHP `^8.3` ويدعم Laravel 12 و13. قد يتطلب إصدار Laravel لديك إصدارًا أحدث من PHP. وتتطلب Common أيضًا امتدادي `intl` و`json` عبر تبعياتها.

تجد وسوم الإصدارات وملاحظات التصحيحات في [إصدارات Mona](https://github.com/Laravel-Lang/mona/releases). راجع [سياسة دعم Laravel](https://laravel.com/docs/releases#support-policy) لمواعيد صيانته.

## توفر الحزم {#package-availability}

يحدّث سير عمل التصدير فرع `main` في مستودعات الحزم بعد الدفع إلى `main` في Mona. يؤدي نشر إصدار Mona إلى تصدير وسمه إلى مستودعات الحزم.

قبل تثبيت الإصدار `20.x` أو الترقية إليه، تحقّق من توفر إصدار حزمة Composer التي تحتاج إليها على Packagist. قد يتوفر إصدار Mona قبل حزم Composer المطابقة له. [تشغيل عمليات تصدير الحزم](https://github.com/Laravel-Lang/mona/actions/workflows/export-packages.yml)
