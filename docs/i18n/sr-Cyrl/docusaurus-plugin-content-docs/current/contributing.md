---
title: Доприноси пројекту
description: "Доприносите преводима, PHP пакетима и документацији из локалне копије Mona."
---

Направите форк [репозиторијума Mona](https://github.com/Laravel-Lang/mona) и отворите захтев за повлачење у његову грану `main`. Mona чува изворне шаблоне, заједничке преводе, PHP пакете и документацију. Садржај појединачних репозиторијума пакета извози се из Mona.

## Исправка или додавање превода {#fix-or-add-a-translation}

1. Пронађите енглески израз у директоријуму `src/<Package>/source/` одговарајућег пакета. Извори за Laravel Framework и званичне Laravel пакете, на пример, налазе се у `src/Lang/source/`. Датотека `laravel_lang.json` пакета наводи изворне датотеке за сваку верзију пакета из којег потичу изрази.
2. Отворите `src/Library/locales/<locale>/default.json`. Измените вредност за енглески израз или додајте унос ако недостаје. Користите тачну изворну вредност као JSON кључ, укључујући изразе из PHP датотека.
3. У преводу сачувајте маркере као што су `:count`, `:other` и `:attribute`. За поруку валидације без назива поља користите исти изворни кључ у `inline.json` и замените `:attribute` изразом који не наводи назив поља. Сачувајте преостале маркере и облике множине.
4. Ако се превод намерно поклапа са енглеским изразом, додајте тај израз под `excludes` у датотеци `_settings.json` локала. Toolkit користи овај списак при генерисању статуса превода.
5. Отворите захтев за повлачење са кодом локала у наслову, на пример `[fr]: Updated validation messages`.

Ако пронађете словну грешку у изворном изразу другог пројекта, пријавите је у том пројекту како би извор и кључ за његово проналажење били ажурирани заједно.

Унос за француски у `src/Library/locales/fr/default.json`, на пример, изгледа овако.

```json
{
  "The :attribute field is invalid.": "Le champ :attribute est invalide."
}
```

Одговарајући унос без назива поља у `src/Library/locales/fr/inline.json` изгледа овако.

```json
{
  "The :attribute field is invalid.": "Ce champ est invalide."
}
```

За преводе које Mona објављује у апликацији уређујте `default.json` и `inline.json` у Library. Неки пакети и даље садрже речнике старог формата `php.json` и `json.json`. Њих користи и аутоматска провера статуса превода пакета.

Да ажурирате називе земаља, валута или локала, измените JSON датотеку локала у `src/CountryNames/data/`, `src/CurrencyNames/data/` или `src/LocaleNames/data/`. Погледајте [локале и преведене називе](./locales.md).

## Предлагање локала {#propose-a-locale}

Проверите [канонске кодове локала](./available-locales.md). Ако је код већ на списку, додајте или ажурирајте његов речник у `src/Library/locales/`. Toolkit може да [направи директоријуме локала који недостају](./toolkit.md#manage-locale-directories). Да предложите нови код, отворите [дискусију у Laravel-Lang](https://github.com/orgs/Laravel-Lang/discussions/new?category=ideas&title=I%20want%20to%20add%20a%20new%20localization).

## Развој пакета {#develop-packages}

Користите Git, Composer 2 и PHP `^8.3` са проширењима `intl`, `json` и `zip`. Омогућите `pdo_sqlite` за тестове који користе базу података у меморији. Покрените команде из корена репозиторијума.

```bash
composer install
composer check-platform-reqs
```

Корени Composer пројекат аутоматски учитава код из `src/<Package>/src/`. Дефинишите зависности сваког објављеног пакета у његовој датотеци `src/<Package>/composer.json`. Унутрашње зависности пакета користе ограничење `self.version` да би пакети из истог издања могли да се инсталирају заједно.

За локално испробавање пакета користите Testbench апликацију у `workbench/`. Команда `composer serve` припрема Workbench и покреће развојни сервер. Да прегледате руте или Artisan команде, покрените Testbench CLI из корена репозиторијума.

```bash
composer serve
```

```bash
php vendor/bin/testbench route:list
php vendor/bin/testbench help lang:update
```

CI инсталира сваки пакет засебно. Остали пакети из локалне копије доступни су преко Composer репозиторијума типа `path`. Када мењате зависности пакета, проверите засебну инсталацију и покрените тестове из корена. Подешавања и матрица верзија Laravel-а налазе се у [току рада Test Dependencies](https://github.com/Laravel-Lang/mona/blob/main/.github/workflows/test-dependencies.yml).

## Локалне провере {#run-local-checks}

После измене речника инсталирајте JavaScript зависности из корена и покрените Biome провере. Toolkit команда `status` уписује извештаје о уносима чија је вредност иста као изворни кључ. Проверите изворне датотеке пакета да пронађете изразе без уноса у речнику. Прегледајте генерисане извештаје помоћу `git status --short`. [Toolkit команде](./toolkit.md)

```bash
npm install
npx biome check src/Library/locales/fr/default.json
php src/Toolkit/bin/toolkit status src/Library
```

За измене у PHP-у покрените Pest и PHPStan из корена репозиторијума. Покрените функционалне тестове један за другим, као у CI-ју. Тестови који генеришу моделе користе исти директоријум Testbench апликације. Архитектонске тестове можете покренути паралелно. Да проверите једну функцију, проследите Pest-у путању до њене тестне датотеке или директоријума.

```bash
php vendor/bin/pest --exclude-group=arch
php vendor/bin/pest --group=arch --parallel
composer test:types
```

Ако тестови рута због развојних подешавања Testbench-а генеришу URL адресе са `http://localhost:8000`, пре покретања Pest-а поставите `APP_URL` у процесу тестирања на `http://localhost`.

Скрипта `composer style` покреће Composer нормализацију и исправке Rector-а и Pint-а. Прегледајте измене пре подношења захтева за повлачење. За покривеност линија укључите Xdebug и покрените `composer run-script --timeout=0 test:coverage`.

За измене манифеста покрените исту проверу шеме која се користи у CI-ју.

```bash
npx --yes ajv-cli@5.0.0 validate --spec=draft2020 -s resources/laravel-lang.schema.json -d 'src/*/laravel_lang.json'
```

Формат манифеста описан је у [додацима за преводе](./plugins.md).

## Уређивање документације {#edit-the-documentation}

Странице актуелног издања на енглеском налазе се у `docs/docs/`, а преводи у `docs/i18n/<language>/docusaurus-plugin-content-docs/current/`. Када се упутства промене, ажурирајте одговарајуће странице и задржите исте идентификаторе наслова за везе на свим језицима. Архивирана верзија пре Mona налази се у `docs/versioned_docs/version-legacy/`, а преводи у одговарајућим директоријумима `version-legacy/` унутар `docs/i18n/`.

Користите верзију Node.js наведену у `docs/package.json`. Покрените команде за документацију из `docs/`.

```bash
cd docs
npm ci
npm start
```

Команда `npm start -- --locale fr` покреће преглед једног превода. Зауставите развојни сервер и направите продукцијску верзију да проверите везе, сидра и индекс претраге за све подешене језике.

```bash
npm run typecheck
npm run build
npm run serve
```

Отворите адресу коју приказује `serve` да проверите претрагу и генерисане Markdown датотеке. Статички сајт се генерише у `docs/build/`.

## Објављивање издања {#publish-a-release}

Спајање у `main` извози сваки пакет у грану `main` његовог репозиторијума и ажурира нацрт издања Mona. Прегледајте нацрт и објавите издање Mona са заједничком ознаком верзије. Токови рада за издање покрећу се догађајем објављивања издања.

Објављивање издања извози његову ознаку и прави одговарајуће издање у репозиторијуму сваког пакета. Покреће и изградњу и постављање сајта документације. Проверите оба тока рада после објављивања. Да прегледате резултате издвајања пакета пре објављивања, ручно покрените **Export Packages** из `main` са укљученим `dry-run`. [Export Packages](https://github.com/Laravel-Lang/mona/actions/workflows/export-packages.yml) · [Documentation](https://github.com/Laravel-Lang/mona/actions/workflows/docs.yml)
