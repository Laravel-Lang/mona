---
title: "Mona’ga yangilash"
description: "Laravel-Lang bog‘liqliklari, sozlamalar, nashr qilingan tarjimalar va ilova integratsiyalarini yangilang."
---

Mona — Laravel-Lang paketlari birga chiqariladigan repozitoriy. Ilovangizga kerak bo‘lgan alohida Composer paketlaridan foydalanishda davom eting.

:::info[Nega aynan 20-versiya?]

Laravel-Lang ekotizimi `laravel-lang/lang` paketidan boshlangan. Paketlar turli vaqtda yaratilgan va har biri o‘z versiya raqamlari bilan alohida chiqarilgan. Yirik yangilanishlarda bir nechta paketdagi o‘zgarishlarni muvofiqlashtirishimizga to‘g‘ri kelardi. Foydalanuvchilar esa qaysi versiyalarni birga o‘rnatish mumkinligini aniqlashlari kerak edi.

Tanlashni osonlashtirish uchun `laravel-lang/common` paketini yaratdik. U bog‘liqliklarning mos versiyalarini belgilardi. Shunga qaramay, bitta yangilanishda versiyalari alohida raqamlangan bir nechta paketni yangilashga to‘g‘ri kelishi mumkin edi.

Mona’ga o‘tishdan oldin paketlarning versiyalari quyidagicha edi. Laravel 11 va PHP 8.2 uchun ulardan qaysilarini o‘rnatgan bo‘lardingiz?

<details>
<summary>Mona’ga o‘tishdan oldingi paket versiyalari</summary>

| Paket | Versiya |
| --- | --- |
| Common | `6.9.0` |
| Actions | `1.13.2` |
| Attributes | `2.16.6` |
| Lang | `15.39.2` |
| MoonShine | `1.9.3` |
| HTTP Statuses | `3.13.2` |
| Currency Names | `1.11.0` |
| Locale Names | `2.10.0` |
| Country Names | `1.8.0` |
| Starter Kits | `1.17.4` |
| Locales | `2.11.0` |
| Models | `1.8.0` |
| Routes | `1.11.0` |
| JSON Fallback | `2.3.0` |
| Locale List | `1.7.0` |
| Config | `1.17.1` (Laravel-Lang’ning boshqa paketlari ishlatishda davom etgan 1.x turkumidagi versiya) va `2.3.2` (Mona’gacha chiqqan eng so‘nggi barqaror versiya, bu paketlarda kam qo‘llangan) |
| Publisher | `16.9.0` |

</details>

Endi paketlarni Mona monorepozitoriysidan bitta umumiy versiya raqami bilan birga chiqaramiz. Eng katta versiya raqami Publisher’da edi, shuning uchun keyingi versiya `17.0` bo‘lishi mumkin edi. Ekotizimni birlashtirish uchun qunt bilan ko‘p mehnat qildik va bu bosqichni nishonlash uchun 20-versiyani tanladik. Uni **Laravel-Lang 2.0** deb ham o‘qish mumkin. 🙂

:::

:::info[Lokal kodlari va ularning muqobil nomlari]

Laravel-Lang endi saqlanadigan qiymatlar uchun, jumladan nashr qilingan til fayllari yo‘llari va model tarjimasi satrlarida kanonik lokal kodlarini ishlatadi. Laravel’ning faol va zaxira lokallari ham kanonik kodlarni qo‘llaydi. Ilova ishlayotganda URL segmentlari va til tanlash vositalari uchun muqobil nomlardan foydalaning. Lokal kodlarini qaytaradigan fasad metodlari standart holatda sozlangan muqobil nomni, u sozlanmagan bo‘lsa, kanonik kodni ishlatadi. Masalan, `lang/pt_BR.json` ga nashr qilish va bazada `pt_BR` ni saqlash bilan birga `/pt-br/account` yo‘lini saqlab qolish uchun `localization_main.aliases` da `'pt_BR' => 'pt-br'` ni belgilang.

:::

## Bog‘liqliklarni yangilash {#updating-dependencies}

Joriy versiya Laravel 12 va 13 ni qo‘llab-quvvatlaydi hamda PHP `^8.3` ni talab qiladi. Ilovangizning `composer.json` faylida `require` yoki `require-dev` ichida bor quyidagi har bir paket uchun `^20.0` cheklovini belgilang. Faqat `laravel-lang/common` ni o‘rnatgan bo‘lsangiz, shu talabning o‘zini o‘zgartirish kifoya.

`20.x` ni o‘rnatish yoki unga yangilashdan oldin kerakli Composer paketi versiyasi Packagist’da mavjudligini tekshiring. Mona relizi va unga mos Composer paketlari turli vaqtda e’lon qilinishi mumkin.

| Vazifasi | Composer paketlari |
| --- | --- |
| To‘plam | `laravel-lang/common` |
| Tarjima paketlari | `laravel-lang/lang`, `laravel-lang/starter-kits`, `laravel-lang/moonshine`, `laravel-lang/actions`, `laravel-lang/attributes`, `laravel-lang/http-statuses` |
| Nashr qilish va sozlash | `laravel-lang/publisher`, `laravel-lang/config`, `laravel-lang/json-fallback`, `laravel-lang/library` |
| Ilova integratsiyalari | `laravel-lang/locales`, `laravel-lang/locale-list`, `laravel-lang/routes`, `laravel-lang/models` |
| Umumiy yordamchi paketlar | `laravel-lang/exceptions`, `laravel-lang/filesystem` |
| Paket andozasi | `laravel-lang/translations-template` |

Faqat standart tarjima jarayoni ishlatiladigan o‘rnatmada bog‘liqliklarni yangilang va tarjimalarni qayta nashr qiling.

```bash
composer update
php artisan lang:update
```

Provayderlarni ro‘yxatdan o‘tkazgan, maxsus sozlamalarni nashr qilgan bo‘lsangiz yoki ilovangizda muqobil nomlar, uchinchi tomon tarjima paketlari yoki paket API’laridan foydalansangiz, qayta nashr qilishdan oldin quyidagi tegishli bo‘limlarga amal qiling.

## Nomi o‘zgargan yoki eskirgan bog‘liqliklarni almashtirish {#replace-renamed-or-retired-dependencies}

`composer update` buyrug‘ini bajarishdan oldin `composer.json` faylidagi eski nom paketlariga bevosita bog‘liqliklarni yangilang.

