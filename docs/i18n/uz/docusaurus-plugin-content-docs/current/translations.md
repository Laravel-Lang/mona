---
title: "Tarjimalarni boshqarish"
description: "Mona Publisher yordamida ilova va bog‘liqlik tarjimalarini qo‘shing, yangilang va olib tashlang."
---

Publisher rasmiy tarjima paketlari tarkibiga kiradi. Uning Artisan buyruqlari PHP va JSON tarjimalarini ilovaning til katalogiga, odatda `lang/` ga yozadi. Ayrim paket fayllari `lang/vendor/` ostiga ham yozilishi mumkin.

Artisan buyruqlarini ilovaning asosiy katalogidan ishga tushiring. `lang_path()` tarjimalar katalogini belgilaydi.

## Lokallar qo‘shish {#add-locales}

Bitta yoki bir nechta [lokal kodini](./available-locales.md) bo‘sh joy bilan ajratib uzating. Sozlangan muqobil nomlar kirishda qabul qilinadi, hosil bo‘lgan fayllarda esa kanonik kodlar ishlatiladi.

```bash
php artisan lang:add fr de_CH
```

Buyruq lokal kataloglarini yaratadi, so‘ng **barcha o‘rnatilgan lokallarni**, jumladan ilovaning joriy va zaxira lokallarini yangilaydi. Argumentlarsiz u barcha qo‘llab-quvvatlanadigan lokallarni o‘rnatish kerakligini so‘raydi. Avtomatlashtirishda kodlarni aniq uzating.

Noma’lum kod berilsa, buyruq kataloglarni yaratishdan oldin xato bilan to‘xtaydi.

## Tarjimalarni yangilash {#update-translations}

```bash
php artisan lang:update
```

`lang:update` barcha o‘rnatilgan lokallarni yangilaydi va argument sifatida lokal kodlarini olmaydi.

Publisher lokallarni ilovaning joriy va zaxira lokali, til katalogi ildizidagi fayl yoki kataloglar hamda `lang/vendor/<package>/` ostidagi JSON fayllaridan aniqlaydi. Bog‘liqlikning PHP tarjima katalogi o‘zi lokalni o‘rnatilgan deb belgilash uchun yetarli emas. Ilova katalogini yaratish uchun o‘sha lokalga `lang:add` buyrug‘ini qo‘llang.

