---
title: "Sozlamalar"
description: "Mona’da tarjima uslublari, lokal muqobil nomlari, nashr filtrlari, tinish belgilari, marshrutlar va modellarni sozlang."
---

Mona standart sozlamalarni avtomatik yuklaydi. Moslashtirishingiz kerak bo‘lgan fayllarni ilovangizning `config/` katalogiga chiqaring.

```bash
php artisan vendor:publish --tag=localization_main
```

`localization` tegi barcha beshta faylni chiqaradi. `.php` kengaytmasisiz har bir fayl nomi ham nashr tegi, ham Laravel sozlama kalitidir.

| Fayl | Sozlamalar |
| --- | --- |
| `localization_main.php` | Tarjima uslublari, muqobil nomlar va nashr filtrlari |
| `localization_punctuation.php` | Aqlli punktuatsiya va lokalga xos qo‘shtirnoqlar |
| `localization_routes.php` | [Marshrut nomlari, lokal manbalari va middleware](./routes.md) |
| `localization_models.php` | [Tarjima modeli suffiksi, filtrlash va generator yo‘llari](./models.md) |
| `localization_map.php` | Yozuv, hudud va yozuv yo‘nalishi kabi lokal metama’lumotlari |

Laravel har bir yuqori darajadagi sozlamani ilovadagi qiymat bilan almashtiradi. `localization_punctuation.php` faylidagi `locales` massivini tahrirlaganda, hali kerak bo‘lgan lokallarning standart qoidalarini saqlang.

Sozlamalarni o‘zgartirgach, mavjud keshni `php artisan config:clear` bilan tozalang. Nashr sozlamalari ta’sir qilgan tarjimalarni qayta yaratish uchun `php artisan lang:update` buyrug‘ini bajaring.

## Tarjima uslublari {#translation-styles}

Tarjima uslubi tanlangan iboralar ifodasini o‘zgartiradigan nomlangan lug‘atdir. Masalan, paket samimiy ohang uchun `informal.json` faylini yoki mahsulotda ishlatiladigan ifodalar uchun `john.json` faylini taqdim etishi mumkin. Uslub nomlari lokal kodlari, ilova va paket nomlariga bog‘liq emas. Istalgan tarjima paketi o‘zi qamragan tillar uchun `locales/<locale>/<style>.json` manzilida uslub taqdim etishi mumkin. Uslub bitta iboradan ham iborat bo‘lishi mumkin. Dastlab norasmiy murojaat shakllarini qo‘llash ko‘zda tutilgan edi. [Norasmiy tarjimalar muhokamasi](https://github.com/orgs/Laravel-Lang/discussions/124)

`john` uslubini taqdim etadigan plaginni o‘rnatganingizdan so‘ng, har bir lug‘at manbasida ustuvor bo‘lishi uchun uni `config/localization_main.php` ro‘yxatining oxiriga qo‘ying.

```php
'styles' => ['default', 'informal', 'john'],
```

`php artisan lang:update` buyrug‘ini bajarganingizda, Publisher har bir o‘rnatilgan lokal uchun uslub fayllarini birlashtiradi. Har bir ibora avval `john` lug‘atidan qidiriladi. U yerda topilmasa, `informal`, unda ham bo‘lmasa, `default` qo‘llanadi. Masalan, fransuz tili paketi `locales/fr/john.json` faylida bir iborani almashtirishi, nemis tili esa ayni ibora uchun `locales/de/informal.json` yoki `locales/de/default.json` faylidan foydalanishi mumkin. Publisher bu tartibni umumiy Library lug‘atiga va faylni taqdim etayotgan paketga alohida qo‘llaydi. Mazkur fayl uchun paketdagi mos ibora Library yozuvidan ustun turadi.

Standart sozlama `['default', 'inline']`. `inline` uslubi «Bu maydon qabul qilinishi kerak» kabi umumiy tekshiruv xabarlarini beradi. Faqat standart ifodalardan foydalanish uchun `styles` qiymatini `['default']` qilib belgilang. Ro‘yxatda `default` bo‘lmasa, Publisher uni boshiga qo‘shadi.

## Lokallarning muqobil nomlari {#locale-aliases}

`localization_main.aliases` qiymatini kanonik kodlarni muqobil nomlarga moslovchi xarita qilib belgilang. Masalan, `config/localization_main.php` qaytaradigan massivga quyidagi yozuvni qo‘shing.

```php
'aliases' => [
    'de_CH' => 'de-CH',
],
```

Artisan `de-CH` ni qabul qilishi mumkin, Laravel va nashr qilingan fayllar esa `de_CH` dan foydalanadi. `APP_LOCALE` va `APP_FALLBACK_LOCALE` uchun kanonik kodlarni belgilang. O‘girish va qidirish misollari uchun [Lokallar va nomlar](./locales.md) bo‘limiga qarang.

Muqobil nomlarni solishtirishda katta va kichik harflar farqlanadi. Har bir lokal uchun boshqa lokallarning kanonik kodlariga teng bo‘lmagan noyob muqobil nom tanlang.

## Fayllarni chiqarib tashlash va integratsiyalarni majburan qo‘llash {#skip-files-and-force-integrations}

- `localization_main.skip` harf registriga bog‘liq bo‘lmagan `*` andozalarini ajratkich sifatida `/` ishlatilgan to‘liq yo‘llar bilan solishtiradi. U manba fayllari, lug‘atlar va nashr manzillarini chiqarib tashlashi mumkin. Masalan, `['*/lang/fr/validation.php']` Publisher’ning `lang/` dan foydalanuvchi ilovada ushbu faylga yozishini oldini oladi.
- `localization_main.force` Composer paket nomlarini qabul qiladi. Nomlangan bog‘liqlik mavjud bo‘lmasa, o‘rnatilgan versiya mos kelmasa yoki asosiy loyiha nomi farq qilsa ham Publisher’ga manifest yozuvlaridan foydalanish imkonini beradi. Manifestdagi tarjimalar ilovangizga mos bo‘lsa, ushbu sozlamadan foydalaning.

`skip` sozlamasi `force` sozlamasidan ustun.

## Aqlli punktuatsiya {#smart-punctuation}

Nashr qilingan tarjimalarda tipografik qo‘shtirnoq, tire va ko‘p nuqtalarni yoqish uchun `LOCALIZATION_PUNCTUATION_ENABLED=true` ni belgilang. Umumiy qoidalar uchun `localization_punctuation.common` ni, ularni kanonik lokal kodi bo‘yicha almashtiradigan qoidalar uchun `localization_punctuation.locales` ni tahrirlang. Nashr qilingan satrlarga qoidalar Publisher ularni keyingi safar yozganda tatbiq etiladi.

## Lokal metama’lumotlari {#locale-metadata}

`localization_map.locales` kanonik kodlarni yozuv tizimi (`type`), hududiy lokal (`regional`) va ixtiyoriy yozuv yo‘nalishi (`direction`) bilan bog‘laydi. Yo‘nalish belgilanmagan bo‘lsa, `Direction::LeftToRight` dan foydalaning.

```php
use LaravelLang\LocaleList\Direction;

$metadata = config('localization_map.locales.ar', []);
$script = $metadata['type'];         // 'Arab'
$regional = $metadata['regional'];  // 'ar_AE'
$dir = ($metadata['direction'] ?? Direction::LeftToRight)->value; // 'rtl'
```
