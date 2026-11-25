---
title: "Удзел у праекце"
description: "Дапамагайце з перакладамі, пакетамі PHP і дакументацыяй з лакальнай копіі Mona."
---

Стварыце форк [рэпазіторыя Mona](https://github.com/Laravel-Lang/mona) і адкрыйце запыт на зліццё ў яго галіну `main`. Mona захоўвае зыходныя шаблоны, агульныя пераклады, пакеты PHP і дакументацыю. Змесціва асобных рэпазіторыяў пакетаў экспартуецца з Mona.

## Выпраўленне або даданне перакладу {#fix-or-add-a-translation}

1. Знайдзіце англійскую фразу ў каталогу `src/<Package>/source/` пакета. Напрыклад, зыходныя файлы фрэймворка Laravel і яго афіцыйных пакетаў знаходзяцца ў `src/Lang/source/`. Файл `laravel_lang.json` пакета пералічвае зыходныя файлы для кожнай версіі пакета, з якога ўзятыя фразы.
2. Адкрыйце `src/Library/locales/<locale>/default.json`. Змяніце значэнне для англійскай фразы або дадайце адсутны запіс. Выкарыстоўвайце дакладнае зыходнае значэнне ў якасці ключа JSON, у тым ліку для фраз з файлаў PHP.
3. Захавайце ў перакладзе падстаноўкі, напрыклад `:count`, `:other` і `:attribute`. Для паведамлення праверкі без назвы поля выкарыстоўвайце той жа зыходны ключ у `inline.json` і замяніце `:attribute` фармулёўкай без назвы поля. Захавайце астатнія падстаноўкі і формы множнага ліку.
4. Калі пераклад наўмысна супадае з англійскай фразай, дадайце яе ў `_settings.json` лакалі ў раздзел `excludes`. Toolkit выкарыстоўвае гэты спіс для справаздачы пра стан перакладу.
5. Стварыце запыт на зліццё з кодам лакалі ў загалоўку, напрыклад `[fr]: Updated validation messages`.

Калі знайшлі памылку друку ў зыходнай фразе з іншага праекта, паведаміце пра яе ў тым праекце, каб фразу і яе пошукавы ключ абнавілі разам.

Напрыклад, запіс французскага перакладу па змаўчанні ў `src/Library/locales/fr/default.json` выглядае так:

```json
{
  "The :attribute field is invalid.": "Le champ :attribute est invalide."
}
```

Адпаведны запіс для ўбудаванага паведамлення ў `src/Library/locales/fr/inline.json`:

```json
{
  "The :attribute field is invalid.": "Ce champ est invalide."
}
```

Для перакладаў, якія Mona публікуе ў праграме, рэдагуйце `default.json` і `inline.json` у Library. У некаторых пакетах захаваліся слоўнікі старога фармату `php.json` і `json.json`. Іх таксама выкарыстоўвае аўтаматычная праверка стану перакладаў пакетаў.

Каб абнавіць назвы краін, валют або лакаляў, адрэдагуйце файл JSON патрэбнай лакалі ў `src/CountryNames/data/`, `src/CurrencyNames/data/` або `src/LocaleNames/data/`. Гл. [Лакалі і перакладзеныя назвы](./locales.md).

## Прапанова новай лакалі {#propose-a-locale}

Праверце [кананічныя коды лакаляў](./available-locales.md). Калі код ужо ў спісе, дадайце або абнавіце яго слоўнік у `src/Library/locales/`. Toolkit можа [стварыць адсутныя каталогі лакаляў](./toolkit.md#manage-locale-directories). Каб прапанаваць новы код, стварыце [абмеркаванне ў Laravel-Lang](https://github.com/orgs/Laravel-Lang/discussions/new?category=ideas&title=I%20want%20to%20add%20a%20new%20localization).

## Распрацоўка пакетаў {#develop-packages}

Выкарыстоўвайце Git, Composer 2 і PHP `^8.3` з пашырэннямі `intl`, `json` і `zip`. Уключыце `pdo_sqlite` для тэстаў, якія выкарыстоўваюць базу даных у памяці. Выконвайце каманды з кораня рэпазіторыя.

```bash
composer install
composer check-platform-reqs
```

Каранёвы праект Composer аўтаматычна загружае код з `src/<Package>/src/`. Захоўвайце залежнасці кожнага пакета, які публікуецца, у яго файле `src/<Package>/composer.json`. Для ўнутраных залежнасцяў выкарыстоўваецца абмежаванне `self.version`, таму пакеты аднаго выпуску можна ўсталяваць разам.

Для лакальнай праверкі пакетаў выкарыстоўвайце праграму Testbench у `workbench/`. Каманда `composer serve` збірае Workbench і запускае сервер распрацоўкі. Каб паглядзець маршруты або каманды Artisan, запусціце CLI Testbench з кораня рэпазіторыя.

```bash
composer serve
```

```bash
php vendor/bin/testbench route:list
php vendor/bin/testbench help lang:update
```

У CI кожны пакет усталёўваецца асобна, а астатнія пакеты падключаюцца з лакальнай копіі праз рэпазіторый Composer тыпу `path`. Калі змянілі залежнасці, праверце ўсталяванне асобнага пакета і запусціце каранёвы набор тэстаў. Налады і матрыца версій Laravel прыведзены ў [працэсе Test Dependencies](https://github.com/Laravel-Lang/mona/blob/main/.github/workflows/test-dependencies.yml).

## Лакальныя праверкі {#run-local-checks}

Пасля змен у слоўніку ўсталюйце залежнасці JavaScript у корані рэпазіторыя і запусціце праверкі Biome. Каманда Toolkit `status` запісвае справаздачы пра запісы, значэнне якіх супадае з зыходным ключом. Праверце зыходныя файлы пакета, каб знайсці фразы, для якіх у слоўніку яшчэ няма запісаў. Прагледзьце згенераваныя справаздачы праз `git status --short`. [Каманды Toolkit](./toolkit.md)

```bash
npm install
npx biome check src/Library/locales/fr/default.json
php src/Toolkit/bin/toolkit status src/Library
```

Для змен у PHP запусціце Pest і PHPStan з кораня рэпазіторыя. Запускайце функцыянальныя тэсты паслядоўна, як у CI. Тэсты генерацыі мадэляў выкарыстоўваюць агульны каталог праграмы Testbench. Архітэктурныя тэсты можна запускаць паралельна. Каб праверыць асобную функцыю, перадайце Pest шлях да файла або каталога з тэстамі.

```bash
php vendor/bin/pest --exclude-group=arch
php vendor/bin/pest --group=arch --parallel
composer test:types
```

Калі тэсты маршрутаў генеруюць URL з `http://localhost:8000` з налад распрацоўкі Testbench, перад запускам Pest задайце `APP_URL` роўным `http://localhost` у працэсе тэставання.

Скрыпт `composer style` запускае нармалізацыю Composer і выпраўленні Rector і Pint. Праглядзіце яго змены перад адпраўкай запыту на зліццё. Каб праверыць пакрыццё радкоў, уключыце Xdebug і выканайце `composer run-script --timeout=0 test:coverage`.

Для змен у маніфесце выканайце тую ж праверку па схеме, што і ў CI.

```bash
npx --yes ajv-cli@5.0.0 validate --spec=draft2020 -s resources/laravel-lang.schema.json -d 'src/*/laravel_lang.json'
```

Фармат маніфеста апісаны ў раздзеле [Плагіны перакладаў](./plugins.md).

## Рэдагаванне дакументацыі {#edit-the-documentation}

Англійскія старонкі бягучага выпуску знаходзяцца ў `docs/docs/`, а іх пераклады — у `docs/i18n/<language>/docusaurus-plugin-content-docs/current/`. Калі інструкцыі змяняюцца, абнаўляйце адпаведныя старонкі і захоўвайце аднолькавыя ідэнтыфікатары загалоўкаў для спасылак на ўсіх мовах. Архіўная версія да Mona знаходзіцца ў `docs/versioned_docs/version-legacy/`, а яе пераклады — у адпаведных каталогах `version-legacy/` у `docs/i18n/`.

Выкарыстоўвайце версію Node.js, пазначаную ў `docs/package.json`. Выконвайце каманды дакументацыі з каталога `docs/`.

```bash
cd docs
npm ci
npm start
```

Каманда `npm start -- --locale fr` запускае папярэдні прагляд аднаго перакладу. Спыніце сервер распрацоўкі і запусціце зборку, каб праверыць спасылкі, якары і пошукавы індэкс для ўсіх настроеных моў.

```bash
npm run typecheck
npm run build
npm run serve
```

Адкрыйце адрас, які паказвае каманда `serve`, каб праверыць пошук і згенераваныя файлы Markdown. Статычны сайт збіраецца ў `docs/build/`.

## Публікацыя выпуску {#publish-a-release}

Зліццё ў `main` экспартуе кожны пакет у галіну `main` яго рэпазіторыя і абнаўляе чарнавік выпуску Mona. Прагледзьце чарнавік і апублікуйце выпуск Mona з агульным тэгам версіі. Працэсы выпуску запускае падзея публікацыі выпуску.

Публікацыя выпуску экспартуе яго тэг і стварае адпаведны выпуск у кожным рэпазіторыі пакета. Яна таксама запускае зборку і разгортванне сайта дакументацыі. Пасля публікацыі праверце абодва працэсы. Каб загадзя паглядзець вынік вылучэння пакетаў, уручную запусціце **Export Packages** з `main` з уключаным `dry-run`. [Export Packages](https://github.com/Laravel-Lang/mona/actions/workflows/export-packages.yml) · [Documentation](https://github.com/Laravel-Lang/mona/actions/workflows/docs.yml)
