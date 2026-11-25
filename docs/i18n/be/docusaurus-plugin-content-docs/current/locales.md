---
title: "Лакалі і назвы"
description: "Знаходзьце падтрыманыя і ўсталяваныя лакалі, пераўтварайце аліясы і шукайце лакалізаваныя назвы."
---

Laravel-Lang выкарыстоўвае кананічныя коды, такія як `en`, `fr` і `pt_BR`, у Laravel, імёнах файлаў перакладу і радках перакладаў мадэляў. Аліяс лакалі — гэта імя для выкарыстання ў прыкладанні, напрыклад `pt-br` у URL. Задайце адпаведнасць кананічных кодаў іх аліясам у [`localization_main.aliases`](./configure.md).

## Падтрыманыя і ўсталяваныя лакалі {#find-supported-and-installed-locales}

`laravel-lang/locale-list` дае enum `LaravelLang\LocaleList\Locale` з базавым тыпам. Яго варыянты адпавядаюць [падтрыманым кананічным кодам](./available-locales.md).

```php
use LaravelLang\LocaleList\Locale;

Locale::French->value;        // 'fr'
Locale::tryFrom('pt_BR');     // Locale::PortugueseBrazil
Locale::values();            // Масіў кананічных кодаў
Locale::collection();        // Калекцыя кананічных кодаў
Locale::collection(false);   // Калекцыя варыянтаў enum-а
```

`laravel-lang/locales` дадае фасад `LaravelLang\Locales\Facades\Locales`, які ўлічвае налады прыкладання. Гэтыя метады вяртаюць калекцыі радкоў. Перадайце `false` як аргумент, каб атрымаць кананічныя коды. Па змаўчанні кожны код прадстаўляецца наладжаным аліясам, калі той ёсць.

| Метад | Вяртае |
| --- | --- |
| `Locales::available()` | Усе вядомыя лакалі |
| `Locales::installed()` | Лакалі, распазнаныя паводле моўных файлаў прыкладання, а таксама абароненыя лакалі |
| `Locales::uninstalled()` | Даступныя лакалі, якіх няма сярод усталяваных |
| `Locales::protects()` | Фактычныя бягучая і рэзервовая лакалі без дублікатаў |

Пошук ахоплівае запісы непасрэдна ў `lang_path()` і файлы JSON, якія адпавядаюць `vendor/*/*.json` адносна гэтага шляху. Імя файла або каталога павінна адпавядаць падтрыманаму коду. Каталогі з перакладамі толькі ў PHP у `lang/vendor/<package>/` не адпавядаюць гэтым шаблонам пошуку. Каб апублікаваць лакаль, гл. [Кіраванне перакладамі](./translations.md).

## Чытанне або заданне лакалі прыкладання {#read-or-set-the-application-locale}

```php
use LaravelLang\Locales\Facades\Locales;

if (Locales::isInstalled('fr')) {
    Locales::set('fr');
}

$canonicalCode = Locales::current(false);
$displayCode = Locales::current();
```

`set()` прымае код, аліяс, варыянт enum-а `Locale` або аб’ект `LocaleData`. Калі лакаль усталявана, метад задае ў Laravel яе кананічны код. Для праверкі падтрымкі кода выкарыстоўвайце `isAvailable()`, а для праверкі наяўнасці файлаў або абароны — `isInstalled()`. Метад `isProtected()` вызначае бягучую і рэзервовую лакалі.

Метад `current()` выкарыстоўвае бягучую лакаль Laravel, калі распазнае яе, інакш выкарыстоўвае `fallback()`. Метад `fallback()` вызначае наладжаную ў Laravel рэзервовую лакаль і вяртае `en`, калі яна невядомая. Абодва метады прымаюць той жа аргумент `false` для вяртання кананічных кодаў.

Выкарыстоўвайце `fromAlias()`, каб атрымаць кананічны код з аліяса, і `toAlias()`, каб атрымаць аліяс з кананічнага кода. Нераспазнаныя непустыя радкі вяртаюцца без змен. Для праверкі ўваходных даных выкарыстоўвайце `isAvailable()`.

## Паказ варыянтаў лакалі {#display-locale-choices}

`Locales::info('fr')` вяртае аб’ект `LaravelLang\LocaleNames\LocaleData` з уласцівасцямі `code`, `alias`, `native` і `localized`. Назвы выводзяцца на бягучай мове прыкладання. Каб атрымаць даныя бягучай лакалі, апусціце аргумент. Звесткі пра вядомую лакаль можна атрымаць да яе ўсталявання. Невядомы код выклікае `LaravelLang\Exceptions\UnknownLocaleException`.

Выкарыстоўвайце `Locales::data()->installed()`, каб атрымаць усталяваныя лакалі як аб’екты `LocaleData`. Метад `data()` таксама прапануе `available()`, `uninstalled()` і `protects()` для іншых калекцый, а `current()` і `fallback()` — для асобных аб’ектаў. У спісе выбару мовы можна адпраўляць `alias`, а паказваць назву `native`.

## Пошук назваў лакаляў, краін і валют {#look-up-locale-country-and-currency-names}

Пакеты `laravel-lang/locale-names`, `laravel-lang/country-names` і `laravel-lang/currency-names` даюць па адным сэрвісе, які можна ўкараніць з кантэйнера Laravel. Common уключае ўсе тры.

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

Першы параметр задае мову паказу. Каб атрымаць назвы на іх роднай мове, выкарыстоўвайце `null`. Другі параметр `one()` задае лакаль запісу, напрыклад `fr` з прыкладаў вышэй. Пры пошуку краіны ці валюты код ISO знаходзіцца ў вернутым аб’екце. Абодва параметры прымаюць кананічныя коды, наладжаныя аліясы і варыянты enum-а `Locale`.

`all($forLocale)` вяртае калекцыю з кананічнымі кодамі лакаляў у якасці ключоў. Перадайце `SortDirection::Ascending` або `SortDirection::Descending`, каб адсартаваць яе паводле ўласцівасці `localized`. Кожнае значэнне — аб’ект толькі для чытання. Значэнні краін і валют таксама змяшчаюць `locale` запісу.
