---
title: Як долучитися
description: "Долучайтеся до роботи над перекладами, пакетами PHP і документацією з локальної копії Mona."
---

Створіть форк [репозиторію Mona](https://github.com/Laravel-Lang/mona) й відкрийте запит на злиття до його гілки `main`. Mona зберігає вихідні шаблони, спільні переклади, пакети PHP і документацію. Вміст окремих репозиторіїв пакетів експортується з Mona.

## Виправлення або додавання перекладу {#fix-or-add-a-translation}

1. Знайдіть англійську фразу в каталозі пакета `src/<Package>/source/`. Наприклад, вихідні файли Laravel Framework і офіційних пакетів Laravel містяться в `src/Lang/source/`. У файлі `laravel_lang.json` пакета перелічено вихідні файли для кожної версії пакета, з якого беруться фрази.
2. Відкрийте `src/Library/locales/<locale>/default.json`. Змініть значення для англійської фрази або додайте запис, якщо його немає. Використовуйте точне вихідне значення як ключ JSON, зокрема для фраз із файлів PHP.
3. Зберігайте в перекладі заповнювачі на кшталт `:count`, `:other` і `:attribute`. Для повідомлення перевірки без назви поля використовуйте той самий вихідний ключ в `inline.json` і замініть `:attribute` формулюванням без назви поля. Зберігайте решту заповнювачів і форми множини.
4. Якщо переклад навмисно збігається з англійською фразою, додайте її до `excludes` у файлі `_settings.json` відповідної локалі. Toolkit використовує цей список для звітів про стан перекладів.
5. Створіть запит на злиття, зазначивши код локалі в заголовку, наприклад `[fr]: Updated validation messages`.

Якщо ви знайшли друкарську помилку у вихідній фразі іншого проєкту, повідомте про неї в його репозиторії, щоб оновити фразу разом із ключем пошуку.

Наприклад, типовий французький запис у `src/Library/locales/fr/default.json` має такий вигляд.

```json
{
  "The :attribute field is invalid.": "Le champ :attribute est invalide."
}
```

Відповідний запис для повідомлень без назви поля у `src/Library/locales/fr/inline.json` має такий вигляд.

```json
{
  "The :attribute field is invalid.": "Ce champ est invalide."
}
```

Для перекладів, які Mona публікує в застосунку, редагуйте `default.json` та `inline.json` у Library. У деяких пакетах лишилися словники старого формату `php.json` і `json.json`. Їх також читає автоматична перевірка стану перекладів пакетів.

Щоб оновити назви країн, валют або локалей, відредагуйте JSON-файл потрібної локалі в `src/CountryNames/data/`, `src/CurrencyNames/data/` або `src/LocaleNames/data/`. Див. [Локалі й перекладені назви](./locales.md).

## Пропозиція нової локалі {#propose-a-locale}

Перевірте [канонічні коди локалей](./available-locales.md). Якщо код уже є в переліку, додайте або оновіть його словник у `src/Library/locales/`. Toolkit може [створити відсутні каталоги локалей](./toolkit.md#manage-locale-directories). Щоб запропонувати новий код, відкрийте [обговорення в Laravel-Lang](https://github.com/orgs/Laravel-Lang/discussions/new?category=ideas&title=I%20want%20to%20add%20a%20new%20localization).

## Розробка пакетів {#develop-packages}

Використовуйте Git, Composer 2 і PHP `^8.3` із розширеннями `intl`, `json` і `zip`. Увімкніть `pdo_sqlite` для тестів, які використовують базу даних у пам’яті. Виконуйте команди з кореня репозиторію.

```bash
composer install
composer check-platform-reqs
```

Кореневий проєкт Composer автоматично завантажує код із `src/<Package>/src/`. Зберігайте залежності кожного пакета, який публікується, у його файлі `src/<Package>/composer.json`. Для внутрішніх залежностей пакетів використовується обмеження `self.version`, тож пакети з одного випуску можна встановлювати разом.

Щоб перевірити роботу пакета локально, використовуйте застосунок Testbench у `workbench/`. Команда `composer serve` збирає Workbench і запускає сервер розробки. Щоб переглянути маршрути або команди Artisan, запустіть CLI Testbench із кореня репозиторію.

```bash
composer serve
```

```bash
php vendor/bin/testbench route:list
php vendor/bin/testbench help lang:update
```

У CI кожен пакет встановлюється окремо. Інші пакети підключаються з локальної копії через репозиторій Composer типу `path`. Якщо ви змінили залежності, перевірте встановлення окремого пакета й виконайте кореневий набір тестів. Налаштування й матрицю версій Laravel наведено в [процесі Test Dependencies](https://github.com/Laravel-Lang/mona/blob/main/.github/workflows/test-dependencies.yml).

## Локальні перевірки {#run-local-checks}

Після змін у словнику встановіть залежності JavaScript у корені репозиторію й запустіть перевірки Biome. Команда Toolkit `status` записує звіти про записи, значення яких збігається з вихідним ключем. Перевірте вихідні файли пакета, щоб знайти фрази, для яких ще немає запису у словнику. Перегляньте згенеровані звіти через `git status --short`. [Команди Toolkit](./toolkit.md)

```bash
npm install
npx biome check src/Library/locales/fr/default.json
php src/Toolkit/bin/toolkit status src/Library
```

Для змін у PHP запустіть Pest і PHPStan із кореня репозиторію. Виконуйте функціональні тести послідовно, як у CI. Тести генерації моделей використовують спільний каталог застосунку Testbench. Архітектурні тести можна запускати паралельно. Щоб перевірити окрему функцію, передайте Pest шлях до файлу чи каталогу її тестів.

```bash
php vendor/bin/pest --exclude-group=arch
php vendor/bin/pest --group=arch --parallel
composer test:types
```

Якщо тести маршрутів через налаштування розробки Testbench генерують URL з `http://localhost:8000`, перед запуском Pest задайте `APP_URL=http://localhost` у середовищі процесу тестування.

Скрипт `composer style` запускає нормалізацію Composer та виправлення Rector і Pint. Перегляньте його зміни, перш ніж надсилати запит на злиття. Щоб перевірити покриття рядків, увімкніть Xdebug і виконайте `composer run-script --timeout=0 test:coverage`.

Якщо ви змінювали маніфест, перевірте його за тією самою схемою, що використовує CI.

```bash
npx --yes ajv-cli@5.0.0 validate --spec=draft2020 -s resources/laravel-lang.schema.json -d 'src/*/laravel_lang.json'
```

Формат маніфесту описано в розділі [Плагіни перекладу](./plugins.md).

## Редагування документації {#edit-the-documentation}

Англійські сторінки поточного випуску містяться в `docs/docs/`, а їхні переклади — у `docs/i18n/<language>/docusaurus-plugin-content-docs/current/`. Коли інструкції змінюються, оновлюйте відповідні сторінки й зберігайте однакові ідентифікатори заголовків для посилань усіма мовами. Архівну версію до Mona збережено в `docs/versioned_docs/version-legacy/`, а її переклади — у відповідних каталогах `version-legacy/` в `docs/i18n/`.

Використовуйте версію Node.js, указану в `docs/package.json`. Виконуйте команди документації з каталогу `docs/`.

```bash
cd docs
npm ci
npm start
```

Команда `npm start -- --locale fr` запускає попередній перегляд одного перекладу. Зупиніть сервер розробки й запустіть збірку, щоб перевірити посилання, якорі та пошуковий індекс усіма налаштованими мовами.

```bash
npm run typecheck
npm run build
npm run serve
```

Відкрийте адресу, яку показує `serve`, щоб перевірити пошук і згенеровані файли Markdown. Статичний сайт збирається в `docs/build/`.

## Публікація випуску {#publish-a-release}

Злиття в `main` експортує кожен пакет у гілку `main` його репозиторію й оновлює чернетку випуску Mona. Перевірте чернетку й опублікуйте випуск Mona зі спільним тегом версії. Процеси публікації запускає подія публікації випуску.

Публікація випуску експортує його тег і створює відповідний випуск у кожному репозиторії пакета. Вона також запускає збірку й розгортання сайту документації. Після публікації перевірте обидва процеси. Щоб заздалегідь переглянути результат виокремлення пакетів, вручну запустіть **Export Packages** із `main`, увімкнувши `dry-run`. [Export Packages](https://github.com/Laravel-Lang/mona/actions/workflows/export-packages.yml) · [Documentation](https://github.com/Laravel-Lang/mona/actions/workflows/docs.yml)
