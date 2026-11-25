---
title: "Model tarjimalari"
description: "Laravel-Lang Models bilan mahalliylashtirilgan Eloquent atributlarini saqlang, o‘qing va so‘rovlar orqali oling."
---

`laravel-lang/models` mavjud Eloquent modeliga tarjima qilinadigan atributlarni qo‘shadi. Har bir tarjima satrida lokal va shu lokalga tegishli atribut qiymatlari saqlanadi. Paket Common tarkibiga kiradi va PHP Intl’ni talab qiladi.

```bash
composer require laravel-lang/models
```

## Modelga tarjimalar qo‘shish {#add-translations-to-a-model}

Asosiy jadvali uchun migratsiyasi bor mavjud `App\Models\Article` modeli uchun quyidagini bajaring:

```bash
php artisan make:model-translation 'App\Models\Article' --columns=title --columns=description
php artisan migrate
```

Generator asosiy modelga `LaravelLang\Models\HasTranslations` treytini qo‘shadi hamda unga hamroh `ArticleTranslation` modeli, migratsiya va IDE yordamchi faylini yaratadi. Migratsiya maqolaga bog‘lovchi tashqi kalit, noyob `(item_id, locale)` juftligi va tarjima qiymatlari uchun null bo‘lishi mumkin bo‘lgan satr ustunlarini yaratadi. Bu qiymatlar asosiy jadvalda saqlanayotgan bo‘lsa, yangi model o‘qish metodlariga tayanishdan oldin ularni tarjima satrlariga ko‘chiring.

Model `app/Models` dan tashqarida bo‘lsa, generatorni ishga tushirishdan oldin uning katalogini [`localization_models.directories`](./configure.md) sozlamasiga qo‘shing.

Model va ustunlarni interaktiv tanlash uchun `php artisan make:model-translation` ni bajaring. Ro‘yxat bo‘sh bo‘lsa, generator `title` va `description` ni ishlatadi. Keyinroq maydon qo‘shish uchun yangi migratsiya yarating va maydonni tarjima modelining to‘ldiriladigan atributlari va o‘girish qoidalariga kiriting.

## Tarjima qilingan atributlarni yozish va o‘qish {#write-and-read-translated-attributes}

[Fransuz tili o‘rnatilgan](./translations.md) bo‘lsa, uning kanonik kodi, muqobil nomi, `Locale` enum varianti yoki `LocaleData` obyektini uzatib tarjimani yozishingiz mumkin. O‘zgarishlarni saqlash uchun asosiy modelning `save()` metodidan foydalaning.

```php
use App\Models\Article;
use LaravelLang\LocaleList\Locale;

$article = Article::create(['title' => 'First article']);
$article->setTranslation('title', 'Premier article', Locale::French)->save();

$article->title;                         // current locale, then fallback for a null value
$article->getTranslation('title', 'fr');  // 'Premier article'
```

Lokal aniq berilsa, aynan shu lokalning qiymati o‘qiladi. Lokal argumenti berilmasa, yozish joriy lokalda bajariladi, o‘qishda esa joriy qiymat `null` bo‘lsa, Laravel’da sozlangan zaxira lokal ishlatiladi. Qidirish va saqlashda muqobil nomlar kanonik kodlarga o‘giriladi. Noma’lum yoki o‘rnatilmagan lokal aniq ko‘rsatilsa, `LaravelLang\Exceptions\UnknownLocaleException` istisnosi chiqadi.

Bitta maydonning bir nechta qiymati uchun `setTranslations()` dan foydalaning. Shuningdek, har bir tarjima qilinadigan maydon uchun `create()` yoki `fill()` ga lokal kalitli xarita uzatishingiz mumkin. Misolda ingliz va fransuz tillari o‘rnatilgan deb hisoblanadi.

```php
$article->setTranslations('title', [
    'en' => 'First article',
    'fr' => 'Premier article',
])->save();
```

Modelda sozlangan maydonlarni tekshirish uchun `isTranslatable('title')` dan, qiymat bor-yo‘qligini tekshirish uchun `hasTranslated('title', 'fr')` dan foydalaning. `forgetTranslation('fr')` fransuzcha satrni, `forgetAllTranslations()` esa modelning barcha tarjima satrlarini o‘chiradi.

## Tarjimalarni so‘rash {#query-translations}

`translated()`, `whereTranslation()` va `whereTranslationLike()` `translations()` munosabati orqali so‘rov yuboradi. Standart holatda bu munosabat satrlarni joriy va zaxira lokallar bilan cheklaydi. Bu so‘rov metodlari orqali boshqa lokallarni so‘rash uchun `localization_models.filter` ni `false` qilib belgilang. Bitta modelning barcha satrlarini o‘qish uchun `translationsRaw()` dan foydalaning.

Joriy lokal fransuzcha bo‘lganda, fransuzcha sarlavha bo‘yicha so‘rang va tartiblang:

```php
use App\Models\Article;

$articles = Article::query()
    ->whereTranslationLike('title', 'Premier%', 'fr')
    ->orderByTranslation('title', 'asc', 'fr')
    ->get();
```

Modellar `translations()` aloqasini oldindan yuklaydi. `orWhereTranslation()` va `orWhereTranslationLike()` muqobil shart qo‘shadi. `orderByTranslation()` bitta lokal bo‘yicha saralaydi, standart qiymat joriy lokal.

## O‘zgarishlarni kuzatish va IDE yordamchi fayllarini yangilash {#observe-changes-and-refresh-ide-helpers}

`LaravelLang\Models\Events` nomlar fazosi `TranslationHasBeenSetEvent`, `TranslationHasBeenForgetEvent` va `AllTranslationsHasBeenForgetEvent` hodisalarini o‘z ichiga oladi. Qiymat belgilash hodisasida model, ustun, ixtiyoriy lokal muqobil nomi, oldingi va yangi qiymat mavjud. Hodisa atributga qiymat berilganda, keyingi saqlashdan oldin yuz beradi.

Barcha aniqlangan modellar uchun yordamchi fayllarni qayta yaratish uchun `php artisan lang:models-helper` buyrug‘ini bajaring. Standart katalog `vendor/_laravel_lang` bo‘lib, `localization_models.helpers` orqali sozlanadi.
