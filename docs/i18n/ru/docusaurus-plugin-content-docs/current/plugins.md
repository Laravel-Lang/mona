---
title: Плагины переводов
description: Устанавливайте пакеты переводов или публикуйте собственный плагин Composer для Laravel-Lang.
---

Publisher обнаруживает пакеты переводов, установленные через Composer. Пакет содержит манифест `laravel_lang.json`, исходные файлы PHP или JSON и при необходимости свои словари локалей.

## Установка пакета {#install-a-pack}

Установите пакет в приложение Laravel и обновите локали, которыми уже пользуетесь. Например, Actions добавляет подписи кнопок и других действий.

```bash
composer require laravel-lang/actions
php artisan lang:update
```

О добавлении локали см. [Управление переводами](./translations.md), а о сторонних пакетах — [Плагины сообщества](./plugins-community.md).

### Официальные пакеты переводов {#official-translation-packs}

Эти пакеты входят в Common. Каждый также можно установить отдельно через Composer.

| Пакет | Переводы |
| --- | --- |
| `laravel-lang/lang` | Фреймворк Laravel и пакеты Breeze, Cashier, Fortify, Jetstream, Nova, Passkeys и Laravel UI |
| `laravel-lang/starter-kits` | Стартовые наборы Laravel для Livewire, React, Svelte и Vue |
| `laravel-lang/moonshine` | Панель администрирования MoonShine |
| `laravel-lang/actions` | Подписи кнопок и других действий |
| `laravel-lang/attributes` | Понятные пользователю имена атрибутов в сообщениях валидации |
| `laravel-lang/http-statuses` | Тексты статусов HTTP |

Каждый пакет подключает Publisher и общую библиотеку Library. В манифесте пакета указано, для каких интеграций и версий он может публиковать переводы.

## Создание пакета {#create-a-pack}

Начните с шаблона переводов. В `composer.json` пакета добавьте зависимость от `laravel-lang/publisher` и укажите поддерживаемые версии. Если пакету нужны общие переводы, добавьте зависимость от `laravel-lang/library`. Publisher ищет манифесты приложения по пути `vendor/*/*/laravel_lang.json`. [Шаблон переводов](https://github.com/Laravel-Lang/mona/tree/main/src/TranslationsTemplate)

Разместите манифест в корне пакета. Следующий пример публикует JSON-файл и PHP-группу переводов для каждой установленной локали.

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

Пути `source` задаются относительно каталога пакета `source/`. Пути `target` задаются относительно языкового каталога приложения Laravel и содержат подстановку `{locale}`.

Пример файла `source/audit.json`:

```json
{
  "Enable audit trail": "Enable audit trail"
}
```

Пример файла `source/audit.php`:

```php
<?php

return [
    'enable' => 'Enable audit trail',
];
```

При публикации французского перевода для обоих файлов Publisher ищет английскую фразу в `locales/fr/default.json` пакета.

```json
{
  "Enable audit trail": "Activer le journal d’audit"
}
```

В приложении вызов `__('Enable audit trail')` читает JSON-перевод, а `__('audit.enable')` — PHP-перевод. Переводы пакета имеют приоритет над общими записями Library для той же фразы. Если фразы нет ни в одном словаре, Publisher использует исходное значение.

Задайте пакету собственное имя в Composer и замените `self.version` из шаблона ограничением для нужной версии. Используйте канонические коды локалей в именах каталогов словарей. Основные переводы храните в `default.json`, а варианты оформления — в файлах наподобие `inline.json`.

### Условная публикация переводов {#apply-translations-conditionally}

| Свойство манифеста | Когда применяется соответствие |
| --- | --- |
| Без условий | Publisher включает его всегда. |
| `vendor` | Указанный пакет Composer установлен. |
| `vendor` и `version` | Установленный пакет удовлетворяет ограничению версий Composer. |
| `vendor` и `project: true` | Имя корневого проекта в Composer совпадает с `vendor`. В этом режиме Publisher проверяет имя. |

Используйте две записи манифеста, если соответствие должно применяться и к установленной зависимости, и к корневому проекту с этим именем. Переход со старого сервис-провайдера описан в [руководстве по апгрейду](./upgrade.md). Правила исключения и принудительного подключения пакетов см. в разделе [Конфигурация](./configure.md).

### Проверка пакета в приложении Laravel {#test-a-pack-in-a-laravel-application}

Если пакет находится в соседнем каталоге, выполните команды из корня тестового приложения. Проверьте `lang/fr.json` и `lang/fr/audit.php`, установите локаль `fr` и проверьте оба ключа. После изменения пакета выполните `php artisan lang:update`.

```bash
composer config repositories.audit-translations path ../audit-translations
composer require "acme/audit-translations:*@dev"
php artisan lang:add fr
```
