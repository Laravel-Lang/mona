---
title: Додаци за преводе
description: Инсталирајте пакете превода или објавите свој Composer додатак за Laravel-Lang.
---

Publisher открива пакете превода инсталиране Composer-ом. Пакет обезбеђује манифест `laravel_lang.json`, PHP или JSON изворне датотеке и, по избору, своје речнике локала.

## Инсталација пакета {#install-a-pack}

Инсталирајте пакет у Laravel апликацију и ажурирајте локале које већ користите. На пример, пакет Actions додаје ознаке за дугмад и друге радње.

```bash
composer require laravel-lang/actions
php artisan lang:update
```

Додавање локала описано је у [управљању преводима](./translations.md), а пакети трећих страна у [додацима заједнице](./plugins-community.md).

### Званични пакети превода {#official-translation-packs}

Common садржи ове пакете. Сваки можете да инсталирате и засебно преко Composer-а.

| Пакет | Преводи |
| --- | --- |
| `laravel-lang/lang` | Оквир Laravel и пакети као што су Breeze, Cashier, Fortify, Jetstream, Nova, Passkeys и Laravel UI |
| `laravel-lang/starter-kits` | Почетни комплети за Laravel, укључујући Livewire, React, Svelte и Vue |
| `laravel-lang/moonshine` | Административна табла MoonShine |
| `laravel-lang/actions` | Ознаке дугмади и других радњи |
| `laravel-lang/attributes` | Читљиви називи атрибута за валидацију |
| `laravel-lang/http-statuses` | Изрази за HTTP статусе |

Сваки пакет укључује Publisher и заједнички Library. Његов манифест наводи за које интеграције и верзије може да објављује преводе.

## Прављење пакета {#create-a-pack}

Почните од шаблона за преводе. У `composer.json` свог пакета наведите зависност `laravel-lang/publisher` и верзије које подржавате. Ако се пакет ослања на заједничке преводе, додајте `laravel-lang/library`. Publisher тражи манифесте на путањи `vendor/*/*/laravel_lang.json` у апликацији. [Шаблон за преводе](https://github.com/Laravel-Lang/mona/tree/main/src/TranslationsTemplate)

Поставите манифест у корен пакета. Овај пример објављује једну JSON датотеку и једну PHP групу превода за сваки инсталирани локал.

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

Свака путања `source` релативна је у односу на директоријум пакета `source/`. Свака путања `target` релативна је у односу на језички директоријум Laravel апликације и садржи `{locale}`.

На пример, у `source/audit.json` упишите следеће.

```json
{
  "Enable audit trail": "Enable audit trail"
}
```

У `source/audit.php` упишите следеће.

```php
<?php

return [
    'enable' => 'Enable audit trail',
];
```

Оба примера при објављивању француског превода траже енглески израз у датотеци `locales/fr/default.json` вашег пакета.

```json
{
  "Enable audit trail": "Activer le journal d’audit"
}
```

У апликацији `__('Enable audit trail')` чита JSON превод, а `__('audit.enable')` чита PHP превод. Преводи пакета имају предност над уносима за исти израз из заједничког Library-а. Ако ниједан речник не садржи израз, Publisher користи изворну вредност.

Доделите пакету сопствено Composer име и замените `self.version` из шаблона ограничењем за подржану верзију. Користите канонске кодове локала за директоријуме речника. Стандардне преводе ставите у `default.json`, а стилске варијанте у датотеке попут `inline.json`.

### Условна примена превода {#apply-translations-conditionally}

| Својство манифеста | Када се мапирање примењује |
| --- | --- |
| Без услова | Publisher га увек укључује. |
| `vendor` | Наведени Composer пакет је инсталиран. |
| `vendor` и `version` | Инсталирани пакет испуњава Composer ограничење верзије. |
| `vendor` и `project: true` | Composer назив коренског пројекта одговара вредности `vendor`. Publisher у овом режиму проверава назив. |

Користите два уноса манифеста ако мапирање треба да важи и за инсталирану зависност и за коренски пројекат датог назива. Погледајте [водич за надоградњу](./upgrade.md) за конверзију старог сервисног провајдера и [подешавања](./configure.md) за правила изузимања и принудно подударање пакета.

### Провера пакета у Laravel апликацији {#test-a-pack-in-a-laravel-application}

Покрените команде из корена тестне апликације ако је пакет у суседном директоријуму. Проверите `lang/fr.json` и `lang/fr/audit.php`, поставите локал `fr` и тестирајте оба кључа. Након измене пакета покрените `php artisan lang:update`.

```bash
composer config repositories.audit-translations path ../audit-translations
composer require "acme/audit-translations:*@dev"
php artisan lang:add fr
```
