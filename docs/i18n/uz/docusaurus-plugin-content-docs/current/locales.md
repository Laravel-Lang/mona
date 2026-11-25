---
title: "Lokallar va nomlar"
description: "Qo‘llab-quvvatlanadigan hamda o‘rnatilgan lokallarni toping, muqobil nomlarni kanonik kodga o‘giring va mahalliylashtirilgan nomlarni izlang."
---

Laravel-Lang Laravel’da, tarjima fayllari nomlarida va model tarjimasi satrlarida `en`, `fr` hamda `pt_BR` kabi kanonik kodlarni ishlatadi. Lokalning muqobil nomi ilovada ko‘rinadigan nomdir, masalan, URL’da `pt-br`. [`localization_main.aliases`](./configure.md) sozlamasida kanonik kodlarni muqobil nomlarga moslang.

## Qo‘llab-quvvatlanadigan va o‘rnatilgan lokallarni topish {#find-supported-and-installed-locales}

`laravel-lang/locale-list` paketi asosiy qiymatga ega `LaravelLang\LocaleList\Locale` enumini taqdim etadi. Uning variantlari [qo‘llab-quvvatlanadigan kanonik kodlarga](./available-locales.md) mos keladi.

```php
use LaravelLang\LocaleList\Locale;

Locale::French->value;        // 'fr'
Locale::tryFrom('pt_BR');     // Locale::PortugueseBrazil
Locale::values();            // array of canonical codes
Locale::collection();        // Collection of canonical codes
Locale::collection(false);   // Collection of enum cases
```

`laravel-lang/locales` ilova sozlamalarini hisobga oladigan `LaravelLang\Locales\Facades\Locales` fasadini qo‘shadi. Bu metodlar satrlar kolleksiyasini qaytaradi. Kanonik kodlarni olish uchun argument sifatida `false` uzating. Standart holatda mavjud bo‘lsa, har bir kod o‘zining sozlangan muqobil nomi bilan ifodalanadi.

| Metod | Qaytaradigan qiymat |
| --- | --- |
| `Locales::available()` | Barcha ma’lum lokallar |
| `Locales::installed()` | Ilova til fayllaridan aniqlangan va himoyalangan lokallar |
| `Locales::uninstalled()` | O‘rnatilmagan mavjud lokallar |
| `Locales::protects()` | Takrorlari olib tashlangan amaldagi joriy va zaxira lokallar |

Aniqlash jarayoni `lang_path()` katalogining bevosita tarkibini va shu yo‘lga nisbatan `vendor/*/*.json` andozasiga mos JSON fayllarini o‘qiydi. Fayl yoki katalog nomi qo‘llab-quvvatlanadigan kodga mos kelishi kerak. `lang/vendor/<package>/` ostidagi faqat PHP tarjimalari bor kataloglar bu andozalarga kirmaydi. Lokalni qanday nashr qilishni [tarjimalarni boshqarish](./translations.md) bo‘limidan bilib oling.

## Ilova lokalini o‘qish yoki belgilash {#read-or-set-the-application-locale}

```php
use LaravelLang\Locales\Facades\Locales;

if (Locales::isInstalled('fr')) {
    Locales::set('fr');
}

$canonicalCode = Locales::current(false);
$displayCode = Locales::current();
```

`set()` kod, muqobil nom, `Locale` enum varianti yoki `LocaleData` obyektini qabul qiladi. Lokal o‘rnatilgan bo‘lsa, metod Laravel lokalini kanonik kodga yangilaydi. Kod qo‘llab-quvvatlanishini tekshirish uchun `isAvailable()` dan, fayllar yoki himoya talab qilinsa `isInstalled()` dan foydalaning. `isProtected()` joriy va zaxira lokallarni aniqlaydi.

`current()` Laravel joriy lokalini tanisa, undan, aks holda `fallback()` dan foydalanadi. `fallback()` Laravel’da sozlangan zaxira lokalni aniqlaydi va u noma’lum bo‘lsa, `en` ni qaytaradi. Har ikki metod kanonik kodlar uchun bir xil `false` argumentini qabul qiladi.

Muqobil nomni kanonik kodga o‘girish uchun `fromAlias()` dan, kanonik kodga mos muqobil nomni olish uchun `toAlias()` dan foydalaning. Bu o‘girishlar tanilmagan bo‘sh bo‘lmagan satrlarni o‘zgartirmaydi. Kirishni tekshirishda `isAvailable()` dan foydalaning.

## Lokal tanlovlarini ko‘rsatish {#display-locale-choices}

`Locales::info('fr')` `code`, `alias`, `native` va `localized` xususiyatlariga ega `LaravelLang\LocaleNames\LocaleData` obyektini qaytaradi. Ko‘rsatish tili ilovaning joriy lokalidir. Joriy lokalni olish uchun argument bermang. Ma’lum lokal o‘rnatilmasidan oldin ham uni izlash mumkin. Noma’lum kod `LaravelLang\Exceptions\UnknownLocaleException` istisnosini chiqaradi.

O‘rnatilgan lokallarni `LocaleData` obyektlari sifatida olish uchun `Locales::data()->installed()` dan foydalaning. `data()` boshqa kolleksiyalar uchun `available()`, `uninstalled()` va `protects()`, alohida obyektlar uchun `current()` va `fallback()` metodlarini taqdim etadi. Til tanlagich `alias` qiymatini yuborib, `native` nomini ko‘rsatishi mumkin.

## Lokal, mamlakat va valyuta nomlarini izlash {#look-up-locale-country-and-currency-names}

`laravel-lang/locale-names`, `laravel-lang/country-names` va `laravel-lang/currency-names` paketlarining har biri Laravel konteyneridan bog‘liqlik sifatida uzatish mumkin bo‘lgan xizmatni taqdim etadi. Common uchtalasini ham o‘z ichiga oladi.

```php
use LaravelLang\CountryNames\CountryNames;
use LaravelLang\CurrencyNames\CurrencyNames;
use LaravelLang\LocaleNames\LocaleNames;

$locale = app(LocaleNames::class)->one('de', 'fr');
$locale->localized; // 'Französisch'

$country = app(CountryNames::class)->one('de', 'fr');
$country->code;      // 'FR'
$country->localized; // 'Frankreich'

$currency = app(CurrencyNames::class)->one('de', 'fr');
$currency->code;    // 'EUR'
$currency->numeric; // 978

$nativeLocaleNames = app(LocaleNames::class)->all(null, \SortDirection::Ascending);
```

Birinchi parametr — ko‘rsatish tili. Asl tildagi nomlar uchun `null` dan foydalaning. `one()` metodining ikkinchi parametri satrga tegishli lokal, masalan, yuqoridagi misollarda `fr`. Mamlakat va valyuta nomlarini izlashda ISO kodi qaytarilgan obyektda bo‘ladi. Har ikki parametr kanonik kodlar, sozlangan muqobil nomlar va `Locale` enum variantlarini qabul qiladi.

`all($forLocale)` kanonik lokal kodlari bilan indekslangan kolleksiyani qaytaradi. `localized` xususiyati bo‘yicha tartiblash uchun `SortDirection::Ascending` yoki `SortDirection::Descending` ni uzating. Har bir qiymat faqat o‘qiladigan obyektdir. Mamlakat va valyuta qiymatlarida satrning `locale` xususiyati ham bo‘ladi.