| Oldingi Composer paketi | `^20.0` talabini belgilang |
| --- | --- |
| `laravel-lang/native-country-names` | `laravel-lang/country-names` |
| `laravel-lang/native-currency-names` | `laravel-lang/currency-names` |
| `laravel-lang/native-locale-names` | `laravel-lang/locale-names` |

Common allaqachon uchala nom paketiga ham bog‘liq. PHP kodingizni ham yangilang. [Nom qidirish API’larini almashtirish](#replace-the-name-lookup-apis) bo‘limiga qarang.

Har bir uchinchi tomon plagini uchun yangi Publisher’ga mos reliz mavjudligini tekshiring. `laravel-lang/translator` yoki `laravel-lang/status-generator` bog‘liqliklarini olib tashlashdan oldin ulardan foydalanayotgan kodni ko‘chiring. [Tarjima vositalari](#update-json-fallback-and-translation-tooling) bo‘limiga qarang.

## Xizmat provayderlarini yangilash {#update-service-providers}

Laravel Config, Publisher, Routes va Models provayderlarini paket manifestlaridan aniqlaydi. Avtomatik aniqlashdan foydalansangiz, eski provayder sinflariga murojaat qiladigan ilova kodi yoki sinovlarni yangilang. Ilovangiz paketlarni qo‘lda ro‘yxatdan o‘tkazsa, o‘sha provayder ro‘yxatida, masalan, `bootstrap/providers.php` yoki `config/app.php` faylida quyidagi almashtirishlarni qo‘llang.

- `LaravelLang\Config\ServiceProvider` → `LaravelLang\Config\LangConfigServiceProvider`
- `LaravelLang\Publisher\ServiceProvider` → `LaravelLang\Publisher\LangPublisherServiceProvider`
- `LaravelLang\Routes\ServiceProvider` → `LaravelLang\Routes\LangRoutesServiceProvider`
- `LaravelLang\Models\ServiceProvider` → `LaravelLang\Models\LangModelsServiceProvider`

`LaravelLang\Locales\LaravelLangLocalesServiceProvider` hamda `LaravelLang\Actions`, `LaravelLang\Attributes`, `LaravelLang\HttpStatuses`, `LaravelLang\Lang`, `LaravelLang\MoonShine` va `LaravelLang\StarterKits` ostidagi eski `ServiceProvider` sinflarining qo‘lda ro‘yxatdan o‘tkazilgan yozuvlarini olib tashlang. Bu tarjima paketlari o‘z manifestlari orqali aniqlanadi.

Zaxira JSON tarjimalaridan foydalansangiz, ro‘yxatdan o‘tkazgan joyingizda `LaravelLang\JsonFallback\TranslationServiceProvider` ni `LaravelLang\JsonFallback\LangJsonFallbackServiceProvider` ga almashtiring. Bu Laravel tarjima provayderining almashtirilgan holatini saqlaydi.

Paketlarni aniqlash ilovani ishga tushira olishi uchun `composer update` buyrug‘ini bajarishdan oldin eski provayderlarga havolalarni yangilang. Composer skripti hali ham eski sinfni yuklayotgani sababli ishlamasa, `composer update --no-scripts` buyrug‘ini bajaring, havolalarni yangilang va qayta aniqlash uchun `composer dump-autoload` buyrug‘ini bajaring.

## Sozlamalarni ko‘chirish {#migrate-configuration}

`config/localization.php` ni moslashtirgan bo‘lsangiz, o‘zgarishlarni ko‘chirayotganingizda ma’lumotnoma sifatida saqlang. Paketlar yangi standart qiymatlarni avtomatik yuklaydi. Tahrirlaydigan [sozlama fayllarini](./configure.md) nashr qiling. `localization` tegi beshtasini ham nashr qiladi. Bittasini nashr qilish uchun faylning `.php` kengaytmasisiz nomini teg sifatida ishlating.

```bash
php artisan vendor:publish --tag=localization
```

### Muqobil nomlar, tarjima uslublari va fayl formati {#aliases-translation-styles-and-file-formatting}

- `aliases` xaritangizni `config/localization_main.php` fayliga nusxalang. Masalan, `'de_CH' => 'de-CH'` kabi kanonik kodlarni kalit, muqobil nomlarni qiymat sifatida saqlang.
- `config/localization_main.php` faylida `inline` ni `styles` ga almashtiring. Standart tekshiruv xabarlari ishlatilgan oldingi xatti-harakatni saqlash uchun `'styles' => ['default']` ni belgilang. Maydon yonidagi xabarlardan foydalangan bo‘lsangiz, yangi standart `['default', 'inline']` ni saqlang. `LOCALIZATION_INLINE` va `LANG_PUBLISHER_INLINE` ni ularni belgilagan muhitlaringizdan olib tashlang.
- `align` kaliti hamda `LOCALIZATION_ALIGN` va `LANG_PUBLISHER_ALIGN` muhit o‘zgaruvchilarini olib tashlang. Nashr qilingan PHP massivlaridagi `=>` operatorlarini tekislash uchun loyihangizning PHP kod formatlagichini sozlang va fayllar nashr qilingach, uni til fayllariga qo‘llang. Masalan, Laravel Pint’ning `binary_operator_spaces` qoidasi `=>` ga `align_single_space_minimal` ni qo‘llashi mumkin.

### Aqlli punktuatsiya {#smart-punctuation}

Aqlli punktuatsiyani yoqqan bo‘lsangiz, `LOCALIZATION_SMART_ENABLED` nomini `LOCALIZATION_PUNCTUATION_ENABLED` ga o‘zgartiring va qiymatini `true` holida saqlang. PHP’da `smart_punctuation.enable` ni bevosita belgilagan bo‘lsangiz, mantiqiy qiymatni `config/localization_punctuation.php` faylining yuqori darajasidagi `enabled` kalitiga ko‘chiring. Punktuatsiya standart holatda o‘chirilgan.

`smart_punctuation.common` va `smart_punctuation.locales` dagi maxsus qo‘shtirnoqlarni shu faylning yuqori darajasidagi `common` va `locales` massivlariga ko‘chiring. `locales` kalitlari sifatida kanonik kodlardan foydalaning. Har bir lokal qo‘shtirnoqlari `common` dagi tegishli kalitlarni almashtiradi. `&nbsp;` ishlatilgan eski fransuzcha misolni nusxalagan bo‘lsangiz, qo‘shtirnoq satrlarida haqiqiy uzilmaydigan bo‘shliq yoki `"\u{00A0}"` ni ishlating.

### Mahalliylashtirilgan marshrutlar {#localized-routes}

Eski sozlamadagi `routes` ga kiritgan o‘zgarishlaringizni `config/localization_routes.php` fayliga ko‘chiring.

| Moslashtirilgan sozlama | `config/localization_routes.php` dagi o‘zgarish | Nomi o‘zgartiriladigan muhit o‘zgaruvchisi |
| --- | --- | --- |
| Lokal parametri, sarlavha, cookie, sessiya kaliti yoki foydalanuvchi modeli ustuni | `names` ga kiritgan o‘zgarishlaringizni nusxalang va qolgan standart yozuvlarni saqlang. | |
| Mahalliylashtirilgan marshrut nomlariga qo‘shiladigan prefiks | `name_prefix` ni `prefix` ga ko‘chiring. Qiymat bo‘sh bo‘lsa, `localized.` ishlatiladi. | `LOCALIZATION_NAME_PREFIX` → `LOCALIZATION_PREFIX` |
| Prefiksli marshrutdan yo‘naltirishlar | `redirect_default` ni `redirect` ga ko‘chiring. | `LOCALIZATION_REDIRECT_DEFAULT` → `LOCALIZATION_REDIRECT` |
| URL yordamchi funksiyasi zaxira lokalni yashirishi yoki yashirmasligi | `hide_default` ni `hide` ga ko‘chiring. | `LOCALIZATION_HIDE_DEFAULT` → `LOCALIZATION_HIDE` |
| Mahalliylashtirilgan marshrut guruhlariga qo‘llanadigan middlewarelar | Prefikssiz marshrutlar uchun `group.middlewares.default` ni `middlewares.default` ga, prefikslilar uchun `group.middlewares.prefix` ni `middlewares.prefix` ga ko‘chiring. Middleware sinf nomlarini [marshrutlarni ko‘chirish](#update-localized-routes-and-middleware) bo‘limida ko‘rsatilganidek yangilang. | |

### Model tarjimalari {#model-translations}

`models` ga kiritgan o‘zgarishlaringizni `config/localization_models.php` ga ko‘chiring. Maxsus `suffix` va `helpers` qiymatlarini yuqori darajaga nusxalang. Eski `filter.enabled` kaliti o‘rniga mantiqiy `filter` qiymatini ishlating. Filtrlash va IDE yordamchi fayllari yo‘lini muhit orqali boshqarsangiz, `LOCALIZATION_FILTER_ENABLED` va `VENDOR_PATH` ni saqlang.

Model buyruqlari endi standart holatda `app/Models` katalogini tekshiradi. Avval butun `app/` katalogini tekshirishga tayangan yoki `localization-private.models.directory` ni o‘zingiz belgilagan bo‘lsangiz, tekshirilishi kerak bo‘lgan har bir katalogni `directories` massiviga kiriting. Masalan, avvalgi qamrovni saqlash uchun `'directories' => [app_path()]` ni belgilang.

### Ilova kodi foydalanadigan sozlamalar {#configuration-used-by-application-code}

- `LaravelLang\Config\Facades\Config` orqali o‘qish va yozish amallarini yangi kalitlar bilan ishlaydigan Laravel sozlama chaqiruvlariga almashtiring. Masalan, `Config::shared()->models->filter->enabled` o‘rniga `config('localization_models.filter')` dan foydalaning. Tiplari ko‘rsatilgan o‘qishni afzal ko‘rsangiz, `LaravelLang\Config\Services\ModelLangConfig` ni bog‘liqlik sifatida uzating va uning `filter` xususiyatini o‘qing yoki marshrut sozlamalari uchun shu nomlar fazosidan `RouteLangConfig` ni uzating.
- Kodingiz lokal metama’lumotlarini `localization-private.map` dan o‘qisa, o‘rniga `localization_map.locales` ni o‘qing. Maxsus qiymatlarni `config/localization_map.php` faylidagi `locales` massiviga nusxalang.
- `localization-private.plugins` va `localization-private.packages` orqali ro‘yxatdan o‘tkazish o‘rniga [plagin manifestidan](./plugins.md) foydalaning.
- Eski `translators` blokini va faqat `laravel-lang/translator` ishlatgan eski `TRANSLATION_*` sozlamalarini olib tashlang.

O‘zgarishlaringizni ko‘chirib, ilovadagi murojaatlarni yangilagach, `config/localization.php` faylini olib tashlang va `php artisan config:clear` buyrug‘ini bajaring.

## Til fayllarini kanonik yo‘llarga ko‘chirish {#move-language-files-to-canonical-paths}

1. `APP_LOCALE` va `APP_FALLBACK_LOCALE` ni kanonik kodlarga sozlang. URL va lokal kiritishlari ishlashda davom etishi uchun muqobil nomlarni `localization_main.aliases` ga ko‘chiring.
2. `lang/pt-br.json` va `lang/pt-br/` kabi muqobil nomli fayl va kataloglarni `lang/pt_BR.json` hamda `lang/pt_BR/` ga o‘zgartiring. `lang/vendor/<package>/` ostida shunday muqobil nomlar borligini tekshiring. Ikkala nom ham mavjud bo‘lsa, muqobil yo‘lni olib tashlashdan oldin ularning mazmunini birlashtiring va to‘qnashgan maxsus tarjimalarni hal qiling.
3. Faqat bog‘liqliklarning PHP tarjimalari bilan ifodalangan har bir lokalni `php artisan lang:add <locale>` yordamida o‘rnating. O‘rnatilgan lokallar ro‘yxati til katalogi ostidagi bevosita yozuvlar, `lang/vendor/<package>/` ostidagi JSON fayllari hamda amaldagi joriy va zaxira lokallardan tuziladi.
4. Sozlamalar va plaginlarni ko‘chirgach, `php artisan lang:update` buyrug‘ini bajaring, so‘ng farqlarni ko‘rib chiqing. Publisher endi fayl nashr qilmaydigan integratsiyalar kalitlarini saqlab qolishi mumkin. Ilova hali ishlatayotgan kalitlarni saqlang va eskirganlarini ularga murojaat qiluvchi kodni tekshirgach olib tashlang. Fayllarni qayta yaratish yoki saqlash haqida [nashr skriptlari va maxsus tarjimalar](#update-publication-scripts-and-keep-custom-translations) bo‘limiga qarang.

## Integratsiyalarni yangilash {#update-integrations}

### Lokallar {#locales}

`LaravelLang\Locales\Facades\Locales` importini saqlang va har bir chaqiruvchi kod foydalanadigan ma’lumotga qarab chaqiruvlarni yangilang.

| Oldingi chaqiruv | Almashtirish |
| --- | --- |
| Obyekt qaytaradigan `Locales::available()`, `installed()` yoki `protects()` | Xuddi shu metodni `Locales::data()` orqali chaqiring. |
| `Locales::raw()->available()`, `installed()` yoki `protects()` | Muqobil nomi sozlangan bo‘lsa shu nomdan, aks holda kanonik koddan iborat satrlar kolleksiyasini olish uchun xuddi shu metodni bevosita `Locales` orqali chaqiring. Kanonik kodlar uchun `false` uzating va chaqiruvchi kodga massiv kerak bo‘lsa, `->all()` ni chaqiring. |
| `Locales::installed(false)` yoki `Locales::raw()->installed(false)` | `false` endi kanonik kodlarni tanlaydi. Joriy va zaxira lokallar til fayllari bo‘lmasa ham kiritiladi. Eski `withProtects: false` nomlangan argumentini ham almashtiring. Ularni chiqarib tashlash uchun `Locales::installed(false)->diff(Locales::protects(false))->values()` dan foydalaning. Obyektlar uchun `Locales::data()->installed()->reject(fn ($locale) => Locales::isProtected($locale))->values()` dan foydalaning. |
| `Locales::notInstalled()` yoki `Locales::raw()->notInstalled()` | Obyektlar uchun `Locales::data()->uninstalled()` dan, sozlangan muqobil nomlar yoki ular yo‘qligida kanonik kodlar ko‘rinishidagi satrlar uchun `Locales::uninstalled()` dan foydalaning. Kanonik kodli satrlarni olish uchun `false` uzating. |
| `Locales::getCurrent()` yoki `Locales::getDefault()` | DTO uchun `Locales::data()->current()` dan, satr uchun `Locales::current()` dan foydalaning. `Locales::raw()->getDefault()` ni `Locales::current()` ga almashtiring. Laravel saqlagan qiymat uchun `Locales::raw()->getCurrent()` ga tayangan bo‘lsangiz, `app()->getLocale()` dan foydalaning. |
| `Locales::getFallback()` yoki `Locales::raw()->getFallback()` | DTO uchun `Locales::data()->fallback()` dan, satr uchun `Locales::fallback()` dan foydalaning. Noma’lum zaxira lokal endi ingliz tiliga o‘giriladi. Noma’lum faol lokal amaldagi zaxira lokalga o‘giriladi. |
| `Locales::get($locale)` yoki `Locales::raw()->get($locale)` | `isInstalled()` dan foydalaning va zaxira lokalni aniq tanlang. Qo‘llab-quvvatlanadigan, lekin o‘rnatilmagan bo‘lishi mumkin bo‘lgan lokal uchun avval `isAvailable()`, keyin `info()` dan foydalaning. |
| `Locales::info($locale)` yoki `Locales::raw()->info($locale)` | `Locales::info($locale)` dan foydalaning va satr kerak bo‘lsa, `->alias` yoki `->code` ni o‘qing. Joriy lokal uchun `null` uzating. Noma’lum yoki bo‘sh kodda `LaravelLang\Exceptions\UnknownLocaleException` ni qayta ishlang yoki qidirishdan oldin `isAvailable()` ni tekshiring. |

O‘rnatilmagan yoki yaroqsiz kirish qiymatida joriy lokalga qaytadigan eski `get()` xatti-harakatini saqlash uchun tanlovni chaqiruvchi kodda bajaring.

```php
use LaravelLang\Locales\Facades\Locales;

$locale = Locales::acceptsInput($requested) && Locales::isInstalled($requested)
    ? Locales::info($requested)
    : Locales::data()->current();

$code = $locale->code;   // Use this in paths, database rows, and other persisted data.
$alias = $locale->alias; // Use this in URLs and language selectors.
```

Oldin `raw()->get()` natijasini `app()->setLocale()` ga uzatgan so‘rov ishlovchilarini yangilang. So‘rovdagi qiymatni qabul qilgach, `Locales::set($locale)` dan foydalaning. U o‘rnatilgan lokal kodlari, sozlangan muqobil nomlar, enum variantlari va yangi DTO’ni qabul qiladi hamda Laravel’ga kanonik kodni belgilaydi. So‘rov qiymati `pt-br` bo‘lsa, Laravel `pt_BR` ni oladi, `Locales::current()` esa `pt-br` ni qaytaradi. Kanonik kod kerak joylarda `Locales::current(false)` yoki `Locales::fallback(false)` ni chaqiring.

Ilovangizning boshqa joylarida qiymatni doimiy saqlash uchun uni `isAvailable()` yoki `isInstalled()` bilan tekshiring, so‘ng `fromAlias()` orqali kanonik kodini aniqlang. Kanonik kodni ko‘rsatishga moslash uchun `toAlias()` dan foydalaning. Eski `Services\Resolver` o‘rniga qabul qilinadigan PHP tiplarini tekshirish uchun `acceptsInput()` ni yoki tiplangan qiymatdan satr olish uchun `normalize()` ni ishlating. `normalize()` satrlarni, jumladan `pt-br` kabi muqobil nomlarni berilgan holida saqlaydi.

Importlar, tip ko‘rsatmalari va sinovlarda `LaravelLang\Locales\Data\LocaleData` ni `LaravelLang\LocaleNames\LocaleData` ga almashtiring. Yangi faqat o‘qiladigan obyektda `code`, `alias`, `native` va `localized` satr xususiyatlari bor. URL va interfeys qiymatlari uchun eski `code` o‘rniga `alias` ni ishlating. Oldin `$data->locale` o‘qilgan joyda `LaravelLang\LocaleList\Locale` ni import qilib, `Locale::from($data->code)` dan foydalaning. Andozalar yoki formatlagichlar ishlatsa, `type`, `regional` va `direction` qiymatlarini `config("localization_map.locales.{$data->code}", [])` dan o‘qing. Yetishmayotgan yozuvlar uchun o‘z standart qiymatlaringizni bering. Eski standartlarni saqlash uchun yozuvga `Latn`, hududga kanonik kod va yo‘nalishga `Direction::LeftToRight` qiymatini ishlating.

`withCountry`, `withCurrency`, `withCountries` va `withCurrencies` bayroqlarini olib tashlang. Lokal satri uchun mamlakat va valyutani alohida oling. Eski `LaravelLang\Locales\Data\CountryData` va `CurrencyData` tiplari importlarini tegishli nom paketlaridagi ma’lumot sinflariga almashtiring.

```php
use LaravelLang\CountryNames\CountryNames;
use LaravelLang\CurrencyNames\CurrencyNames;
use LaravelLang\Locales\Facades\Locales;

$locale = Locales::info('pt_BR');
$displayLanguage = Locales::current(false);

$country = app(CountryNames::class)->one($displayLanguage, $locale->code);
$currency = app(CurrencyNames::class)->one($displayLanguage, $locale->code);
```

Eski xizmatda `localizedLocales()`, `localizedCountries()` yoki `localizedCurrencies()` ni chaqirgan bo‘lsangiz, asl va mahalliylashtirilgan qiymatlar uchun tegishli nomlar xizmatida mos ravishda `all(null)` va `all($displayLanguage)` dan foydalaning. Avvalgi `Services\Locales` yoki `Services\RawLocales` sinflarini bevosita uzatish o‘rniga fasad yoki tegishli `Services\MainService` va `Services\DataService` sinflaridan foydalaning. Yangi xizmat API’lari uchun [Lokallar va nomlar](./locales.md) bo‘limiga qarang.

#### Lokal enumining yordamchi metodlari {#locale-enum-helpers}

`Locale` va `Direction` enum variantlari o‘z qiymatlarini saqlaydi. Maxsus lokal ro‘yxatlari yoki tanlash vositalarini yangilaganda `archtechx/enums` dan olib tashlangan yordamchi metodlarni almashtiring.

| Oldingi yordamchi metod | Almashtirish |
| --- | --- |
| `names()` | `array_column(Locale::cases(), 'name')` |
| `options()` | Nomdan qiymatga mos xaritani `array_column(Locale::cases(), 'value', 'name')` bilan tuzing. |
| `stringOptions()` | Ko‘rinishda `Locale::cases()` dan tanlov variantlarini yarating. Kanonik kod uchun enum variantining qiymatidan, ko‘rsatish uchun muqobil nom kerak bo‘lsa `Locales::toAlias($case)` dan foydalaning. |
| `fromName()` yoki `tryFromName()` | Variantni `Locale::cases()` ichidan `name` bo‘yicha toping va mos variant topilmagan holatni qayta ishlang. `pt_BR` kabi enum asosida yotgan skalyar qiymat uchun `Locale::from()` yoki `Locale::tryFrom()` dan foydalaning. |
| `Locale::onlyValues()` | Qiymatdan qiymatga mos xaritani `array_column(Locale::cases(), 'value', 'value')` bilan tuzing. Qiymatlar ro‘yxati uchun `Locale::values()` dan foydalaning. |
| `Direction::values()` | `array_column(Direction::cases(), 'value')` dan foydalaning. |

### Nom qidirish API’larini almashtirish {#replace-the-name-lookup-apis}

`LaravelLang\NativeCountryNames`, `LaravelLang\NativeCurrencyNames` va `LaravelLang\NativeLocaleNames` importlarini mos ravishda `LaravelLang\CountryNames`, `LaravelLang\CurrencyNames` hamda `LaravelLang\LocaleNames` ga almashtiring. `CountryNames`, `CurrencyNames` yoki `LocaleNames` ni Laravel konteyneridan oling yoki xizmatni sinfingizga bog‘liqlik sifatida uzating.

| Oldingi qo‘llanish | Almashtirish |
| --- | --- |
| `SomeNames::get($displayLocale)` | `$names->all($displayLocale)` |
| Asl tildagi nomlar uchun `SomeNames::get()` | `$names->all(null)` |
| `SortBy::None` | Tartiblash argumentini berib o‘tirmang. |
| `SortBy::Key` | Qaytarilgan kolleksiyada `->sortKeys()` ni chaqiring. |
| `SortBy::Value` | Mahalliylashtirilgan nom bo‘yicha tartiblash uchun `all()` ga `SortDirection::Ascending` ni uzating. |

Ma’lum yozuvni olish uchun `one($displayLocale, $entryLocale)` dan foydalaning. Qidiruv kaliti `fr` kabi lokaldir. Masalan, `app(CountryNames::class)->one('de', 'fr')` fransuz tiliga tegishli mamlakatni nemischa nom va `FR` ga teng `code` bilan qaytaradi. Mamlakat va valyuta DTO’lari paketlarining ildiz nomlar fazosida joylashgan, qidiruv kaliti ularda `locale` xususiyati orqali beriladi. `code` maydonlari esa hali ham mamlakat yoki valyuta kodini saqlaydi.

Uchala xizmat ham kanonik lokal kalitli kolleksiyalarni qaytaradi. Eski lokal nomlari paketidan satrlar massivi olgan bo‘lsangiz, har bir DTO’ni `localized` ga moslang. Eski mamlakat yoki valyuta DTO’larida `toArray()` ni chaqirgan yoki xususiyatlarini o‘zgartirgan bo‘lsangiz, ilova uchun massivni aniq tuzing. Yangi DTO’lar faqat o‘qiladi.

```php
use LaravelLang\LocaleNames\LocaleData;
use LaravelLang\LocaleNames\LocaleNames;

$options = app(LocaleNames::class)
    ->all('de', SortDirection::Ascending)
    ->map(fn (LocaleData $locale): string => $locale->localized)
    ->all();
```

`one()` ni chaqirishdan oldin foydalanuvchi tanlagan lokallarni tegishli katalog bo‘yicha tekshiring. Noma’lum ko‘rsatish lokali sababli `all()` bo‘sh ro‘yxat qaytarishi mumkin. Ilovaga asl tildagi nomlar kerak bo‘lsa, `null` uzating. Kodingiz maxsus asosiy qiymatli enumdan foydalangan bo‘lsa, uning satr qiymatini uzating. [Lokallar va nomlar](./locales.md)

### Mahalliylashtirilgan marshrutlar va middlewarelarni yangilash {#update-localized-routes-and-middleware}

Mahalliylashtirilgan URL yaratuvchi PHP fayllari va Blade andozalariga yordamchi funksiyani nomlar fazosi bilan import qiling. `\LaravelLang\Routes\localizedRoute()` ni bevosita ham chaqirishingiz mumkin.

```php
use LaravelLang\Locales\Facades\Locales;

use function LaravelLang\Routes\localizedRoute;

$url = localizedRoute('products.show', [
    'product' => 42,
    'locale' => Locales::current(),
]);
```

`Route::localizedGroup()` va `LocalizationRoute::group()` prefikssiz hamda lokal prefiksli marshrutlarni ro‘yxatdan o‘tkazadi. Mavjud guruhni saqlang va URL yordamchi funksiyasiga chaqiruvlarni tekshiring. Lokal parametri uzatilmasa, u zaxira lokalning muqobil nomidan foydalanadi. Til tanlash vositasida kerakli muqobil nomni `Locales::toAlias($locale)` yoki `$localeData->alias` orqali aniq uzating. Uzatilgan lokal satri o‘zgarishsiz ishlatiladi. Lokal parametrining nomini o‘zgartirgan bo‘lsangiz, massiv kalitiga `localization_routes.names.parameter` da sozlangan nomni qo‘ying.

Marshrutlaringiz, `bootstrap/app.php` va maxsus middleware ro‘yxatlaridagi import hamda ro‘yxatdan o‘tkazish yozuvlarini almashtiring. Jadvaldagi barcha sinflar `LaravelLang\Routes\Middlewares` nomlar fazosida.

| Oldingi middleware | Almashtirish |
| --- | --- |
| `LocalizationByCookie` | `LocalizationByCookieMiddleware` |
| `LocalizationByHeader` | `LocalizationByHeaderMiddleware` |
| `LocalizationByModel` | `LocalizationByModelMiddleware` |
| `LocalizationBySession` | `LocalizationBySessionMiddleware` |
| `LocalizationByParameter` | `LocalizationByParameterMiddleware` |
| `LocalizationByParameterWithRedirect` | `LocalizationByParameterWithRedirectMiddleware` |
| `LocalizationByParameterPrefix` | `LocalizationByParameterPrefixMiddlewareMiddleware` |

Model middlewareni autentifikatsiya mexanizmi bilan yaratgan bo‘lsangiz, uni Laravel middleware parametri sifatida uzating, masalan, `LocalizationByModelMiddleware::class . ':admin'`. Uning foydalanuvchini aniqlash vositasi Eloquent modelini kutadi. Maxsus hosila sinflarda `LaravelLang\Config\Services\RouteLangConfig` ni bog‘liqlik sifatida uzating, manba nomlarini `$this->routeConfig->name` dan o‘qing va `detect()` dan `int|string|null` qaytaring. `LaravelLang\Routes\Services\Route` ga bevosita murojaatlarni `LaravelLang\Routes\Services\RouteService` ga almashtiring.

Ilovangiz foydalanadigan har bir manbani sinang. Sarlavha orqali aniqlash `Accept-Language` dagi afzallik tartibiga ko‘ra o‘rnatilgan tillarni PHP Intl bilan tekshiradi. Avval to‘liq til tegi, keyin asosiy til sinab ko‘riladi. Keyingi middleware oldingi manba tanlagan lokalni almashtirishi mumkin. Prefiksli marshrutlarda URL lokalni belgilashi uchun ularning zanjirida faqat URL’dan lokal tanlaydigan middlewareni qoldiring.

URL yaratish va so‘rovlarni alohida sinang. `localization_routes.hide` URL yaratilganda zaxira lokal segmentini yashiradi. `localization_routes.redirect` joriy lokal uchun prefiksli so‘rovlarni prefikssiz marshrutga yo‘naltiradi. Nomlangan prefiksli marshrut yaroqsiz yoki o‘rnatilmagan lokalni ham prefikssiz marshrutga yo‘naltiradi.

`LocaleHasBeenSetEvent` tinglovchilarida kanonik kod uchun `$event->locale->code`, ko‘rsatish uchun `$event->locale->alias` dan foydalaning. Hodisa ma’lumoti endi `LaravelLang\LocaleNames\LocaleData` obyektidir. Middleware tanlangan lokal o‘zgarganda hodisani yuboradi va kanonik kodni sessiyada saqlaydi. Qo‘lda chaqirilgan `LocaleHasBeenSetEvent::dispatch($data)` ni `event(new LocaleHasBeenSetEvent($data))` ga almashtiring. [Mahalliylashtirilgan marshrutlar](./routes.md)

### Model tarjimasi kodi va ma’lumotlarini ko‘chirish {#migrate-model-translation-code-and-data}

Har bir tarjima jadvalining `locale` ustunida muqobil nomlar saqlangan satrlar borligini tekshiring. Har bir muqobil nom uchun ma’lumot migratsiyasida tegishli satrlarni kanonik kodga yangilang. Yangilashdan oldin ayni `item_id` uchun kanonik kodli satr mavjudligini tekshiring. Noyob `(item_id, locale)` chekloviga rioya qilish uchun saqlamoqchi bo‘lgan qiymatlaringizni bitta satrga birlashtiring. Eski treytning ayrim versiyalari allaqachon kanonik kodlarni yozgan, shuning uchun migratsiyada jadvallaringizda haqiqatda saqlangan qiymatlarga tayaning.

Importlar va yaratilgan sinflarni yangilang.

| Oldingi qo‘llanish | Almashtirish |
| --- | --- |
| `LaravelLang\Locales\Data\LocaleData` | `LaravelLang\LocaleNames\LocaleData` |
| `LaravelLang\Models\Casts\TrimCast` | `LaravelLang\Models\Casts\TrimStringCast` |
| `LaravelLang\Models\Exceptions\UnavailableLocaleException` | `LaravelLang\Exceptions\UnknownLocaleException` |
| `LaravelLang\Models\Exceptions\AttributeIsNotTranslatableException` yoki `UnknownModelPathException` | `LaravelLang\Exceptions` ichida xuddi shu nomdagi sinf. |
| `translationModelName()` | `translationModel(): string`. Maxsus tarjima sinfini tanlaydigan qayta belgilangan metodlarni yangilang. |
| `getTranslationTable()` | Jadval nomi bilan to‘liq ko‘rsatilgan ustun uchun `translationQualifyColumn($column)` dan, jadval nomi uchun `translationsRaw()->getRelated()->getTable()` dan foydalaning. |

`getTranslation()`, `setTranslation()`, `hasTranslated()` va `forgetTranslation()` lokal satri, `Locale` yoki yangi `LocaleData` ni qabul qiladi. Bu metodlarda foydalaniladigan har qanday aniq lokalni chaqiruvdan oldin o‘rnating. Aniq lokal berilmagan o‘qishda joriy qiymat `null` bo‘lsa, zaxira lokal ishlatiladi. Lokal aniq berilsa, aynan shu lokalning qiymati o‘qiladi. Quyi darajadagi `translation()` metodini chaqirsangiz, lokal satri yoki `null` uzating. DTO’ni `$locale->code` orqali o‘giring.

```php
$article->setTranslation('title', 'Schweizer Titel', 'de-CH')->save();
$article->translation('de_CH');
```

`de_CH` → `de-CH` muqobil nomi sozlangan va Shveysariya nemischasi o‘rnatilgan bo‘lsa, ikkala chaqiruv ham `de_CH` sifatida saqlangan satrga murojaat qiladi. Tarjima qilinadigan maydon uchun bir nechta qiymat to‘ldirganda lokal kalitli xarita uzating, masalan, `['en' => 'Title', 'de-CH' => 'Schweizer Titel']`. Indeksli ro‘yxatlar endi `InvalidLocaleInputException` istisnosini chiqaradi. Yuklangan `translations` munosabatini belgilaydigan maxsus kodga tarjima modellari Laravel kolleksiyasini uzatish tavsiya etiladi.

`translated()`, `whereTranslation()` va tegishli so‘rov doiralarini Eloquent so‘rov tuzuvchisi orqali chaqiring. Doiraning lokal satri sifatida kod yoki muqobil nom uzating. `orderByTranslation()` uchun `'asc'`, `'desc'` yoki `SortDirection` dan foydalaning. `whereTranslation()` yoki `whereTranslationLike()` orqali joriy va zaxira lokallardan boshqa lokalni so‘ranganda, `localization_models.filter` ni `false` qilib belgilang yoki bevosita `translationsRaw()` ga so‘rov yuboring.

`$event->locale->value` ni chaqiradigan yoki `$event->locale` ni `Locale` enumi bilan solishtiradigan hodisa ishlovchilarini yangilang. `TranslationHasBeenSetEvent` va `TranslationHasBeenForgetEvent` endi aniq lokal uchun satr ko‘rinishidagi muqobil nomni olib yuradi. Chaqiruvchi lokalni bermasa, qiymat belgilash hodisasida `null` bo‘ladi. Muqobil nomni saqlanadigan kalit sifatida ishlatishdan oldin `Locales::fromAlias()` bilan o‘giring. Qiymat belgilash hodisasi atributga qiymat tayinlanganda, `save()` dan oldin yuz beradi. Model tarjimasiga oid uchta hodisadan birortasini qo‘lda yuborish uchun `event(new …)` dan foydalaning.

Yaratilgan IDE yordamchi fayllarini saqlasangiz, importlar va modellarni aniqlash yo‘llarini yangilagach, `php artisan lang:models-helper` buyrug‘ini bajaring. [Model tarjimalari](./models.md)

### Nashr skriptlarini yangilash va maxsus tarjimalarni saqlash {#update-publication-scripts-and-keep-custom-translations}

| Oldingi buyruq yoki taxmin | Ko‘chirish tartibi |
| --- | --- |
| `lang:add <locale>` faqat qo‘shilayotgan lokallarni yangilagan | Endi barcha o‘rnatilgan lokallarni yangilaydi. Skriptlarda kodlarni aniq uzating va mavjud lokal fayllari ham yangilanishini hisobga oling. |
| `lang:reset` | Birlashtirish uchun `lang:update` dan foydalaning. Noldan qayta yaratish uchun maxsus yozuvlarni zaxiralang, qayta yaratmoqchi bo‘lgan fayllarni o‘chirib, `lang:add` ga kerakli lokallar kodlarini aniq uzating. |
| `lang:rm <locale> --force` | `lang:rm <locale>` dan foydalaning. Laravel’ning joriy va zaxira lokallari himoyalangan. |
| Faqat `lang:rm` chiqish kodini tekshirish | Har bir natijani tekshiring. `DONE` o‘chirilganini tasdiqlaydi, `FAILED` esa urinishdan keyin fayl yoki katalog qolganini bildiradi. `PROTECTED`, `INVALID` va `SKIP` muvaffaqiyatli chiqish kodi bilan ham qaytishi mumkin. |

Artisan’ni buyruq sinfi orqali chaqirsangiz, `LaravelLang\Publisher\Console\Add`, `Remove` va `Update` ni mos ravishda `AddCommand`, `RemoveCommand` va `UpdateCommand` ga almashtiring. Maxsus matn dekoratori uchun `Contracts\TextDecorator` o‘rniga `LaravelLang\Publisher\Contracts\Decorator` ni amalga oshiring va konteynerga bog‘lang. Uning `convert(string $locale, string $value): string` metodi kanonik lokalni oladi.

Ilk `lang:update` ni mavjud til fayllaringiz versiya nazoratida turganda bajaring. Publisher mavjud maqsad fayllariga yozuvlarni birlashtiradi va mos kalitlarni, jumladan, sonli kalitlarni almashtiradi. Imkon bo‘lsa, maxsus tarjimalarni alohida ilova tarjima faylida saqlang. Yaratiladigan butun maqsad faylini o‘zingiz yuritish uchun uning yo‘l andozasini `localization_main.skip` ga qo‘shing. Manba iboraga qaytgan har qanday qiymatni tekshiring. Lug‘atlarda tarjima bo‘lmasa, Publisher ushbu iborani ishlatadi.

Avvalgi yangilanishda `http-statuses.php` fayllaridagi kalitlar qayta raqamlangan bo‘lsa, tuzatish talab qilinadigan fayllardan zaxira nusxa oling va ularni o‘chiring. Muammoni chetlab o‘tish uchun `localization_main.skip` ga `'*/http-statuses.php'` ni qo‘shgan bo‘lsangiz, shu yozuvni olib tashlang. Fayllarni qayta yaratish uchun `php artisan lang:update` buyrug‘ini bajaring, keyin maxsus tarjimalarni tegishli holat kodlari bilan tiklang. [Tarjimalarni boshqarish](./translations.md)

### Eski plagin ro‘yxatdan o‘tkazish usulini almashtirish {#replace-legacy-plugin-registration}

Uchinchi tomon tarjima paketidan foydalansangiz, uning manifestga asoslangan reliziga yangilang. Paketga xizmat ko‘rsatsangiz, eski `LaravelLang\Publisher\Plugins\Provider` ni ro‘yxatdan o‘tkazadigan provayderni va Composer aniqlash yozuvini olib tashlang. Provayder boshqa Laravel xizmatlarini ham sozlagan bo‘lsa, bu ro‘yxatdan o‘tkazishlarni odatdagi Laravel xizmat provayderida saqlang.

1. `vendor/` ostiga o‘rnatiladigan paket ildiziga `laravel_lang.json` qo‘shing. Har bir `Plugin::files()` yozuvini paketning `source/` katalogiga nisbatan `source` yo‘liga va ilovaning til katalogiga nisbatan `target` yo‘liga aylantiring. Maqsad yo‘lida `{locale}` dan foydalaning.
2. Composer moslash qoidalarini manifestga ko‘chiring. O‘rnatilgan bog‘liqlik uchun `vendor` va `version` dan foydalaning, ildiz Composer loyihasi uchun esa `project: true` li alohida yozuv yarating. Eski plagin `$with_project_name = true` dan foydalangan va ikkala rejimni ham qo‘llab-quvvatlagan bo‘lsa, ikkala yozuvni yarating.
3. Lug‘atlarni `locales/<code>/<style>.json` ko‘rinishiga o‘tkazing. Manba fayllaridagi qiymatlar lug‘at qidiruv kalitlaridir. Masalan, `'enable' => 'Enable audit trail'` PHP yozuvi `{locale}/audit.php` ga nashr qilinganda hali ham `audit.enable` ilova kalitini yaratadi. Lug‘atda uning tarjimasini `"Enable audit trail"` kaliti ostida saqlang.
4. Mona’ning mahalliy nusxasidan quyidagi Toolkit buyrug‘ini eski paketga nisbatan bajaring. U eski JSON va PHP lug‘atlaridan `default.json` hamda `inline.json` yaratadi va lokal istisnolarini ko‘chiradi. Natijani tekshiring va uni manifest hamda manba fayllari bilan yangi paketga qo‘shing. Muqobil nomli lug‘at kataloglarini kanonik kodlarga o‘zgartiring.
5. Paketning Composer manifestida joriy Publisher’ni bog‘liqlik sifatida belgilang. Paketingiz umumiy lug‘atdan tarjima olishni kutsa, `laravel-lang/library` ni ham qo‘shing. Natijadagi paketni ilovaga o‘rnating va nashr qilingan natijani tekshirish uchun `php artisan lang:update` ni bajaring.

```bash
php src/Toolkit/bin/toolkit migrate-locales path/to/legacy-pack --target=path/to/new-pack/locales
```

Mavjud ilovalarda yangilashdan keyin ichki paketlarning qaysi qoidalari qo‘llanishini tekshiring.

- Spark Paddle, Spark Stripe, Nova Dusk Suite yoki Nova Log Viewer uchun ilgari nashr qilingan Laravel-Lang tarjimalari hali ham kerak bo‘lsa, kalitlarni ilovaga tegishli fayllarda saqlang yoki eski manba va lug‘atlarni maxsus paketga ko‘chiring.
- Starter Kit tarjimalari endi loyihaning Composer `name` qiymatiga moslanadi. Loyiha nomini o‘zgartirgan yoki to‘plamni bog‘liqlik sifatida ishlatayotgan bo‘lsangiz, tarjimalarini nashr qilish uchun to‘plamning Composer nomini, masalan, `laravel/livewire-starter-kit` ni `localization_main.force` ga qo‘shing. [Tarjima plaginlari](./plugins.md)

### Zaxira JSON tarjimalari va tarjima vositalarini yangilash {#update-json-fallback-and-translation-tooling}

Ilova kodi `LaravelLang\JsonFallback\Translator` ni import qilsa yoki tip sifatida ishlatsa, tipni `LaravelLang\JsonFallback\OverridedTranslator` ga almashtiring yoki Laravel’ning tarjimon kontraktiga tayaning. Eski tarjimon uchun hosila sinf yaratgan bo‘lsangiz, maxsus xatti-harakatni ilova tarjimoni yoki o‘z tarjima xizmatingizga ko‘chiring. Kalit topilmasa, yangi tarjimon Laravel’ning tarjima topilmaganda ishlaydigan qayta chaqiruv funksiyasini chaqiradi. Ilovangiz bunday funksiyani ro‘yxatdan o‘tkazsa, uni ko‘rib chiqing. Zaxira JSON’da ham, joriy til PHP faylida ham mavjud kalitlarni tekshiring, chunki zaxira JSON birinchi tekshiriladi. [Zaxira JSON tarjimalari](./translations.md#json-fallback)

Mashina tarjimasi uchun alohida `laravel-lang/translator` paketidan foydalansangiz, uni bog‘liqliklardan olib tashlashdan avval uning `Translate` fasadidan foydalanadigan joylar hamda Google, DeepL yoki Yandex integratsiyalarini tanlagan tarjima xizmatingizga ko‘chiring. Bu skriptlar yaratgan tarjimalarni ilova yoki paket lug‘atlarida saqlang.

Paket kataloglarini `laravel-lang/status-generator` dagi `vendor/bin/lang` bilan yuritsangiz, `composer install` dan keyin yangi buyruqlarni Mona’ning mahalliy nusxasidan bajaring.

| Oldingi skript | Ishlatiladigan Toolkit skripti |
| --- | --- |
| `vendor/bin/lang status --path=path/to/pack` | `php src/Toolkit/bin/toolkit status path/to/pack`. Hosil bo‘lgan `locales/<code>/_status.json` hisobotlarini o‘qing. |
| `vendor/bin/lang sync --path=path/to/pack` | `php src/Toolkit/bin/toolkit sync:package path/to/pack`. Umumiy Library’ni yuritish uchun `sync:library` dan foydalaning. |
| `vendor/bin/lang create` | `php src/Toolkit/bin/toolkit create-locale path/to/pack`. Bu ma’lum lokallar uchun kataloglar yaratadi. |
| `vendor/bin/lang download` | ZIP URL manzilini `--url` bilan, to‘liq maqsad yo‘lini `--directory` bilan va arxivdagi kerakli yo‘llarni takrorlanadigan `--paths` bilan uzating. |
| `vendor/bin/lang translate` | Mashina tarjimasini alohida xizmatga ko‘chiring. Hanuz manba iboraga mos saqlangan satrlarni topish uchun Toolkit `status` buyrug‘ini bajaring. |

To‘liq misollar uchun [Toolkit buyruqlariga](./toolkit.md) qarang.

## Yangilashni tekshirish {#verify-the-upgrade}

Ilovangiz sinovlarini ishga tushiring. Har bir muhim lokaldagi nashr qilingan xabarni, zaxira tarjimani, sozlangan muqobil nomli mahalliylashtirilgan URL’ni va kanonik kod ostida saqlangan model tarjimasini tekshiring. Yaratilgan fayllar va saqlangan lokal qiymatlarini manba kodidagi o‘zgarishlar bilan birga ko‘rib chiqing.
