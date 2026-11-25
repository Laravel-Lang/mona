---
title: "Ishni boshlash"
description: "Laravel-Lang’ni o‘rnating va ilk Laravel tarjimalaringizni nashr qiling."
---

Laravel-Lang Laravel va uning ekotizimidagi paketlar uchun tarjimalarni nashr qiladi. `laravel-lang/common` to‘plamiga [lokal va nomlarni qidirish](./locales.md), [marshrutlarni mahalliylashtirish](./routes.md) va [modellarni tarjima qilish](./models.md) imkoniyatlari ham kiradi. Faqat Laravel tarjima paketi va nashr qilish uchun kerakli bog‘liqliklar kerak bo‘lsa, `laravel-lang/lang` paketini tanlang.

## Tez boshlash {#quick-start}

Quyidagi buyruqlarni Composer, PHP `^8.3`, `intl` va `json` kengaytmalari mavjud bo‘lgan Laravel 12 yoki 13 ilovasining asosiy katalogida bajaring. Laravel versiyangiz PHP’ning yangiroq versiyasini talab qilishi mumkin.

```bash
composer require laravel-lang/common
php artisan lang:add fr
```

Nashr qilingan fransuzcha xabarni olish uchun ilovangizda Laravel tarjima yordamchi funksiyasidan foydalaning.

```php
__('auth.failed', locale: 'fr');
// Ces identifiants ne correspondent pas à nos enregistrements.
```

Fransuz tilini standart til qilish uchun ilovaning `.env` faylida `APP_LOCALE=fr` qiymatini belgilang. Zaxira lokaldan foydalanadigan qidiruv joriy lokalda tarjima topmasa, ingliz tilini ishlatish uchun `APP_FALLBACK_LOCALE=en` qiymatini belgilang. Konfiguratsiya keshlangan bo‘lsa, `php artisan config:clear` buyrug‘i bilan keshni tozalang. Shundan so‘ng `__('auth.failed')` kabi chaqiruvlar ilovaning joriy lokalidan foydalanadi.

- [Laravel-Lang’ning oldingi versiyalari](/legacy)
- [Tarjimalarni boshqaring](./translations.md) va [mavjud lokallarni ko‘rib chiqing](./available-locales.md)
- [Mahalliylashtirishni sozlang](./configure.md)
- [Moslikni tekshiring](./release-notes.md#versioning-and-compatibility) yoki [mavjud o‘rnatmani yangilang](./upgrade.md)

## Reliz nomzodini sinab ko‘rish {#try-a-release-candidate}

E’lon qilingan 20.x reliz nomzodini sinash uchun alohida Laravel sinov ilovasidan foydalaning. Composer mos Laravel-Lang bog‘liqliklarini ham o‘rnata olishi uchun shu ilovadagi minimal barqarorlik darajasini `RC` ga sozlang. [Packagist](https://packagist.org/packages/laravel-lang/common) ro‘yxatidan versiyani tanlang. `<release-candidate>` o‘rniga tanlagan versiyangizni kiriting.

```bash
composer config minimum-stability RC
composer config prefer-stable true
composer require "laravel-lang/common:<release-candidate>"
php artisan lang:add fr
```