Tarjima paketlari qo‘llab-quvvatlanadigan paket va versiyalarni, shuningdek maqsad fayllarini [plagin manifestlarida](./plugins.md) e’lon qiladi. Publisher umumiy va paket lug‘atlarini birlashtiradi, so‘ng tarjimalarni har bir maqsad fayliga qo‘shadi. U aloqasiz kalitlarni saqlaydi va mos keladigan kalitlarning qiymatini almashtiradi, bu sonli kalitlarga ham tegishli. Lug‘atlarda ibora topilmasa, Publisher manba iborani yozadi. O‘zingizga xos almashtirishlarni alohida tarjima fayllarida saqlang yoki mustaqil yuritadigan maqsad fayllarini [nashrdan chiqarib tashlang](./configure.md#skip-files-and-force-integrations).

Yaratilgan PHP tarjima fayllari yuklanganda `404` kabi sonli kalitlar saqlanadi. Bo‘sh bo‘lmagan ichma-ich PHP massivining har bir elementi alohida satrga yoziladi. JSON tarjima lug‘atlari barcha darajalarda satrli kalitlarga ega obyektlar sifatida yoziladi, sonli kalitlar ketma-ket bo‘lganda ham.

Ilovaning `validation.php` fayllarida Publisher yuqori darajadagi `custom` va `attributes` kalitlari mavjud bo‘lsa, ularni shu tartibda fayl oxiriga joylashtiradi. Yuqori darajadagi qolgan kalitlar alifbo tartibida saralanadi.

Composer bog‘liqliklari yoki [nashr sozlamalari](./configure.md) o‘zgargach, `lang:update` buyrug‘ini bajaring. Hosil bo‘lgan farqlarni ko‘rib chiqing.

Loyihaning yangi nusxasida tarjimalarni nashr qilishdan oldin `composer install` buyrug‘ini bajaring. Yaratilgan fayllarni ilova bilan birga joylashtiring yoki yozish mumkin bo‘lgan tarjima katalogida joylashtirish vaqtida yarating. Fayllar hali yo‘q bo‘lsa, kerakli kodlar bilan `php artisan lang:add fr de_CH --no-interaction` buyrug‘ini bajaring. Mavjud fayllarni `php artisan lang:update --no-interaction` bilan yangilang.

## Lokallarni olib tashlash {#remove-locales}

```bash
php artisan lang:rm de_CH
```

Kanonik kodlarni yoki sozlangan muqobil nomlarni kiriting.

Buyruq kanonik lokal katalogi va JSON faylini til katalogi ildizidan hamda har bir bevosita `lang/vendor/<package>/` katalogidan o‘chiradi. O‘chiriladigan lokal kataloglari ichidagi maxsus fayllar ham o‘chadi. Joriy va zaxira lokallar himoyalangan. Argumentlarsiz buyruq barcha himoyalanmagan lokallarni olib tashlash kerakligini so‘raydi. Standart javob — yo‘q. Lokal katalogi havola bo‘lsa, buyruq havolaning o‘zini o‘chiradi.

Buyruq har bir lokal uchun `DONE`, `PROTECTED`, `INVALID`, `SKIP` yoki `FAILED` holatini bildiradi. O‘chirishga urinishdan keyin fayl yoki katalog qolsa, buyruq uning yo‘lini ko‘rsatadi va xato chiqish kodini qaytaradi. Har bir lokal holatini tekshiring, chunki `PROTECTED`, `INVALID` va `SKIP` muvaffaqiyatli chiqish kodi bilan ham qaytishi mumkin.

## Artisan buyruqlarini topish {#find-the-artisan-commands}

`php artisan help lang:add` orqali buyruq ro‘yxatdan o‘tganini tekshiring. Buyruq topilmasa, paket o‘rnatilganini `composer show laravel-lang/publisher` orqali tekshiring va `php artisan package:discover` buyrug‘ini bajaring. Config va Publisher paketlarini avtomatik aniqlash o‘chirilgan bo‘lsa, `LaravelLang\Config\LangConfigServiceProvider::class` va `LaravelLang\Publisher\LangPublisherServiceProvider::class` klasslarini `bootstrap/providers.php` faylida ro‘yxatdan o‘tkazing.

## Zaxira JSON tarjimalari {#json-fallback}

`laravel-lang/json-fallback` JSON kaliti topilmaganda PHP tarjimalariga o‘tishdan oldin zaxira lokalning JSON faylidan qidirishni qo‘shadi. Paket Common tarkibiga kiradi. Alohida o‘rnatish uchun `composer require laravel-lang/json-fallback` buyrug‘ini bajaring.

`LaravelLang\JsonFallback\LangJsonFallbackServiceProvider::class` ni `bootstrap/providers.php` faylidagi ilova provayderlariga qo‘shing. Ilovada `Illuminate\Translation\TranslationServiceProvider` aniq ko‘rsatilgan bo‘lsa, ushbu yozuvni Mona provayderiga almashtiring. [Laravel provayderlarini ro‘yxatdan o‘tkazish](https://laravel.com/docs/13.x/providers#registering-providers)

Joriy lokal fransuz tili, zaxira lokal esa ingliz tili bo‘lsa, `lang/fr.json` da yo‘q kalit uchun endi `lang/en.json` dagi qiymat qaytishi mumkin.

Qidiruvni so‘ralgan lokal bilan cheklash uchun `Illuminate\Support\Facades\Lang` fasadi orqali `Lang::get($key, fallback: false)` chaqiruvini ishlating.
