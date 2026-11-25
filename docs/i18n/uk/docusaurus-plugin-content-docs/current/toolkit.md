---
title: Інструменти перекладу
description: Обслуговуйте, мігруйте й імпортуйте вихідні файли та словники перекладів із локальної копії Mona.
---

Toolkit — інструмент командного рядка для обслуговування пакетів перекладів. Запускайте його в локальній копії Mona після встановлення залежностей Composer із кореня проєкту. Щоб публікувати переклади в застосунку Laravel, використовуйте [команди Artisan](./translations.md).

```bash
composer install
php src/Toolkit/bin/toolkit list
php src/Toolkit/bin/toolkit migrate-locales --help
```

## Перевірка стану перекладів {#check-translation-status}

```bash
php src/Toolkit/bin/toolkit status src/Library
```

За замовчуванням команда записує `locales/<locale>/_status.json` у проєкті. Вона перелічує записи, чий збережений переклад збігається з вихідною фразою, і групує їх за стилем. Команда об'єднує винятки з `locales/_settings.json` та `locales/<locale>/_settings.json` і видаляє порожні звіти. Щоб знайти фрази, яких узагалі немає у словнику, порівняйте його з вихідними файлами пакета.

Укажіть `--output-dir`, щоб записувати звіти у файли `<locale>.json` в окремому каталозі. Для відносних шляхів команда використовує поточний робочий каталог як базовий. Якщо каталогу призначення ще немає, команда створює його під час запису першого звіту.

```bash
php src/Toolkit/bin/toolkit status src/Library --output-dir=workbench/status
```

## Керування каталогами локалей {#manage-locale-directories}

```bash
php src/Toolkit/bin/toolkit create-locale src/Library
php src/Toolkit/bin/toolkit delete-locale src/Library
```

Команда `create-locale` створює відсутні каталоги для всіх канонічних [кодів локалей](./available-locales.md). Нові каталоги порожні. Команда `delete-locale` видаляє кожен каталог безпосередньо в `locales/`, назви якого немає в цьому списку. Перед запуском перевірте цільовий каталог, особливо якщо він містить аліаси локалей або власні коди.

## Видалення застарілих записів словника {#remove-obsolete-dictionary-entries}

```bash
php src/Toolkit/bin/toolkit sync:library src
php src/Toolkit/bin/toolkit sync:package path/to/translation-pack
```

- `sync:library` збирає вихідні фрази з пакетів із маніфестом `laravel_lang.json` у `src/` і перезаписує словники в `src/Library/locales/`.
- `sync:package` читає каталог `source/` одного пакета та вилучає невикористані записи з його каталогу `locales/`. Команда видаляє словники, що залишилися порожніми.

Обидві команди зберігають поточні переклади для активних фраз і файли з префіксом підкреслення, як-от `_settings.json`. Переклади нових фраз додавайте безпосередньо до словника.

## Міграція словників попередніх версій {#migrate-legacy-dictionaries}

```bash
php src/Toolkit/bin/toolkit migrate-locales path/to/legacy-pack --target=path/to/new-pack/locales
```

Передайте пакет попередньої версії з `source/` та `locales/`. Цільовий каталог має містити підкаталоги локалей. У скриптах передавайте `--target`. Якщо параметр пропущено, інтерактивний запуск запитає його.

- `php.json` та `json.json` стають `default.json`. Новими ключами стають вихідні фрази.
- `php-inline.json` та `json-inline.json` стають `inline.json` для вихідних фраз із `:attribute`.
- Записи з `_excludes.json` переносяться до `excludes` у файлі `_settings.json` цільової локалі.

Toolkit зберігає файли попередніх версій та об'єднує їх із цільовими словниками. У разі збігу ключів пріоритет має перенесений переклад. Якщо обидва старі формати містять переклад тієї самої фрази, пріоритет має словник PHP. Використовуйте окремий цільовий каталог, щоб перевірити міграцію перед заміною чинного словника. Зміни до застосунку або маніфесту плагіна описано в [посібнику з апгрейду](./upgrade.md).

## Імпортування вихідних файлів з архіву ZIP {#import-sources-from-a-zip-archive}

```bash
php src/Toolkit/bin/toolkit download \
  --url=https://github.com/laravel/framework/archive/refs/heads/12.x.zip \
  --directory=workbench/downloads/framework/12.x \
  --paths=src/Illuminate/Translation/lang/en \
  --only-copy
```

- `--directory` задає кінцевий цільовий каталог. `--paths` можна повторювати для каталогів відносно кореня архіву.
- Вибрані файли PHP та JSON копіюються до цільового каталогу за назвою файла. Файли PHP копіюються побайтово. Файли JSON з однаковими назвами об'єднуються з пріоритетом нових значень.
- Без `--only-copy` Toolkit шукає підтримувані виклики перекладу з літеральними ключами. Якщо фрази знайдено, команда записує вихідний файл JSON із назвою батьківського каталогу цільового шляху, наприклад `framework.json` у наведеному прикладі.
- Команді потрібне розширення PHP ZIP. Вона готує зміни перед публікуванням, а в разі помилки намагається відновити попередній стан цільового каталогу.
