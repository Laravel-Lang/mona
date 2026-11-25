---
title: Алатке за превођење
description: Одржавајте, мигрирајте и увозите изворе превода и речнике из локалне копије Mona репозиторијума.
---

Toolkit је алатка командне линије за одржавање пакета превода. Покрените је из локалне копије Mona репозиторијума после инсталације коренских Composer зависности. За објављивање превода у Laravel апликацији користите [Artisan команде](./translations.md).

```bash
composer install
php src/Toolkit/bin/toolkit list
php src/Toolkit/bin/toolkit migrate-locales --help
```

## Провера статуса превода {#check-translation-status}

```bash
php src/Toolkit/bin/toolkit status src/Library
```

Подразумевано команда уписује `locales/<locale>/_status.json` у пројекат. Приказује уносе чији се сачувани превод поклапа са изворним изразом и групише их по стилу. Спаја изузетке из `locales/_settings.json` и `locales/<locale>/_settings.json` и брише празне извештаје. За проналажење израза који уопште нису у речнику, упоредите речник са изворним датотекама пакета.

Наведите опцију `--output-dir` да бисте извештаје уписали као датотеке `<locale>.json` у засебан директоријум. Команда тумачи релативне путање у односу на тренутни радни директоријум и прави одредишни директоријум када има извештај за упис.

```bash
php src/Toolkit/bin/toolkit status src/Library --output-dir=workbench/status
```

## Управљање директоријумима локала {#manage-locale-directories}

```bash
php src/Toolkit/bin/toolkit create-locale src/Library
php src/Toolkit/bin/toolkit delete-locale src/Library
```

`create-locale` прави директоријуме који недостају за све канонске [кодове локала](./available-locales.md). Нови директоријуми су празни. `delete-locale` брише сваки непосредни поддиректоријум у `locales/` чији назив није на том списку. Проверите одредиште пре покретања, нарочито ако садржи алијасе локала или прилагођене кодове.

## Уклањање застарелих уноса из речника {#remove-obsolete-dictionary-entries}

```bash
php src/Toolkit/bin/toolkit sync:library src
php src/Toolkit/bin/toolkit sync:package path/to/translation-pack
```

- `sync:library` прикупља изворне изразе из пакета са манифестом `laravel_lang.json` у `src/` и поново уписује речнике у `src/Library/locales/`.
- `sync:package` чита директоријум `source/` једног пакета и уклања неискоришћене уносе из његовог директоријума `locales/`. Брише речнике који после тога остану празни.

Обе команде задржавају постојеће преводе активних израза и чувају датотеке чији назив почиње доњом цртом, као што је `_settings.json`. Преводе нових израза додајте непосредно у речник.

## Миграција старих речника {#migrate-legacy-dictionaries}

```bash
php src/Toolkit/bin/toolkit migrate-locales path/to/legacy-pack --target=path/to/new-pack/locales
```

Проследите стари пакет који садржи `source/` и `locales/`. Као одредиште наведите директоријум који треба да садржи поддиректоријуме локала. У скриптама проследите `--target`. При интерактивном покретању алатка тражи одредиште ако није наведено.

- `php.json` и `json.json` прелазе у `default.json`. Нови кључеви су изворни изрази.
- `php-inline.json` и `json-inline.json` прелазе у `inline.json` за изворне изразе који садрже `:attribute`.
- Уноси из `_excludes.json` премештају се под `excludes` у `_settings.json` одредишног локала.

Toolkit задржава старе датотеке и спаја уносе у одредишне речнике. Када се кључ поклапа, предност има мигрирани превод. Ако оба стара формата имају превод истог израза, предност има PHP речник. Користите одвојено одредиште да прегледате миграцију пре замене активног речника. За измене у апликацији или манифесту додатка погледајте [водич за надоградњу](./upgrade.md).

## Увоз извора из ZIP архиве {#import-sources-from-a-zip-archive}

```bash
php src/Toolkit/bin/toolkit download \
  --url=https://github.com/laravel/framework/archive/refs/heads/12.x.zip \
  --directory=workbench/downloads/framework/12.x \
  --paths=src/Illuminate/Translation/lang/en \
  --only-copy
```

- `--directory` поставља крајње одредиште. `--paths` можете поновити за директоријуме релативне у односу на корен архиве.
- Изабране PHP и JSON датотеке копирају се по називу у одредиште. PHP датотеке копирају се бајт по бајт. Истоимене JSON датотеке спајају се тако да нове вредности имају предност.
- Без `--only-copy`, Toolkit тражи подржане позиве за превод са литералним кључевима. Ако нађе изразе, уписује JSON извор чији назив одговара надређеном директоријуму одредишта, попут `framework.json` у примеру.
- Команда захтева PHP ZIP проширење. Припрема измене пре објављивања и покушава да врати претходно одредиште ако објављивање не успе.
