---
title: "Tarjima plaginlari"
description: "Tarjima paketlarini o‘rnating yoki Laravel-Lang uchun o‘z Composer plaginingizni nashr qiling."
---

Publisher Composer orqali o‘rnatilgan tarjima paketlarini aniqlaydi. Paket `laravel_lang.json` manifesti, PHP yoki JSON manba fayllari va ixtiyoriy ravishda o‘z lokal lug‘atlarini taqdim etadi.

## Paketni o‘rnatish {#install-a-pack}

Paketni Laravel ilovangizga o‘rnating va foydalanayotgan lokallarni yangilang. Masalan, Actions paketi tugmalar va boshqa amallar uchun yozuvlarni qo‘shadi.

```bash
composer require laravel-lang/actions
php artisan lang:update
```

Lokal qo‘shish uchun [tarjimalarni boshqarish](./translations.md), uchinchi tomon paketlari uchun [hamjamiyat plaginlari](./plugins-community.md) bo‘limiga qarang.

### Rasmiy tarjima paketlari {#official-translation-packs}

Common bu paketlarni o‘z ichiga oladi. Siz har birini Composer bilan alohida ham o‘rnatishingiz mumkin.

| Paket | Tarjimalar |
| --- | --- |
| `laravel-lang/lang` | Laravel freymvorki hamda Breeze, Cashier, Fortify, Jetstream, Nova, Passkeys va Laravel UI kabi paketlar |
| `laravel-lang/starter-kits` | Laravel’ning Livewire, React, Svelte va Vue boshlang‘ich to‘plamlari |
| `laravel-lang/moonshine` | MoonShine administrator paneli |
| `laravel-lang/actions` | Tugmalar va boshqa amal yozuvlari |
| `laravel-lang/attributes` | Tekshiriladigan atributlarning tushunarli nomlari |
| `laravel-lang/http-statuses` | HTTP holat iboralari |

Har bir paket Publisher va umumiy Library’ni bog‘liqlik sifatida o‘rnatadi. Paket manifesti qaysi integratsiya va versiyalar uchun tarjima nashr qilish mumkinligini belgilaydi.

## Paket yaratish {#create-a-pack}

Tarjima andozasidan boshlang. Paketingizning `composer.json` faylida `laravel-lang/publisher` bog‘liqligini va qo‘llab-quvvatlaydigan versiyalarni belgilang. Agar paketingiz umumiy tarjimalarga tayansa, `laravel-lang/library` bog‘liqligini ham kiriting. Publisher ilovada `vendor/*/*/laravel_lang.json` yo‘li bo‘yicha manifestlarni qidiradi. [Tarjima andozasi](https://github.com/Laravel-Lang/mona/tree/main/src/TranslationsTemplate)

Manifestni paketingiz ildiziga joylang. Bu misol har bir o‘rnatilgan lokal uchun JSON fayli va PHP tarjima guruhini nashr qiladi.

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

Har bir `source` yo‘li paketning `source/` katalogiga nisbatan beriladi. Har bir `target` ilovaning Laravel til katalogiga nisbatan beriladi va `{locale}` ni o‘z ichiga oladi.

Masalan, `source/audit.json` da quyidagini ishlating:

```json
{
  "Enable audit trail": "Enable audit trail"
}
```

`source/audit.php` da esa quyidagini ishlating:

```php
<?php

return [
    'enable' => 'Enable audit trail',
];
```

Fransuzcha tarjimalar nashr qilinganda ikkala manba ham paketingizdagi `locales/fr/default.json` faylidan inglizcha iborani qidiradi.

```json
{
  "Enable audit trail": "Activer le journal d’audit"
}
```

Ilovada `__('Enable audit trail')` JSON tarjimasini, `__('audit.enable')` esa PHP tarjimasini o‘qiydi. Bir xil ibora uchun paket tarjimasi umumiy Library yozuvidan ustun turadi. Ibora ikkala lug‘atda ham bo‘lmasa, Publisher manba qiymatidan foydalanadi.

Paketga alohida Composer nomi bering va shablondagi `self.version` talablarini qo‘llab-quvvatlanadigan versiyaga mos cheklov bilan almashtiring. Lug‘at kataloglari uchun kanonik lokal kodlaridan foydalaning. Odatdagi tarjimalarni `default.json`, uslub variantlarini esa `inline.json` kabi fayllarda saqlang.

### Tarjimalarni shartga qarab qo‘llash {#apply-translations-conditionally}

| Manifest xususiyati | Moslik qachon qo‘llanadi |
| --- | --- |
| Shart yo‘q | Publisher uni har doim qo‘shadi. |
| `vendor` | Ko‘rsatilgan Composer paketi o‘rnatilgan. |
| `vendor` va `version` | O‘rnatilgan paket Composer versiya chekloviga mos. |
| `vendor` va `project: true` | Ildiz loyihaning Composer nomi `vendor` ga mos. Bu rejimda Publisher nomni tekshiradi. |

Moslik ham o‘rnatilgan bog‘liqlikka, ham shu nomdagi ildiz loyihaga qo‘llanishi kerak bo‘lsa, manifestda ikkita yozuvdan foydalaning. Eski provayderni o‘zgartirish uchun [yangilash qo‘llanmasiga](./upgrade.md), istisno qoidalari va paket mosligini majburlash uchun [sozlamalarga](./configure.md) qarang.

### Paketni Laravel ilovasida sinash {#test-a-pack-in-a-laravel-application}

Paket qo‘shni katalogda bo‘lsa, buyruqlarni sinov ilovasi tubida bajaring. `lang/fr.json` va `lang/fr/audit.php` ni tekshiring, ilova lokalini `fr` ga o‘rnating va ikki kalitni sinang. Paketni o‘zgartirgandan so‘ng `php artisan lang:update` ni bajaring.

```bash
composer config repositories.audit-translations path ../audit-translations
composer require "acme/audit-translations:*@dev"
php artisan lang:add fr
```
