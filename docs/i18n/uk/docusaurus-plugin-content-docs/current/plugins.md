---
title: Плагіни перекладу
description: Установлюйте пакети перекладів або публікуйте власний плагін Composer для Laravel-Lang.
---

Publisher знаходить пакети перекладів, установлені через Composer. Пакет надає маніфест `laravel_lang.json`, вихідні файли PHP або JSON і, за потреби, власні словники локалей.

## Установлення пакета {#install-a-pack}

Установіть пакет у застосунку Laravel й оновіть локалі, які вже використовуєте. Наприклад, пакет Actions додає підписи кнопок та інших дій.

```bash
composer require laravel-lang/actions
php artisan lang:update
```

Щоб додати локаль, див. [Керування перекладами](./translations.md). Сторонні пакети наведено на сторінці [Плагіни спільноти](./plugins-community.md).

### Офіційні пакети перекладів {#official-translation-packs}

Пакети наведено нижче. Усі вони входять до Common, але кожен можна встановити окремо через Composer.

| Пакет | Переклади |
| --- | --- |
| `laravel-lang/lang` | Фреймворк Laravel і пакети, зокрема Breeze, Cashier, Fortify, Jetstream, Nova, Passkeys та Laravel UI |
| `laravel-lang/starter-kits` | Стартові набори Laravel для Livewire, React, Svelte та Vue |
| `laravel-lang/moonshine` | Панель адміністратора MoonShine |
| `laravel-lang/actions` | Підписи кнопок та інших дій |
| `laravel-lang/attributes` | Зрозумілі назви атрибутів для перевірки даних |
| `laravel-lang/http-statuses` | Описи кодів стану HTTP |

Кожен пакет підключає Publisher і спільний пакет Library. У маніфесті зазначено інтеграції й версії, для яких пакет може публікувати переклади.

## Створення пакета {#create-a-pack}

Почніть із шаблону перекладів. У `composer.json` вашого пакета додайте залежність `laravel-lang/publisher` і вкажіть підтримувані версії. Додайте залежність `laravel-lang/library`, якщо ваш пакет використовує спільні переклади. Publisher шукає маніфести застосунку за шляхом `vendor/*/*/laravel_lang.json`. [Шаблон перекладів](https://github.com/Laravel-Lang/mona/tree/main/src/TranslationsTemplate)

Розмістіть маніфест у корені пакета. Цей приклад публікує файл JSON і групу перекладів PHP для кожної встановленої локалі.

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

Кожен шлях `source` вказано відносно каталогу `source/` пакета. Кожен шлях `target` вказано відносно мовного каталогу застосунку Laravel і містить `{locale}`.

Наприклад, додайте такий вміст до `source/audit.json`.

```json
{
  "Enable audit trail": "Enable audit trail"
}
```

А такий — до `source/audit.php`.

```php
<?php

return [
    'enable' => 'Enable audit trail',
];
```

Під час публікування французьких перекладів обидва файли шукають англійську фразу у `locales/fr/default.json` вашого пакета.

```json
{
  "Enable audit trail": "Activer le journal d’audit"
}
```

У застосунку `__('Enable audit trail')` читає переклад JSON, а `__('audit.enable')` — переклад PHP. Переклади пакета мають пріоритет над спільними записами Library для тієї самої фрази. Якщо фрази немає в жодному зі словників, Publisher використовує вихідне значення.

Дайте пакету власну назву в Composer і замініть `self.version` із шаблону обмеженням версії. Використовуйте канонічні коди локалей у назвах каталогів словників. Основні переклади зберігайте в `default.json`, а варіанти стилів — у файлах на кшталт `inline.json`.

### Публікування перекладів за умовою {#apply-translations-conditionally}

| Властивість маніфесту | Коли застосовується відповідність |
| --- | --- |
| Без умови | Publisher завжди застосовує її. |
| `vendor` | Установлено вказаний пакет Composer. |
| `vendor` і `version` | Установлений пакет відповідає обмеженню версії Composer. |
| `vendor` і `project: true` | Назва кореневого проєкту в Composer збігається з `vendor`. У цьому режимі Publisher перевіряє назву. |

Якщо відповідність має працювати і для встановленої залежності, і для кореневого проєкту з такою назвою, створіть два записи маніфесту. Перехід зі старого сервіс-провайдера описано в [посібнику з апгрейду](./upgrade.md), а правила виключення й примусового підключення пакетів — у розділі [Налаштування](./configure.md).

### Перевірка пакета в застосунку Laravel {#test-a-pack-in-a-laravel-application}

Якщо пакет міститься в сусідньому каталозі, виконайте команди з кореня тестового застосунку. Перевірте `lang/fr.json` і `lang/fr/audit.php`, установіть локаль `fr` і перевірте обидва ключі. Після зміни пакета виконайте `php artisan lang:update`.

```bash
composer config repositories.audit-translations path ../audit-translations
composer require "acme/audit-translations:*@dev"
php artisan lang:add fr
```
