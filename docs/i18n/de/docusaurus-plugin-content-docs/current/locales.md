---
title: "Locales und Namen"
description: "Ermittle unterstützte und installierte Locales, löse Aliasse auf und frage lokalisierte Namen ab."
---

Laravel-Lang verwendet kanonische Codes wie `en`, `fr` und `pt_BR` in Laravel selbst, in den Namen von Übersetzungsdateien und in Modelldatensätzen mit Übersetzungen. Ein Locale-Alias ist eine Bezeichnung für die Anwendung, etwa `pt-br` in einer URL. Definiere die Aliasse in [`localization_main.aliases`](./configure.md) als Zuordnung vom kanonischen Code zum Alias.

## Unterstützte und installierte Locales ermitteln {#find-supported-and-installed-locales}

`laravel-lang/locale-list` stellt das Backed Enum `LaravelLang\LocaleList\Locale` bereit. Seine Fälle entsprechen den [unterstützten kanonischen Codes](./available-locales.md).

```php
use LaravelLang\LocaleList\Locale;

Locale::French->value;        // 'fr'
Locale::tryFrom('pt_BR');     // Locale::PortugueseBrazil
Locale::values();            // Array kanonischer Codes
Locale::collection();        // Collection kanonischer Codes
Locale::collection(false);   // Collection von Enum-Fällen
```

`laravel-lang/locales` ergänzt die Fassade `LaravelLang\Locales\Facades\Locales`, die die Anwendungseinstellungen berücksichtigt. Die Methoden geben Collections von Zeichenketten zurück. Übergib `false` als Argument, um kanonische Codes zu erhalten. Standardmäßig wird jeder Code durch seinen konfigurierten Alias dargestellt, sofern vorhanden.

| Methode | Rückgabewert |
| --- | --- |
| `Locales::available()` | Alle bekannten Locales |
| `Locales::installed()` | Aus den Sprachdateien der Anwendung erkannte und geschützte Locales |
| `Locales::uninstalled()` | Verfügbare Locales, die noch nicht installiert sind |
| `Locales::protects()` | Effektive aktuelle Locale und Fallback-Locale ohne Duplikate |

Die Erkennung liest die Einträge direkt unter `lang_path()` sowie JSON-Dateien, die relativ zu diesem Pfad dem Muster `vendor/*/*.json` entsprechen. Der Datei- oder Verzeichnisname muss sich zu einem unterstützten Code auflösen lassen. Tiefer verschachtelte Übersetzungsverzeichnisse unter `lang/vendor/<package>/`, die ausschließlich PHP-Dateien enthalten, fallen aus diesem Suchmuster heraus. Unter [Übersetzungen verwalten](./translations.md) erfährst du, wie du eine Locale veröffentlichst.

## Locale der Anwendung abfragen oder setzen {#read-or-set-the-application-locale}

```php
use LaravelLang\Locales\Facades\Locales;

if (Locales::isInstalled('fr')) {
    Locales::set('fr');
}

$canonicalCode = Locales::current(false);
$displayCode = Locales::current();
```

`set()` akzeptiert einen Code, einen Alias, einen Fall des Enums `Locale` oder ein Objekt vom Typ `LocaleData`. Ist die Locale installiert, setzt die Methode Laravels Locale auf den kanonischen Code. Verwende `isAvailable()`, um zu prüfen, ob ein Code unterstützt wird, und `isInstalled()`, wenn Dateien oder Schutz erforderlich sind. `isProtected()` ermittelt die aktuelle Locale und die Fallback-Locale.

`current()` verwendet zunächst die aktuelle Laravel-Locale, sofern sie erkannt wird, und greift andernfalls auf `fallback()` zurück. `fallback()` löst die in Laravel konfigurierte Fallback-Locale auf und verwendet `en`, falls sie unbekannt ist. Beide Methoden akzeptieren `false` als Argument, um kanonische Codes zurückzugeben.

Verwende `fromAlias()`, um einen Alias zum kanonischen Code aufzulösen, und `toAlias()`, um den Alias eines kanonischen Codes abzurufen. Nichtleere, unbekannte Zeichenketten bleiben bei diesen Umwandlungen erhalten. Verwende `isAvailable()`, um Eingaben zu validieren.

## Locales zur Auswahl anzeigen {#display-locale-choices}

`Locales::info('fr')` gibt ein Objekt vom Typ `LaravelLang\LocaleNames\LocaleData` mit den Eigenschaften `code`, `alias`, `native` und `localized` zurück. Die Anzeigesprache entspricht der aktuellen Locale der Anwendung. Lasse das Argument weg, um die aktuelle Locale abzufragen. Eine bekannte Locale lässt sich bereits vor der Installation abfragen. Ein unbekannter Code löst `LaravelLang\Exceptions\UnknownLocaleException` aus.

Mit `Locales::data()->installed()` erhältst du die installierten Locales als `LocaleData`-Objekte. `data()` bietet außerdem `available()`, `uninstalled()` und `protects()` für weitere Collections sowie `current()` und `fallback()` für einzelne Objekte. Eine Sprachauswahl kann `alias` als Wert übermitteln und `native` als Bezeichnung anzeigen.

## Locale-, Länder- und Währungsnamen nachschlagen {#look-up-locale-country-and-currency-names}

Die Pakete `laravel-lang/locale-names`, `laravel-lang/country-names` und `laravel-lang/currency-names` stellen jeweils einen Dienst bereit, den du über den Laravel-Container injizieren kannst. Common enthält alle drei Pakete.

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

Der erste Parameter ist die Anzeigesprache. Verwende `null` für Namen in der jeweiligen Landessprache. Der zweite Parameter von `one()` ist die Locale des Datensatzes, in den obigen Beispielen etwa `fr`. Bei Länder- und Währungsabfragen steht der ISO-Code am zurückgegebenen Objekt. Beide Parameter akzeptieren kanonische Codes, konfigurierte Aliasse und Fälle des Enums `Locale`.

`all($forLocale)` gibt eine Collection mit den kanonischen Locale-Codes als Schlüsseln zurück. Übergib `SortDirection::Ascending` oder `SortDirection::Descending`, um nach der Eigenschaft `localized` zu sortieren. Jeder Wert ist ein schreibgeschütztes Objekt. Länder- und Währungswerte enthalten zusätzlich `locale` des Eintrags.
