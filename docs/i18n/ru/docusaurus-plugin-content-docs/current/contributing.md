---
title: Участие в проекте
description: "Участвуйте в работе над переводами, PHP-пакетами и документацией из локальной копии Mona."
---

Создайте форк [репозитория Mona](https://github.com/Laravel-Lang/mona) и откройте пул-реквест в его ветку `main`. Mona хранит исходные шаблоны, общие переводы, PHP-пакеты и документацию. Содержимое отдельных репозиториев пакетов экспортируется из Mona.

## Исправление или добавление перевода {#fix-or-add-a-translation}

1. Найдите английскую фразу в каталоге пакета `src/<Package>/source/`. Например, источники Laravel Framework и официальных пакетов Laravel находятся в `src/Lang/source/`. В файле `laravel_lang.json` пакета перечислены источники для каждой версии пакета, из которого берутся фразы.
2. Откройте `src/Library/locales/<locale>/default.json`. Измените значение по ключу с английской фразой или добавьте отсутствующую запись. В качестве ключа JSON используйте точное исходное значение, в том числе для фраз из PHP-файлов.
3. Сохраняйте в переводе подстановки `:count`, `:other`, `:attribute` и подобные. Для сообщения валидации без имени поля используйте тот же ключ в `inline.json` и замените `:attribute` формулировкой без названия поля. Сохраните остальные подстановки и формы множественного числа.
4. Если перевод намеренно совпадает с английской фразой, добавьте её в список `excludes` файла `_settings.json` этой локали. Toolkit учитывает список при составлении отчёта о состоянии переводов.
5. Откройте пул-реквест с кодом локали в заголовке, например `[fr]: Updated validation messages`.

Если нашли опечатку в исходной фразе из другого проекта, сообщите о ней в том проекте, чтобы исходник и ключ поиска обновились вместе.

Например, стандартная запись на французском в `src/Library/locales/fr/default.json` выглядит так:

```json
{
  "The :attribute field is invalid.": "Le champ :attribute est invalide."
}
```

Соответствующая запись без имени поля в `src/Library/locales/fr/inline.json` выглядит так:

```json
{
  "The :attribute field is invalid.": "Ce champ est invalide."
}
```

Для переводов, которые Mona публикует в приложении, редактируйте `default.json` и `inline.json` в Library. В некоторых пакетах сохранились словари прежнего формата `php.json` и `json.json`. В них также заглядывает автоматическая проверка состояния переводов пакетов.

Чтобы обновить названия стран, валют или локалей, отредактируйте JSON-файл локали в `src/CountryNames/data/`, `src/CurrencyNames/data/` или `src/LocaleNames/data/`. См. [Локали и переведённые названия](./locales.md).

## Как предложить локаль {#propose-a-locale}

Проверьте [канонические коды локалей](./available-locales.md). Если код уже в списке, добавьте или обновите его словарь в `src/Library/locales/`. Toolkit умеет [создавать недостающие каталоги локалей](./toolkit.md#manage-locale-directories). Чтобы предложить новый код, откройте [обсуждение в Laravel-Lang](https://github.com/orgs/Laravel-Lang/discussions/new?category=ideas&title=I%20want%20to%20add%20a%20new%20localization).

## Разработка пакетов {#develop-packages}

Установите Git, Composer 2 и PHP `^8.3` с расширениями `intl`, `json` и `zip`. Включите `pdo_sqlite` для тестов, которые используют базу данных в памяти. Выполняйте команды из корня репозитория.

```bash
composer install
composer check-platform-reqs
```

Корневой проект Composer загружает код из `src/<Package>/src/`. Указывайте зависимости каждого публикуемого пакета в его файле `src/<Package>/composer.json`. Для внутренних зависимостей используется ограничение `self.version`, поэтому пакеты из одного релиза устанавливаются вместе.

Для локальной проверки пакетов используйте приложение Testbench в `workbench/`. Команда `composer serve` собирает Workbench и запускает сервер для разработки. Маршруты и команды Artisan можно посмотреть через CLI Testbench из корня репозитория.

```bash
composer serve
```

```bash
php vendor/bin/testbench route:list
php vendor/bin/testbench help lang:update
```

В CI каждый пакет устанавливается отдельно. Остальные пакеты подключаются из локальной копии через репозиторий Composer типа `path`. При изменении зависимостей проверьте установку отдельного пакета и запустите корневой набор тестов. Настройки и матрица версий Laravel приведены в [процессе Test Dependencies](https://github.com/Laravel-Lang/mona/blob/main/.github/workflows/test-dependencies.yml).

## Локальные проверки {#run-local-checks}

После изменений в словаре установите JavaScript-зависимости корневого проекта и запустите проверки Biome. Команда `status` в Toolkit формирует отчёты по записям, значение которых совпадает с исходным ключом. Сверьтесь с исходными файлами пакета, чтобы найти фразы, для которых в словаре ещё нет записи. Просмотрите изменения отчётов через `git status --short`. [Команды Toolkit](./toolkit.md)

```bash
npm install
npx biome check src/Library/locales/fr/default.json
php src/Toolkit/bin/toolkit status src/Library
```

После изменений в PHP запустите Pest и PHPStan из корня репозитория. Запускайте функциональные тесты последовательно, как в CI. Тесты генерации моделей используют общий каталог приложения Testbench. Архитектурные тесты можно запускать параллельно. Чтобы проверить одну функцию, передайте Pest путь к её тестовому файлу или каталогу.

```bash
php vendor/bin/pest --exclude-group=arch
php vendor/bin/pest --group=arch --parallel
composer test:types
```

Если тесты маршрутов используют адрес `http://localhost:8000` из настроек разработки Testbench, перед запуском Pest задайте `APP_URL=http://localhost` в окружении тестового процесса.

Скрипт `composer style` запускает нормализацию Composer и правки Rector и Pint. Просмотрите изменения перед отправкой пул-реквеста. Чтобы проверить покрытие строк, включите Xdebug и выполните `composer run-script --timeout=0 test:coverage`.

Если меняли манифест, выполните ту же проверку схемы, что и в CI.

```bash
npx --yes ajv-cli@5.0.0 validate --spec=draft2020 -s resources/laravel-lang.schema.json -d 'src/*/laravel_lang.json'
```

Формат манифеста описан в разделе [Плагины переводов](./plugins.md).

## Редактирование документации {#edit-the-documentation}

Английские страницы текущего релиза находятся в `docs/docs/`, а их переводы — в `docs/i18n/<language>/docusaurus-plugin-content-docs/current/`. Когда меняются инструкции, обновляйте соответствующие страницы и сохраняйте одинаковые идентификаторы заголовков для ссылок на разных языках. Архивная версия до Mona находится в `docs/versioned_docs/version-legacy/`, а её переводы — в соответствующих каталогах `version-legacy/` внутри `docs/i18n/`.

Используйте версию Node.js, указанную в `docs/package.json`. Команды для документации выполняются из `docs/`.

```bash
cd docs
npm ci
npm start
```

Команда `npm start -- --locale fr` запускает предварительный просмотр одного перевода. Остановите сервер разработки и запустите сборку, чтобы проверить ссылки, якоря и поисковый индекс для всех настроенных языков.

```bash
npm run typecheck
npm run build
npm run serve
```

Откройте адрес, который выводит команда `serve`, чтобы проверить поиск и сгенерированные файлы Markdown. Статический сайт собирается в `docs/build/`.

## Публикация релиза {#publish-a-release}

Слияние в `main` экспортирует каждый пакет в ветку `main` его репозитория и обновляет черновик релиза Mona. Проверьте черновик и опубликуйте релиз Mona с общим тегом версии. Процессы публикации запускает событие публикации релиза.

Публикация релиза экспортирует его тег и создаёт соответствующий релиз в каждом репозитории пакета. Она также запускает сборку и развёртывание сайта документации. Проверьте оба процесса после публикации. Чтобы заранее посмотреть результат выделения пакетов, вручную запустите **Export Packages** из `main` с включённым `dry-run`. [Export Packages](https://github.com/Laravel-Lang/mona/actions/workflows/export-packages.yml) · [Documentation](https://github.com/Laravel-Lang/mona/actions/workflows/docs.yml)
