---
title: Локали и називи
description: Пронађите подржане и инсталиране локале, разрешите алијасе и потражите локализоване називе.
---

Laravel-Lang користи канонске кодове, као што су `en`, `fr` и `pt_BR`, у Laravel-у, називима датотека превода и редовима превода модела. Алијас локала је назив који користи апликација, на пример `pt-br` у URL-у. У [`localization_main.aliases`](./configure.md) дефинишите мапу канонских кодова у алијасе.

## Претрага подржаних и инсталираних локала {#find-supported-and-installed-locales}

`laravel-lang/locale-list` обезбеђује енум са придруженим вредностима `LaravelLang\LocaleList\Locale`. Његови чланови одговарају [подржаним канонским кодовима](./available-locales.md).

```php
use LaravelLang\LocaleList\Locale;

Locale::French->value;        // 'fr'
Locale::tryFrom('pt_BR');     // Locale::PortugueseBrazil
Locale::values();            // Низ канонских кодова
Locale::collection();        // Колекција канонских кодова
Locale::collection(false);   // Колекција чланова енума
```

`laravel-lang/locales` додаје фасаду `LaravelLang\Locales\Facades\Locales` која узима у обзир подешавања апликације. Ови методи враћају колекције ниски. Проследите `false` да добијете канонске кодове. Подразумевано сваки код представља његов подешени алијас, ако постоји.

| Метод | Повратна вредност |
| --- | --- |
| `Locales::available()` | Сви познати локали |
| `Locales::installed()` | Локали препознати из језичких датотека апликације и заштићени локали |
| `Locales::uninstalled()` | Доступни локали који нису инсталирани |
| `Locales::protects()` | Ефективни текући и резервни локал без дупликата |

Откривање обухвата непосредне ставке у `lang_path()` и JSON датотеке које одговарају обрасцу `vendor/*/*.json` у односу на ту путању. Назив датотеке или директоријума мора да се разреши у подржани код. Директоријуми испод `lang/vendor/<package>/` који садрже само PHP преводе не одговарају тим обрасцима. Како се локал објављује описано је у [управљању преводима](./translations.md).

## Читање или постављање локала апликације {#read-or-set-the-application-locale}

```php
use LaravelLang\Locales\Facades\Locales;

if (Locales::isInstalled('fr')) {
    Locales::set('fr');
}

$canonicalCode = Locales::current(false);
$displayCode = Locales::current();
```

`set()` прихвата код, алијас, члан енума `Locale` или објекат `LocaleData`. Ако је локал инсталиран, метод поставља канонски код као локал Laravel-а. Користите `isAvailable()` да проверите да ли је код подржан, а `isInstalled()` када су потребне датотеке или заштита. `isProtected()` препознаје текући и резервни локал.

`current()` користи текући локал Laravel-а ако је препознат, а иначе `fallback()`. `fallback()` разрешава резервни локал подешен у Laravel-у и користи `en` ако је непознат. Оба метода прихватају исти аргумент `false` да врате канонске кодове.

Користите `fromAlias()` да разрешите алијас у канонски код, а `toAlias()` да добијете алијас за канонски код. Ови методи враћају непрепознате непразне ниске без измена. За валидацију улаза користите `isAvailable()`.

## Приказ локала за избор {#display-locale-choices}

`Locales::info('fr')` враћа објекат `LaravelLang\LocaleNames\LocaleData` са својствима `code`, `alias`, `native` и `localized`. За приказ се користи језик текућег локала апликације. Изоставите аргумент да добијете податке о текућем локалу. Познати локал можете потражити и пре инсталације. Непознат код изазива `LaravelLang\Exceptions\UnknownLocaleException`.

Користите `Locales::data()->installed()` да добијете инсталиране локале као `LocaleData` објекте. `data()` нуди и `available()`, `uninstalled()` и `protects()` за друге колекције, као и `current()` и `fallback()` за појединачне објекте. Избор језика може да шаље `alias` као вредност и приказује назив `native` као ознаку.

## Претрага назива локала, земаља и валута {#look-up-locale-country-and-currency-names}

Пакети `laravel-lang/locale-names`, `laravel-lang/country-names` и `laravel-lang/currency-names` пружају по једну услугу коју можете убризгати из Laravel контејнера. Common обухвата сва три пакета.

```php
use LaravelLang\CountryNames\CountryNames;
use LaravelLang\CurrencyNames\CurrencyNames;
use LaravelLang\LocaleNames\LocaleNames;

$locale = app(LocaleNames::class)->one('de', 'fr');
$locale->localized; // 'Französisch'

$country = app(CountryNames::class)->one('de', 'fr');
$country->code;      // 'FR'
$country->localized; // 'Frankreich'

$currency = app(CurrencyNames::class)->one('de', 'fr');
$currency->code;    // 'EUR'
$currency->numeric; // 978

$nativeLocaleNames = app(LocaleNames::class)->all(null, \SortDirection::Ascending);
```

Први параметар је језик приказа. За изворне називе користите `null`. Други параметар метода `one()` је локал реда, на пример `fr` у претходним примерима. При претрази назива земаља и валута враћени објекат садржи ISO код. Оба параметра прихватају канонске кодове, подешене алијасе и чланове енума `Locale`.

`all($forLocale)` враћа колекцију чији су кључеви канонски кодови локала. За сортирање по својству `localized` проследите `SortDirection::Ascending` или `SortDirection::Descending`. Свака вредност је објекат само за читање. Вредности за земље и валуте садрже и `locale` одговарајућег реда.
