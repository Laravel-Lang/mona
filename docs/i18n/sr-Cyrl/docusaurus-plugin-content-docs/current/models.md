---
title: Преводи модела
description: Чувајте, читајте и претражујте локализоване Eloquent атрибуте помоћу Laravel-Lang Models.
---

`laravel-lang/models` додаје преведене атрибуте постојећем Eloquent моделу. Сваки ред превода чува локал и вредности атрибута за тај локал. Пакет је укључен у Common и захтева PHP Intl.

```bash
composer require laravel-lang/models
```

## Додавање превода моделу {#add-translations-to-a-model}

За постојећи модел `App\Models\Article` који има миграцију основне табеле покрените следеће команде.

```bash
php artisan make:model-translation 'App\Models\Article' --columns=title --columns=description
php artisan migrate
```

Генератор додаје особину `LaravelLang\Models\HasTranslations` основном моделу и прави пратећи модел `ArticleTranslation`, миграцију и IDE помоћну датотеку. Миграција прави страни кључ ка чланку, јединствени пар `(item_id, locale)` и колоне за преведене ниске које могу имати вредност `null`. Ако ове вредности већ чувате у основној табели, пренесите их у редове превода пре него што почнете да користите приступне методе новог модела.

Ако модел није у `app/Models`, додајте његов директоријум у [`localization_models.directories`](./configure.md) пре покретања генератора.

Покрените `php artisan make:model-translation` да бисте интерактивно изабрали модел и колоне. Ако оставите празан списак, генератор користи `title` и `description`. Да бисте касније додали поље, направите миграцију и додајте поље у попуњиве атрибуте и конверзије модела превода.

## Упис и читање преведених атрибута {#write-and-read-translated-attributes}

Када је [француски инсталиран](./translations.md), можете да сачувате превод тако што ћете проследити канонски код, алијас, члан енума `Locale` или `LocaleData`. За упис измена користите `save()` основног модела.

```php
use App\Models\Article;
use LaravelLang\LocaleList\Locale;

$article = Article::create(['title' => 'First article']);
$article->setTranslation('title', 'Premier article', Locale::French)->save();

$article->title;                         // current locale, then fallback for a null value
$article->getTranslation('title', 'fr');  // 'Premier article'
```

Ако изричито наведете локал, чита се његова вредност. Без аргумента за локал, упис користи текући локал. При читању се користи резервни локал подешен у Laravel-у ако је текућа вредност `null`. Алијаси се разрешавају у канонске кодове и за претрагу и за чување. Изричито наведен непознат или неинсталирани локал изазива `LaravelLang\Exceptions\UnknownLocaleException`.

Користите `setTranslations()` за више вредности једног поља. Методу `create()` или `fill()` можете да проследите и мапу са локалима као кључевима за свако преведено поље. У примеру се подразумева да су инсталирани енглески и француски.

```php
$article->setTranslations('title', [
    'en' => 'First article',
    'fr' => 'Premier article',
])->save();
```

Користите `isTranslatable('title')` да проверите подешена поља модела, а `hasTranslated('title', 'fr')` да проверите да ли је вредност попуњена. `forgetTranslation('fr')` брише ред за француски, а `forgetAllTranslations()` брише све редове за модел.

## Упити над преводима {#query-translations}

`translated()`, `whereTranslation()` и `whereTranslationLike()` извршавају упите преко везе `translations()`. Ова веза подразумевано ограничава редове на текући и резервни локал. Да преко ових опсега претражите друге локале, подесите `localization_models.filter` на `false`. За читање свих редова једног модела користите `translationsRaw()`.

Када је француски текући локал, извршите упит и сортирајте по француском наслову.

```php
use App\Models\Article;

$articles = Article::query()
    ->whereTranslationLike('title', 'Premier%', 'fr')
    ->orderByTranslation('title', 'asc', 'fr')
    ->get();
```

Модели унапред учитавају релацију `translations()`. `orWhereTranslation()` и `orWhereTranslationLike()` додају алтернативан услов. `orderByTranslation()` сортира према једном локалу, подразумевано текућем.

## Праћење измена и освежавање IDE помоћних датотека {#observe-changes-and-refresh-ide-helpers}

Именски простор `LaravelLang\Models\Events` садржи `TranslationHasBeenSetEvent`, `TranslationHasBeenForgetEvent` и `AllTranslationsHasBeenForgetEvent`. Догађај постављања садржи модел, колону, опциони алијас локала, стару и нову вредност. Окида се при додели вредности атрибуту, пре каснијег чувања.

Покрените `php artisan lang:models-helper` да поново генеришете помоћне датотеке за све пронађене моделе. Њихов подразумевани директоријум је `vendor/_laravel_lang` и подешава се преко `localization_models.helpers`.
