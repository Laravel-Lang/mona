---
title: "Пераклады мадэляў"
description: "Захоўвайце, чытайце і шукайце лакалізаваныя атрыбуты Eloquent з Laravel-Lang Models."
---

`laravel-lang/models` дадае перакладзеныя атрыбуты да наяўнай мадэлі Eloquent. Кожны радок перакладу змяшчае лакаль і значэнні атрыбутаў для яе. Пакет уваходзіць у Common і патрабуе PHP Intl.

```bash
composer require laravel-lang/models
```

## Даданне перакладаў да мадэлі {#add-translations-to-a-model}

Для наяўнай мадэлі `App\Models\Article` з міграцыяй базавай табліцы выканайце:

```bash
php artisan make:model-translation 'App\Models\Article' --columns=title --columns=description
php artisan migrate
```

Генератар дадае трэйт `LaravelLang\Models\HasTranslations` да базавай мадэлі і стварае спадарожную мадэль `ArticleTranslation`, міграцыю і дапаможны файл для IDE. Міграцыя стварае знешні ключ да артыкула, унікальную пару `(item_id, locale)` і радковыя слупкі для перакладзеных значэнняў з падтрымкай null. Калі гэтыя значэнні ўжо захоўваюцца ў базавай табліцы, перанясіце іх у радкі перакладу, перш чым выкарыстоўваць аксэсары новай мадэлі.

Калі мадэль знаходзіцца па-за `app/Models`, дадайце яе каталог у [`localization_models.directories`](./configure.md) перад запускам генератара.

Запусціце `php artisan make:model-translation`, каб інтэрактыўна выбраць мадэль і слупкі. Калі спіс пусты, генератар выкарыстоўвае `title` і `description`. Каб пазней дадаць поле, стварыце міграцыю і дапоўніце запаўняльныя палі і пераўтварэнні мадэлі перакладу.

## Запіс і чытанне перакладзеных атрыбутаў {#write-and-read-translated-attributes}

Калі [французская лакаль усталяваная](./translations.md), пераклад можна захаваць, перадаўшы кананічны код, аліяс, варыянт enum-а `Locale` або `LocaleData`. Каб запісаць змены, выкарыстоўвайце `save()` базавай мадэлі.

```php
use App\Models\Article;
use LaravelLang\LocaleList\Locale;

$article = Article::create(['title' => 'First article']);
$article->setTranslation('title', 'Premier article', Locale::French)->save();

$article->title;                         // Бягучая лакаль, затым рэзервовая, калі значэнне null
$article->getTranslation('title', 'fr');  // 'Premier article'
```

Пры яўнай лакалі чытаецца значэнне для яе. Без аргумента лакалі запіс ідзе ў бягучую лакаль, а пры чытанні выкарыстоўваецца наладжаная ў Laravel рэзервовая лакаль, калі бягучае значэнне роўнае `null`. Для пошуку і захавання аліясы пераўтвараюцца ў кананічныя коды. Яўна зададзеная невядомая або неўсталяваная лакаль выклікае `LaravelLang\Exceptions\UnknownLocaleException`.

Выкарыстоўвайце `setTranslations()` для некалькіх значэнняў аднаго поля. Таксама можна перадаць у `create()` або `fill()` для кожнага перакладзенага поля масіў, дзе ключы — лакалі. Прыклад прадугледжвае, што англійская і французская лакалі ўсталяваныя.

```php
$article->setTranslations('title', [
    'en' => 'First article',
    'fr' => 'Premier article',
])->save();
```

Выкарыстоўвайце `isTranslatable('title')` для праверкі наладжаных палёў мадэлі і `hasTranslated('title', 'fr')` для праверкі наяўнасці запоўненага значэння. `forgetTranslation('fr')` выдаляе французскі радок, а `forgetAllTranslations()` выдаляе ўсе радкі мадэлі.

## Запыты да перакладаў {#query-translations}

`translated()`, `whereTranslation()` і `whereTranslationLike()` выконваюць запыты праз сувязь `translations()`. Па змаўчанні яна адбірае радкі толькі для бягучай і рэзервовай лакаляў. Каб рабіць запыты да іншых лакаляў праз гэтыя вобласці запытаў, задайце `localization_models.filter` значэнне `false`. Каб атрымаць усе радкі адной мадэлі, выкарыстоўвайце `translationsRaw()`.

Калі бягучая лакаль — французская, выбарка і сартаванне паводле французскай назвы выглядаюць так:

```php
use App\Models\Article;

$articles = Article::query()
    ->whereTranslationLike('title', 'Premier%', 'fr')
    ->orderByTranslation('title', 'asc', 'fr')
    ->get();
```

Мадэлі загадзя загружаюць сувязь `translations()`. `orWhereTranslation()` і `orWhereTranslationLike()` дадаюць альтэрнатыўную ўмову. `orderByTranslation()` сартуе паводле адной лакалі, паводле змоўчання бягучай.

## Назіранне за зменамі і абнаўленне дапаможных файлаў IDE {#observe-changes-and-refresh-ide-helpers}

Прастора імёнаў `LaravelLang\Models\Events` змяшчае `TranslationHasBeenSetEvent`, `TranslationHasBeenForgetEvent` і `AllTranslationsHasBeenForgetEvent`. Падзея прысваення змяшчае мадэль, слупок, неабавязковы аліяс лакалі, старое і новае значэнні. Яна спрацоўвае пры прысваенні атрыбута перад наступным захаваннем.

Запусціце `php artisan lang:models-helper`, каб нанова згенераваць дапаможныя файлы для ўсіх знойдзеных мадэляў. Іх каталог па змаўчанні — `vendor/_laravel_lang`. Яго можна змяніць у `localization_models.helpers`.
