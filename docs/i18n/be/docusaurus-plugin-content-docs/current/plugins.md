---
title: "Плагіны перакладаў"
description: "Усталюйце пакеты перакладаў або апублікуйце ўласны плагін Composer для Laravel-Lang."
---

Publisher знаходзіць пакеты перакладаў, усталяваныя праз Composer. Пакет змяшчае маніфест `laravel_lang.json`, зыходныя файлы PHP або JSON і, неабавязкова, уласныя слоўнікі лакаляў.

## Усталяванне пакета {#install-a-pack}

Усталюйце пакет у прыкладанне Laravel і абнавіце лакалі, якімі ўжо карыстаецеся. Напрыклад, пакет Actions дадае надпісы кнопак і іншых дзеянняў.

```bash
composer require laravel-lang/actions
php artisan lang:update
```

Каб дадаць лакаль, глядзіце [Кіраванне перакладамі](./translations.md), а пакеты ад іншых распрацоўшчыкаў шукайце ў раздзеле [Плагіны супольнасці](./plugins-community.md).

### Афіцыйныя пакеты перакладаў {#official-translation-packs}

Common уключае гэтыя пакеты. Кожны з іх таксама можна ўсталяваць асобна праз Composer.

| Пакет | Пераклады |
| --- | --- |
| `laravel-lang/lang` | Фрэймворк Laravel і такія пакеты, як Breeze, Cashier, Fortify, Jetstream, Nova, Passkeys і Laravel UI |
| `laravel-lang/starter-kits` | Стартавыя наборы Laravel для Livewire, React, Svelte і Vue |
| `laravel-lang/moonshine` | Панэль адміністратара MoonShine |
| `laravel-lang/actions` | Надпісы для кнопак і іншых дзеянняў |
| `laravel-lang/attributes` | Зразумелыя назвы атрыбутаў для праверкі даных |
| `laravel-lang/http-statuses` | Фразы станаў HTTP |

Кожны пакет падключае Publisher і агульны слоўнік Library. Маніфест вызначае, для якіх інтэграцый і версій пакет можа публікаваць пераклады.

## Стварэнне пакета {#create-a-pack}

Пачніце з шаблона перакладаў. У `composer.json` свайго пакета дадайце залежнасць ад `laravel-lang/publisher` і ўкажыце падтрыманыя версіі. Дадайце залежнасць ад `laravel-lang/library`, калі пакет выкарыстоўвае агульныя пераклады. Publisher шукае маніфесты ў `vendor/*/*/laravel_lang.json` прыкладання. [Шаблон перакладаў](https://github.com/Laravel-Lang/mona/tree/main/src/TranslationsTemplate)

Змясціце маніфест у корань пакета. Гэты прыклад публікуе файл JSON і групу перакладаў PHP для кожнай усталяванай лакалі.

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

Кожны шлях `source` задаецца адносна каталога `source/` пакета. Кожны `target` задаецца адносна моўнага каталога прыкладання Laravel і змяшчае `{locale}`.

Напрыклад, дадайце наступнае ў `source/audit.json`:

```json
{
  "Enable audit trail": "Enable audit trail"
}
```

А наступнае — у `source/audit.php`:

```php
<?php

return [
    'enable' => 'Enable audit trail',
];
```

Пры публікацыі французскіх перакладаў абодва файлы шукаюць англійскую фразу ў `locales/fr/default.json` вашага пакета.

```json
{
  "Enable audit trail": "Activer le journal d’audit"
}
```

У прыкладанні `__('Enable audit trail')` чытае пераклад JSON, а `__('audit.enable')` чытае пераклад PHP. Пераклады пакета маюць прыярытэт перад запісамі з агульнага слоўніка Library для той жа фразы. Калі яе няма ні ў адным слоўніку, Publisher выкарыстоўвае зыходнае значэнне.

Задайце пакету ўласную назву Composer і замяніце `self.version` у шаблоне абмежаваннямі для патрэбнай лінейкі. Выкарыстоўвайце кананічныя коды лакаляў у назвах каталогаў слоўнікаў. Захоўвайце стандартныя пераклады ў `default.json`, а варыянты стыляў — у файлах накшталт `inline.json`.

### Прымяненне перакладаў па ўмове {#apply-translations-conditionally}

| Уласцівасць маніфеста | Калі дзейнічае супастаўленне |
| --- | --- |
| Без умовы | Publisher уключае яго заўсёды. |
| `vendor` | Названы пакет Composer усталяваны. |
| `vendor` і `version` | Усталяваны пакет адпавядае абмежаванню версіі Composer. |
| `vendor` і `project: true` | Імя каранёвага праекта ў Composer супадае з `vendor`. У гэтым рэжыме Publisher правярае імя. |

Выкарыстоўвайце два запісы ў маніфесце, калі супастаўленне павінна дзейнічаць і для ўсталяванай залежнасці, і для каранёвага праекта з гэтым імем. Глядзіце [дапаможнік па апгрэйдзе](./upgrade.md) для пераўтварэння старога сэрвіс-правайдара і [канфігурацыю](./configure.md) для правілаў выключэння і прымусовага супастаўлення пакетаў.

### Праверка пакета ў прыкладанні Laravel {#test-a-pack-in-a-laravel-application}

Выканайце каманды з кораня тэставага прыкладання, калі пакет знаходзіцца ў суседнім каталогу. Праверце `lang/fr.json` і `lang/fr/audit.php`, усталюйце лакаль прыкладання ў `fr` і праверце абодва ключы перакладу. Пасля змен у пакеце запусціце `php artisan lang:update`.

```bash
composer config repositories.audit-translations path ../audit-translations
composer require "acme/audit-translations:*@dev"
php artisan lang:add fr
```
