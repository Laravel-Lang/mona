---
title: "Upgrade auf Mona"
description: "Aktualisiere deine Laravel-Lang-Abhängigkeiten, Konfiguration, veröffentlichten Übersetzungen und Anwendungsintegrationen."
---

Mona ist das Repository, aus dem die Laravel-Lang-Pakete gemeinsam veröffentlicht werden. Verwende weiterhin die einzelnen Composer-Pakete, die deine Anwendung benötigt.

:::info[Warum Version 20?]

Laravel-Lang ist aus `laravel-lang/lang` hervorgegangen. Die Pakete kamen zu unterschiedlichen Zeiten hinzu und hatten jeweils ihre eigene Versionsnummer und ihren eigenen Veröffentlichungsrhythmus. Bei einem Wechsel auf eine neue Hauptversion mussten wir mehrere Pakete aufeinander abstimmen. Wer die Pakete nutzte, musste herausfinden, welche Versionen zusammenpassten.

Dafür haben wir `laravel-lang/common` entwickelt. Das Paket gibt kompatible Abhängigkeiten vor und erleichtert so die Installation. Bei einem Upgrade konnten trotzdem mehrere Pakete mit eigenen Versionsnummern betroffen sein.

Welche Paketversionen hättest du damals zum Beispiel in einer Anwendung mit Laravel 11 und PHP 8.2 installiert?

<details>
<summary>Paketversionen vor Mona</summary>

| Paket | Version |
| --- | --- |
| Common | `6.9.0` |
| Actions | `1.13.2` |
| Attributes | `2.16.6` |
| Lang | `15.39.2` |
| MoonShine | `1.9.3` |
| HTTP Statuses | `3.13.2` |
| Currency Names | `1.11.0` |
| Locale Names | `2.10.0` |
| Country Names | `1.8.0` |
| Starter Kits | `1.17.4` |
| Locales | `2.11.0` |
| Models | `1.8.0` |
| Routes | `1.11.0` |
| JSON Fallback | `2.3.0` |
| Locale List | `1.7.0` |
| Config | `1.17.1` (Version aus der 1.x-Reihe, die andere Laravel-Lang-Pakete weiterhin nutzten) und `2.3.2` (letzte stabile Version vor Mona, die diese Pakete selten nutzten) |
| Publisher | `16.9.0` |

</details>

Mit dem Monorepository Mona veröffentlichen wir die Pakete gemeinsam unter einer einheitlichen Versionsnummer.

Publisher hatte die höchste Versionsnummer. In seiner bisherigen Reihe wäre `17.0` die nächste Hauptversion gewesen. Wir haben viel Arbeit und Herzblut in die Zusammenführung der Pakete gesteckt und die 20 gewählt, um diesen Meilenstein zu markieren. Man kann die Zahl auch als **Laravel-Lang 2.0** lesen. 🙂

:::

:::info[Locale-Codes und Aliasse]

Laravel-Lang verwendet jetzt kanonische Locale-Codes für gespeicherte Werte, etwa veröffentlichte Sprachpfade und Übersetzungsdatensätze für Modelle. Auch die aktive Locale und Fallback-Locale von Laravel verwenden kanonische Codes. Verwende zur Laufzeit Aliasse für URL-Segmente und die Sprachauswahl. Fassadenmethoden, die Locale-Codes zurückgeben, verwenden standardmäßig den konfigurierten Alias oder, falls keiner vorhanden ist, den kanonischen Code. Konfiguriere zum Beispiel `'pt_BR' => 'pt-br'` in `localization_main.aliases`, um `/pt-br/account` beizubehalten, während du nach `lang/pt_BR.json` veröffentlichst und `pt_BR` in der Datenbank speicherst.

:::

## Abhängigkeiten aktualisieren {#updating-dependencies}

Die aktuelle Version unterstützt Laravel 12 und 13 und benötigt PHP `^8.3`. Setze in `composer.json` deiner Anwendung die Versionsvorgabe für jedes der unten aufgeführten Pakete, das in `require` oder `require-dev` steht, auf `^20.0`. Hast du nur `laravel-lang/common` installiert, musst du nur diese Anforderung ändern.

Prüfe vor der Installation von `20.x` oder dem Upgrade, ob die benötigte Composer-Paketversion auf Packagist verfügbar ist. Das Mona-Release und die passenden Composer-Pakete können zu unterschiedlichen Zeiten erscheinen.

| Verwendung | Composer-Pakete |
| --- | --- |
| Komplettpaket | `laravel-lang/common` |
| Übersetzungspakete | `laravel-lang/lang`, `laravel-lang/starter-kits`, `laravel-lang/moonshine`, `laravel-lang/actions`, `laravel-lang/attributes`, `laravel-lang/http-statuses` |
| Veröffentlichung und Konfiguration | `laravel-lang/publisher`, `laravel-lang/config`, `laravel-lang/json-fallback`, `laravel-lang/library` |
| Anwendungsintegrationen | `laravel-lang/locales`, `laravel-lang/locale-list`, `laravel-lang/routes`, `laravel-lang/models` |
| Gemeinsame Hilfsbibliotheken | `laravel-lang/exceptions`, `laravel-lang/filesystem` |
| Paketvorlage | `laravel-lang/translations-template` |

Wenn deine Installation nur den Standardablauf für Übersetzungen nutzt, aktualisiere die Abhängigkeiten und veröffentliche die Übersetzungen erneut.

```bash
composer update
php artisan lang:update
```

Wenn du Provider registriert oder eigene Einstellungen veröffentlicht hast, Aliasse oder Übersetzungspakete von Drittanbietern nutzt oder Paket-APIs aufrufst, befolge vor der erneuten Veröffentlichung die Abschnitte, die auf deine Anwendung zutreffen.

## Umbenannte oder veraltete Abhängigkeiten ersetzen {#replace-renamed-or-retired-dependencies}

Aktualisiere direkte Abhängigkeiten von den alten Namenspaketen in `composer.json`, bevor du `composer update` ausführst.

| Bisheriges Composer-Paket | Paket mit Versionsanforderung `^20.0` |
| --- | --- |
| `laravel-lang/native-country-names` | `laravel-lang/country-names` |
| `laravel-lang/native-currency-names` | `laravel-lang/currency-names` |
| `laravel-lang/native-locale-names` | `laravel-lang/locale-names` |

Common hängt bereits von allen drei Namenspaketen ab. Aktualisiere auch deinen PHP-Code. Siehe [APIs zur Namensabfrage ersetzen](#replace-the-name-lookup-apis).

Suche für jedes Drittanbieter-Plugin nach einer Version, die mit dem neuen Publisher kompatibel ist. Stelle jede Verwendung von `laravel-lang/translator` oder `laravel-lang/status-generator` um, bevor du diese Abhängigkeiten entfernst. Siehe [Übersetzungswerkzeuge](#update-json-fallback-and-translation-tooling).

## Service Provider aktualisieren {#update-service-providers}

Laravel erkennt die Provider von Config, Publisher, Routes und Models anhand der Paketmanifeste. Wenn du die automatische Erkennung verwendest, aktualisiere Referenzen auf alte Provider-Klassen in Anwendungscode und Tests. Registriert deine Anwendung die Pakete manuell, verwende die Ersatzklassen in derselben Provider-Liste, etwa `bootstrap/providers.php` oder `config/app.php`.

- `LaravelLang\Config\ServiceProvider` → `LaravelLang\Config\LangConfigServiceProvider`
- `LaravelLang\Publisher\ServiceProvider` → `LaravelLang\Publisher\LangPublisherServiceProvider`
- `LaravelLang\Routes\ServiceProvider` → `LaravelLang\Routes\LangRoutesServiceProvider`
- `LaravelLang\Models\ServiceProvider` → `LaravelLang\Models\LangModelsServiceProvider`

Entferne manuelle Registrierungen von `LaravelLang\Locales\LaravelLangLocalesServiceProvider` und den alten Klassen `ServiceProvider` unter `LaravelLang\Actions`, `LaravelLang\Attributes`, `LaravelLang\HttpStatuses`, `LaravelLang\Lang`, `LaravelLang\MoonShine` und `LaravelLang\StarterKits`. Diese Übersetzungspakete werden anhand ihrer Manifeste erkannt.

Wenn du den JSON-Fallback verwendest, ersetze `LaravelLang\JsonFallback\TranslationServiceProvider` an der Registrierungsstelle durch `LaravelLang\JsonFallback\LangJsonFallbackServiceProvider`. So bleibt die Überschreibung des Laravel-Übersetzungsproviders aktiv.

Aktualisiere veraltete Provider-Referenzen vor `composer update`, damit die Paketerkennung die Anwendung starten kann. Falls ein Composer-Skript weiterhin fehlschlägt, weil es eine alte Klasse lädt, führe `composer update --no-scripts` aus, aktualisiere die Referenzen und starte mit `composer dump-autoload` die Erkennung erneut.

## Konfiguration migrieren {#migrate-configuration}

Wenn du `config/localization.php` angepasst hast, behalte die Datei beim Übertragen der Änderungen als Referenz. Die Pakete laden die neuen Standardwerte automatisch. Veröffentliche die [Konfigurationsdateien](./configure.md), die du bearbeiten möchtest. Der Tag `localization` veröffentlicht alle fünf Dateien. Verwende für eine einzelne Datei ihren Namen ohne `.php` als Tag.

```bash
php artisan vendor:publish --tag=localization
```

### Aliasse, Übersetzungsstile und Dateiformatierung {#aliases-translation-styles-and-file-formatting}

- Kopiere die Zuordnung `aliases` nach `config/localization_main.php`. Behalte kanonische Codes als Schlüssel und Aliasse als Werte bei, etwa `'de_CH' => 'de-CH'`.
- Ersetze `inline` in `config/localization_main.php` durch `styles`. Setze `'styles' => ['default']`, um die bisherige Vorgabe mit standardmäßigen Validierungsmeldungen beizubehalten. Wenn du Inline-Meldungen verwendet hast, behalte die neue Vorgabe `['default', 'inline']` bei. Entferne `LOCALIZATION_INLINE` und `LANG_PUBLISHER_INLINE` aus den Umgebungen, in denen du sie gesetzt hast.
- Entferne den Schlüssel `align` und die Umgebungsvariablen `LOCALIZATION_ALIGN` und `LANG_PUBLISHER_ALIGN`. Um die Operatoren `=>` in veröffentlichten PHP-Arrays auszurichten, konfiguriere den PHP-Codeformatter deines Projekts und führe ihn nach der Veröffentlichung für die Sprachdateien aus. Beispielsweise kann die Regel `binary_operator_spaces` von Laravel Pint `align_single_space_minimal` auf `=>` anwenden.

### Intelligente Zeichensetzung {#smart-punctuation}

Wenn du die intelligente Zeichensetzung aktiviert hast, benenne `LOCALIZATION_SMART_ENABLED` in `LOCALIZATION_PUNCTUATION_ENABLED` um und behalte den Wert `true` bei. Hast du `smart_punctuation.enable` direkt in PHP gesetzt, verschiebe den booleschen Wert zum Schlüssel `enabled` auf der obersten Ebene in `config/localization_punctuation.php`. Standardmäßig ist die Funktion deaktiviert.

Verschiebe angepasste Anführungszeichen aus `smart_punctuation.common` und `smart_punctuation.locales` in die Arrays `common` und `locales` auf der obersten Ebene dieser Datei. Verwende in `locales` kanonische Codes als Schlüssel. Die Anführungszeichen einer Locale überschreiben die entsprechenden Schlüssel in `common`. Wenn du ein älteres französisches Beispiel mit `&nbsp;` übernommen hast, verwende in den Zeichenketten ein geschütztes Leerzeichen oder `"\u{00A0}"`.

### Lokalisierte Routen {#localized-routes}

Übertrage deine abweichenden Einstellungen aus `routes` in der alten Konfiguration nach `config/localization_routes.php`.

| Angepasste Einstellung | Änderung in `config/localization_routes.php` | Umzubenennende Umgebungsvariable |
| --- | --- | --- |
| Locale-Parameter, Header, Cookie, Session-Schlüssel oder Spalte im Benutzermodell | Kopiere deine Einstellungen für `names` und behalte die übrigen Standardeinträge bei. | |
| Präfix vor lokalisierten Routennamen | Verschiebe `name_prefix` nach `prefix`. Bei einem leeren Wert wird `localized.` verwendet. | `LOCALIZATION_NAME_PREFIX` → `LOCALIZATION_PREFIX` |
| Weiterleitungen von der Route mit Präfix | Verschiebe `redirect_default` nach `redirect`. | `LOCALIZATION_REDIRECT_DEFAULT` → `LOCALIZATION_REDIRECT` |
| Ob der URL-Helper die Fallback-Locale ausblendet | Verschiebe `hide_default` nach `hide`. | `LOCALIZATION_HIDE_DEFAULT` → `LOCALIZATION_HIDE` |
| Middleware für lokalisierte Routengruppen | Verschiebe `group.middlewares.default` nach `middlewares.default` für Routen ohne Präfix und `group.middlewares.prefix` nach `middlewares.prefix` für Routen mit Präfix. Aktualisiere die Klassennamen wie in der [Routenmigration](#update-localized-routes-and-middleware) beschrieben. | |

### Modellübersetzungen {#model-translations}

Übertrage deine abweichenden Einstellungen aus `models` nach `config/localization_models.php`. Kopiere angepasste Werte für `suffix` und `helpers` auf die oberste Ebene. Verwende statt des alten Schlüssels `filter.enabled` einen booleschen Wert für `filter`. Wenn du Filterung und IDE-Helper-Pfad über die Umgebung steuerst, behalte `LOCALIZATION_FILTER_ENABLED` und `VENDOR_PATH` bei.

Die Modellbefehle durchsuchen jetzt standardmäßig `app/Models`. Wenn du dich zuvor auf die Suche in ganz `app/` verlassen oder `localization-private.models.directory` selbst gesetzt hast, trage jedes zu durchsuchende Verzeichnis in das Array `directories` ein. Setze zum Beispiel `'directories' => [app_path()]`, um wie bisher zu suchen.

### Konfiguration für Anwendungscode {#configuration-used-by-application-code}

- Ersetze Lese- und Schreibzugriffe über `LaravelLang\Config\Facades\Config` durch Laravel-Konfigurationsaufrufe mit den neuen Schlüsseln. Ersetze beispielsweise `Config::shared()->models->filter->enabled` durch `config('localization_models.filter')`. Wenn du typisierte Zugriffe bevorzugst, injiziere `LaravelLang\Config\Services\ModelLangConfig` und lies seine Eigenschaft `filter`. Für Routeneinstellungen kannst du `RouteLangConfig` aus demselben Namespace injizieren.
- Liest dein Code Locale-Metadaten aus `localization-private.map`, lies künftig `localization_map.locales`. Kopiere eigene Einstellungen in das Array `locales` in `config/localization_map.php`.
- Ersetze die Registrierung über `localization-private.plugins` und `localization-private.packages` durch ein [Plugin-Manifest](./plugins.md).
- Entferne den alten Block `translators` und frühere Einstellungen `TRANSLATION_*`, die ausschließlich `laravel-lang/translator` verwendet hat.

Entferne `config/localization.php` und führe `php artisan config:clear` aus, nachdem du deine Einstellungen übertragen und die Anwendungsreferenzen aktualisiert hast.

## Sprachdateien in kanonische Pfade verschieben {#move-language-files-to-canonical-paths}

1. Setze `APP_LOCALE` und `APP_FALLBACK_LOCALE` auf kanonische Codes. Verschiebe die Aliasse nach `localization_main.aliases`, damit URLs und Locale-Eingaben weiterhin funktionieren.
2. Benenne Dateien und Verzeichnisse mit Alias, etwa `lang/pt-br.json` und `lang/pt-br/`, in `lang/pt_BR.json` und `lang/pt_BR/` um. Suche auch unter `lang/vendor/<package>/` nach denselben Aliasen. Wenn beide Namen vorhanden sind, führe ihre Inhalte zusammen und kläre Konflikte zwischen eigenen Übersetzungen, bevor du den Alias-Pfad entfernst.
3. Installiere mit `php artisan lang:add <locale>` jede Locale, die nur in PHP-Übersetzungen eines Drittanbieters vorkommt. Die Liste installierter Locales berücksichtigt Einträge direkt im Sprachverzeichnis, JSON-Dateien unter `lang/vendor/<package>/` sowie die effektive aktuelle Locale und die Fallback-Locale.
4. Führe `php artisan lang:update` nach der Migration von Konfiguration und Plugins aus und prüfe die Änderungen. Der Publisher kann Schlüssel aus Integrationen behalten, die keine Datei mehr veröffentlichen. Behalte die noch genutzten Schlüssel bei und entferne veraltete nach Prüfung ihrer Aufrufstellen. Siehe [Veröffentlichungsskripte und eigene Übersetzungen](#update-publication-scripts-and-keep-custom-translations) zum Neugenerieren oder Beibehalten der Dateien.

## Integrationen aktualisieren {#update-integrations}

### Locales {#locales}

Behalte den Import `LaravelLang\Locales\Facades\Locales` bei und passe die Aufrufe an die Daten an, die der jeweilige Aufrufer benötigt.

| Bisheriger Aufruf | Ersatz |
| --- | --- |
| `Locales::available()`, `installed()` oder `protects()` mit Objekten als Rückgabewerten | Rufe dieselbe Methode auf `Locales::data()` auf. |
| `Locales::raw()->available()`, `installed()` oder `protects()` | Rufe dieselbe Methode direkt auf `Locales` auf, um eine Collection von Zeichenketten mit Aliasen zu erhalten. Übergib `false` für kanonische Codes und rufe `->all()` auf, wenn der Aufrufer ein Array benötigt. |
| `Locales::installed(false)` oder `Locales::raw()->installed(false)` | `false` wählt jetzt kanonische Codes aus. Die aktuelle Locale und die Fallback-Locale sind auch ohne Sprachdateien enthalten. Ersetze auch das alte benannte Argument `withProtects: false`. Schließe beide Locales mit `Locales::installed(false)->diff(Locales::protects(false))->values()` aus. Verwende `Locales::data()->installed()->reject(fn ($locale) => Locales::isProtected($locale))->values()` für Objekte. |
| `Locales::notInstalled()` oder `Locales::raw()->notInstalled()` | Verwende `Locales::data()->uninstalled()` für Objekte oder `Locales::uninstalled()` für Zeichenketten mit Aliasen. Übergib `false` für kanonische Zeichenketten. |
| `Locales::getCurrent()` oder `Locales::getDefault()` | Verwende `Locales::data()->current()` für ein DTO oder `Locales::current()` für eine Zeichenkette. Ersetze `Locales::raw()->getDefault()` durch `Locales::current()`. Wenn du mit `Locales::raw()->getCurrent()` auf den von Laravel gehaltenen Wert zugegriffen hast, verwende `app()->getLocale()`. |
| `Locales::getFallback()` oder `Locales::raw()->getFallback()` | Verwende `Locales::data()->fallback()` für ein DTO oder `Locales::fallback()` für eine Zeichenkette. Eine unbekannte Fallback-Locale wird jetzt zu Englisch aufgelöst. Eine unbekannte aktive Locale wird zur effektiven Fallback-Locale aufgelöst. |
| `Locales::get($locale)` oder `Locales::raw()->get($locale)` | Verwende `isInstalled()` und wähle die Fallback-Locale ausdrücklich. Verwende für eine unterstützte, möglicherweise nicht installierte Locale `isAvailable()` und dann `info()`. |
| `Locales::info($locale)` oder `Locales::raw()->info($locale)` | Verwende `Locales::info($locale)` und lies `->alias` oder `->code`, wenn du eine Zeichenkette brauchst. Übergib `null` für die aktuelle Locale. Behandle bei unbekannten oder leeren Codes `LaravelLang\Exceptions\UnknownLocaleException` oder prüfe vor dem Nachschlagen `isAvailable()`. |

Um das bisherige Fallback-Verhalten von `get()` beizubehalten, wähle beim Aufrufer für nicht installierte oder ungültige Eingaben die aktuelle Locale.

```php
use LaravelLang\Locales\Facades\Locales;

$locale = Locales::acceptsInput($requested) && Locales::isInstalled($requested)
    ? Locales::info($requested)
    : Locales::data()->current();

$code = $locale->code;   // Für Pfade, Datenbankzeilen und andere dauerhaft gespeicherte Daten.
$alias = $locale->alias; // Für URLs und die Sprachauswahl.
```

Aktualisiere Anfrage-Handler, die bisher das Ergebnis von `raw()->get()` an `app()->setLocale()` übergeben haben. Verwende nach dem Einlesen des Anfragewerts `Locales::set($locale)`. Die Methode akzeptiert installierte Locale-Codes, konfigurierte Aliasse, Enum-Fälle und das neue DTO und weist Laravel den kanonischen Code zu. Beim Anfragewert `pt-br` erhält Laravel `pt_BR`, während `Locales::current()` den Wert `pt-br` zurückgibt. Rufe `Locales::current(false)` oder `Locales::fallback(false)` auf, wenn du kanonische Codes benötigst.

Für andere Arten der Speicherung in der Anwendung prüfe den Wert mit `isAvailable()` oder `isInstalled()` und ermittle dann den kanonischen Code mit `fromAlias()`. Verwende `toAlias()`, um einen kanonischen Code zur Anzeige zu formatieren. Ersetze die alte Methode `Services\Resolver` durch `acceptsInput()`, um akzeptierte PHP-Typen zu prüfen, oder durch `normalize()`, um die Zeichenkette aus einem typisierten Wert zu lesen. `normalize()` lässt Zeichenketten unverändert, auch einen Alias wie `pt-br`.

Ersetze `LaravelLang\Locales\Data\LocaleData` in Importen, Typhinweisen und Tests durch `LaravelLang\LocaleNames\LocaleData`. Das neue schreibgeschützte Objekt hat die Zeichenketteneigenschaften `code`, `alias`, `native` und `localized`. Verwende für URLs und UI-Werte `alias` anstelle der alten Eigenschaft `code`. Verwende `Locale::from($data->code)`, wenn du bisher `$data->locale` gelesen hast, und importiere `LaravelLang\LocaleList\Locale`. Lies `type`, `regional` und `direction` aus `config("localization_map.locales.{$data->code}", [])`, wenn deine Vorlagen oder Formatierer diese Daten nutzen. Lege eigene Vorgaben für fehlende Einträge fest. Um die bisherigen Vorgaben beizubehalten, verwende `Latn` für die Schrift, den kanonischen Code für die Region und `Direction::LeftToRight` für die Schreibrichtung.

Entferne die Flags `withCountry`, `withCurrency`, `withCountries` und `withCurrencies`. Frage Land und Währung für den Locale-Datensatz ausdrücklich ab. Ersetze die Importe der alten Typen `LaravelLang\Locales\Data\CountryData` und `CurrencyData` durch die Datenklassen der jeweiligen Namenspakete.

```php
use LaravelLang\CountryNames\CountryNames;
use LaravelLang\CurrencyNames\CurrencyNames;
use LaravelLang\Locales\Facades\Locales;

$locale = Locales::info('pt_BR');
$displayLanguage = Locales::current(false);

$country = app(CountryNames::class)->one($displayLanguage, $locale->code);
$currency = app(CurrencyNames::class)->one($displayLanguage, $locale->code);
```

Wenn du `localizedLocales()`, `localizedCountries()` oder `localizedCurrencies()` auf dem alten Dienst aufgerufen hast, verwende `all(null)` und `all($displayLanguage)` des passenden Namensdienstes für die Werte in der jeweiligen Sprache beziehungsweise der Anzeigesprache. Ersetze direkte Injektionen der alten Klassen `Services\Locales` oder `Services\RawLocales` durch die Fassade oder die entsprechenden Klassen `Services\MainService` und `Services\DataService`. Die neuen Service-APIs findest du unter [Locales und Namen](./locales.md).

#### Helper des Locale-Enums {#locale-enum-helpers}

Die Enum-Fälle `Locale` und `Direction` behalten ihre Werte. Ersetze entfernte Helper aus `archtechx/enums` beim Aktualisieren eigener Locale-Listen oder Auswahlelemente.

| Bisheriger Helper | Ersatz |
| --- | --- |
| `names()` | `array_column(Locale::cases(), 'name')` |
| `options()` | Erstelle die Zuordnung von Namen zu Werten mit `array_column(Locale::cases(), 'value', 'name')`. |
| `stringOptions()` | Erzeuge die Auswahloptionen in der View aus `Locale::cases()`. Nutze für einen kanonischen Code den Wert des Enum-Falls oder `Locales::toAlias($case)` für den Anzeigealias. |
| `fromName()` oder `tryFromName()` | Suche den Fall anhand von `name` in `Locale::cases()` und behandle fehlende Treffer. Verwende für einen Backed-Wert wie `pt_BR` entweder `Locale::from()` oder `Locale::tryFrom()`. |
| `Locale::onlyValues()` | Erstelle die Zuordnung der Werte zu sich selbst mit `array_column(Locale::cases(), 'value', 'value')`. Verwende für eine Werteliste `Locale::values()`. |
| `Direction::values()` | Verwende `array_column(Direction::cases(), 'value')`. |

### APIs zur Namensabfrage ersetzen {#replace-the-name-lookup-apis}

Aktualisiere die Importe von `LaravelLang\NativeCountryNames`, `LaravelLang\NativeCurrencyNames` und `LaravelLang\NativeLocaleNames` auf `LaravelLang\CountryNames`, `LaravelLang\CurrencyNames` und `LaravelLang\LocaleNames`. Ermittle `CountryNames`, `CurrencyNames` oder `LocaleNames` über den Laravel-Container oder injiziere den Dienst in deine Klasse.

| Bisherige Verwendung | Ersatz |
| --- | --- |
| `SomeNames::get($displayLocale)` | `$names->all($displayLocale)` |
| `SomeNames::get()` für Namen in der jeweiligen Sprache | `$names->all(null)` |
| `SortBy::None` | Lasse das Sortierargument weg. |
| `SortBy::Key` | Rufe `->sortKeys()` auf der zurückgegebenen Collection auf. |
| `SortBy::Value` | Übergib `SortDirection::Ascending` an `all()`, um nach dem lokalisierten Namen zu sortieren. |

Rufe einen bekannten Eintrag mit `one($displayLocale, $entryLocale)` ab. Der Suchschlüssel ist eine Locale wie `fr`. Beispielsweise liefert `app(CountryNames::class)->one('de', 'fr')` das Land für Französisch mit deutschem Namen und dem Wert `FR` für `code`. Die Länder- und Währungs-DTOs befinden sich im Stamm-Namespace ihres Pakets und stellen den Suchschlüssel als `locale` bereit. Ihre Felder `code` enthalten weiterhin den Länder- oder Währungscode.

Alle drei Dienste geben Collections mit kanonischen Locale-Codes als Schlüsseln zurück. Wenn du das Array von Zeichenketten des alten Pakets für Locale-Namen verwendet hast, bilde jedes DTO auf `localized` ab. Wenn du `toArray()` auf den alten Länder- oder Währungs-DTOs aufgerufen oder ihre Eigenschaften geändert hast, erzeuge das Anwendungsarray ausdrücklich. Die neuen DTOs sind schreibgeschützt.

```php
use LaravelLang\LocaleNames\LocaleData;
use LaravelLang\LocaleNames\LocaleNames;

$options = app(LocaleNames::class)
    ->all('de', SortDirection::Ascending)
    ->map(fn (LocaleData $locale): string => $locale->localized)
    ->all();
```

Prüfe die vom Benutzer ausgewählten Locales anhand des jeweiligen Katalogs, bevor du `one()` aufrufst. Eine unbekannte Anzeige-Locale kann bei `all()` zu einer leeren Liste führen. Übergib `null`, wenn die Anwendung Namen in der jeweiligen Sprache benötigt. Wenn der Code ein eigenes Backed Enum verwendet hat, übergib dessen Zeichenkettenwert. [Locales und Namen](./locales.md)

### Lokalisierte Routen und Middleware aktualisieren {#update-localized-routes-and-middleware}

Importiere den Helper mit Namespace in PHP-Dateien und Blade-Vorlagen, die lokalisierte URLs generieren. Du kannst auch `\LaravelLang\Routes\localizedRoute()` direkt aufrufen.

```php
use LaravelLang\Locales\Facades\Locales;

use function LaravelLang\Routes\localizedRoute;

$url = localizedRoute('products.show', [
    'product' => 42,
    'locale' => Locales::current(),
]);
```

`Route::localizedGroup()` und `LocalizationRoute::group()` registrieren sowohl Routen ohne Präfix als auch Routen mit Locale-Präfix. Behalte die Gruppe bei und überprüfe Aufrufe des URL-Helpers. Er verwendet den Fallback-Alias, wenn der Locale-Parameter fehlt. Übergib für eine Sprachauswahl den gewünschten Alias ausdrücklich mit `Locales::toAlias($locale)` oder `$localeData->alias`. Eine übergebene Locale-Zeichenkette wird unverändert verwendet. Passe den Array-Schlüssel an `localization_routes.names.parameter` an, wenn du den Parameter umbenannt hast.

Ersetze Importe und Registrierungen in deinen Routen, `bootstrap/app.php` und eigenen Middleware-Listen. Alle Klassen in dieser Tabelle befinden sich in `LaravelLang\Routes\Middlewares`.

| Bisherige Middleware | Ersatz |
| --- | --- |
| `LocalizationByCookie` | `LocalizationByCookieMiddleware` |
| `LocalizationByHeader` | `LocalizationByHeaderMiddleware` |
| `LocalizationByModel` | `LocalizationByModelMiddleware` |
| `LocalizationBySession` | `LocalizationBySessionMiddleware` |
| `LocalizationByParameter` | `LocalizationByParameterMiddleware` |
| `LocalizationByParameterWithRedirect` | `LocalizationByParameterWithRedirectMiddleware` |
| `LocalizationByParameterPrefix` | `LocalizationByParameterPrefixMiddlewareMiddleware` |

Wenn du Modell-Middleware mit einem Guard erstellt hast, übergib ihn als Laravel-Middleware-Parameter, etwa `LocalizationByModelMiddleware::class . ':admin'`. Der Benutzer-Resolver erwartet ein Eloquent-Modell. Injiziere in eigenen Middleware-Unterklassen `LaravelLang\Config\Services\RouteLangConfig`, lies die Quellennamen aus `$this->routeConfig->name` und gib aus `detect()` den Wert `int|string|null` zurück. Ersetze direkte Referenzen auf `LaravelLang\Routes\Services\Route` durch `LaravelLang\Routes\Services\RouteService`.

Teste jede von der Anwendung verwendete Quelle. Die Header-Erkennung prüft installierte Sprachen mit PHP Intl in der Reihenfolge aus `Accept-Language`. Sie versucht zuerst das vollständige Sprach-Tag und dann die Basissprache. Eine spätere Middleware kann die Locale einer früheren Quelle überschreiben. Soll die URL bei Routen mit Präfix maßgeblich sein, verwende dort nur die Middleware zur Locale-Auswahl aus der URL.

Teste URL-Generierung und Anfragen separat. `localization_routes.hide` unterdrückt das Segment der Fallback-Locale bei der URL-Generierung. `localization_routes.redirect` leitet Anfragen mit Präfix für die aktuelle Locale zur Route ohne Präfix um. Eine benannte Route mit Präfix leitet bei einer ungültigen oder nicht installierten Locale ebenfalls zur Route ohne Präfix um.

Verwende in Listenern für `LocaleHasBeenSetEvent` den Wert `$event->locale->code` für den kanonischen Code und `$event->locale->alias` für die Anzeige. Die Nutzlast ist jetzt `LaravelLang\LocaleNames\LocaleData`. Die Middleware löst das Ereignis aus, wenn sich die gewählte Locale ändert, und speichert den kanonischen Code in der Session. Ersetze manuelle Aufrufe von `LocaleHasBeenSetEvent::dispatch($data)` durch `event(new LocaleHasBeenSetEvent($data))`. [Lokalisierte Routen](./routes.md)

### Code und Daten für Modellübersetzungen migrieren {#migrate-model-translation-code-and-data}

Prüfe die Spalte `locale` jeder Übersetzungstabelle auf Datensätze mit Aliasen. Stelle die Datensätze für jeden Alias in einer Datenmigration auf den kanonischen Code um. Prüfe vor der Aktualisierung, ob für dieselbe `item_id` bereits ein Datensatz mit dem kanonischen Code existiert. Führe die Werte, die du behalten möchtest, in einem Datensatz zusammen, um die Eindeutigkeitsanforderung `(item_id, locale)` zu erfüllen. Versionen des alten Traits haben bereits kanonische Codes geschrieben. Richte die Migration deshalb nach den tatsächlich gespeicherten Werten aus.

Aktualisiere die Importe und die generierten Klassen.

| Bisherige Verwendung | Ersatz |
| --- | --- |
| `LaravelLang\Locales\Data\LocaleData` | `LaravelLang\LocaleNames\LocaleData` |
| `LaravelLang\Models\Casts\TrimCast` | `LaravelLang\Models\Casts\TrimStringCast` |
| `LaravelLang\Models\Exceptions\UnavailableLocaleException` | `LaravelLang\Exceptions\UnknownLocaleException` |
| `LaravelLang\Models\Exceptions\AttributeIsNotTranslatableException` oder `UnknownModelPathException` | Derselbe Klassenname in `LaravelLang\Exceptions`. |
| `translationModelName()` | `translationModel(): string`. Aktualisiere Überschreibungen, die eine eigene Übersetzungsklasse auswählen. |
| `getTranslationTable()` | Verwende `translationQualifyColumn($column)` für einen qualifizierten Spaltennamen oder `translationsRaw()->getRelated()->getTable()` für den Tabellennamen. |

`getTranslation()`, `setTranslation()`, `hasTranslated()` und `forgetTranslation()` akzeptieren eine Locale-Zeichenkette, `Locale` oder das neue `LocaleData`. Installiere jede ausdrücklich angegebene Locale vor dem Aufruf dieser Methoden. Ein Lesezugriff ohne ausdrückliche Locale verwendet den Fallback, wenn der aktuelle Wert `null` ist. Ein Lesezugriff mit ausdrücklicher Locale liest deren Wert direkt. Wenn du die tiefer liegende Methode `translation()` aufrufst, übergib eine Locale-Zeichenkette oder `null`. Wandle ein DTO mit `$locale->code` um.

```php
$article->setTranslation('title', 'Schweizer Titel', 'de-CH')->save();
$article->translation('de_CH');
```

Mit einem Alias `de_CH` → `de-CH` und installiertem Schweizer Hochdeutsch greifen beide Aufrufe auf den unter `de_CH` gespeicherten Datensatz zu. Übergib beim Befüllen eines übersetzten Felds mit mehreren Werten eine Zuordnung nach Locale wie `['en' => 'Title', 'de-CH' => 'Schweizer Titel']`. Indizierte Listen lösen jetzt `InvalidLocaleInputException` aus. Eigener Code, der die geladene Relation `translations` setzt, sollte eine Laravel-Collection von Übersetzungsmodellen liefern.

Rufe `translated()`, `whereTranslation()` und verwandte Scopes über den Eloquent Query Builder auf. Übergib als Locale-Zeichenkette des Scopes einen Code oder Alias. Verwende für `orderByTranslation()` entweder `'asc'`, `'desc'` oder `SortDirection`. Wenn du mit `whereTranslation()` oder `whereTranslationLike()` eine andere als die aktuelle Locale oder Fallback-Locale abfragst, setze `localization_models.filter` auf `false` oder frage `translationsRaw()` direkt ab.

Aktualisiere Ereignisempfänger, die `$event->locale->value` aufrufen oder `$event->locale` mit einem Enum `Locale` vergleichen. `TranslationHasBeenSetEvent` und `TranslationHasBeenForgetEvent` enthalten jetzt einen Alias als Zeichenkette für eine ausdrücklich angegebene Locale. Das Set-Ereignis enthält `null`, wenn der Aufrufer die Locale weglässt. Wandle einen Alias mit `Locales::fromAlias()` um, bevor du ihn als dauerhaften Schlüssel verwendest. Das Set-Ereignis wird beim Zuweisen des Attributs vor `save()` ausgelöst. Verwende `event(new …)`, um eines der drei Modellübersetzungsereignisse manuell auszulösen.

Wenn du generierte IDE-Helper beibehalten möchtest, führe `php artisan lang:models-helper` aus, nachdem du die Importe und Suchpfade für Modelle aktualisiert hast. [Modellübersetzungen](./models.md)

### Veröffentlichungsskripte aktualisieren und eigene Übersetzungen behalten {#update-publication-scripts-and-keep-custom-translations}

| Bisheriger Befehl oder Annahme | Migration |
| --- | --- |
| `lang:add <locale>` aktualisierte nur die hinzugefügten Locales | Der Befehl aktualisiert jetzt alle installierten Locales. Gib in Skripten die Codes ausdrücklich an und berücksichtige Änderungen an bestehenden Sprachdateien. |
| `lang:reset` | Verwende `lang:update` zum Zusammenführen. Sichere für einen vollständigen Neuaufbau eigene Einträge, entferne die neu zu generierenden Dateien und führe dann `lang:add` mit den konkreten Codes der betreffenden Locales aus. |
| `lang:rm <locale> --force` | Verwende `lang:rm <locale>`. Die aktuelle Locale und Fallback-Locale von Laravel sind geschützt. |
| Nur den Exit-Code von `lang:rm` prüfen | Prüfe jedes Ergebnis. `DONE` bestätigt die Entfernung. `FAILED` zeigt an, dass nach dem Löschversuch ein Pfad übrig bleibt. Auch `PROTECTED`, `INVALID` und `SKIP` können bei einem erfolgreichen Exit-Code auftreten. |

Wenn du Artisan über die Befehlsklasse aufrufst, ersetze `LaravelLang\Publisher\Console\Add`, `Remove` und `Update` durch `AddCommand`, `RemoveCommand` und `UpdateCommand`. Für einen eigenen Text-Decorator implementiere und binde `LaravelLang\Publisher\Contracts\Decorator` anstelle von `Contracts\TextDecorator`. Seine Methode `convert(string $locale, string $value): string` erhält die kanonische Locale.

Führe `lang:update` zum ersten Mal mit bestehenden Sprachdateien unter Versionskontrolle aus. Der Publisher führt die Übersetzungen mit den vorhandenen Zielen zusammen und ersetzt übereinstimmende Schlüssel, auch numerische. Lege eigene Übersetzungen nach Möglichkeit in einer separaten Übersetzungsdatei der Anwendung ab. Um ein ganzes generiertes Ziel selbst zu pflegen, ergänze sein Pfadmuster in `localization_main.skip`. Prüfe jeden Wert, der zum Quelltext zurückkehrt. Der Publisher verwendet diesen Text, wenn die Wörterbücher keine Übersetzung enthalten.

Wenn ein früheres Update die Schlüssel in deinen `http-statuses.php`-Dateien neu indiziert hat, sichere und entferne die betroffenen generierten Dateien. Entferne `'*/http-statuses.php'` aus `localization_main.skip`, falls du den Eintrag als Behelf hinzugefügt hast. Führe `php artisan lang:update` aus, um die Dateien neu zu erzeugen, und stelle dann eigene Übersetzungen unter den vorgesehenen Statuscodes wieder her. [Übersetzungen verwalten](./translations.md)

### Alte Plugin-Registrierung ersetzen {#replace-legacy-plugin-registration}

Wenn du ein Übersetzungspaket eines Drittanbieters verwendest, aktualisiere es auf eine manifestbasierte Version. Wenn du ein Paket pflegst, entferne den Provider und den Composer-Discovery-Eintrag, die ein altes `LaravelLang\Publisher\Plugins\Provider` registrieren. Konfiguriert der Provider darüber hinaus andere Laravel-Dienste, behalte deren Registrierungen in einem regulären Laravel Service Provider bei.

1. Füge `laravel_lang.json` zum Stammverzeichnis des unter `vendor/` installierten Pakets hinzu. Wandle jeden Eintrag `Plugin::files()` in einen Pfad `source` relativ zum Paketverzeichnis `source/` und einen Pfad `target` relativ zum Sprachverzeichnis der Anwendung um. Verwende `{locale}` im Zielpfad.
2. Übernimm die Composer-Zuordnungsregeln ins Manifest. Verwende `vendor` und `version` für eine installierte Abhängigkeit und einen separaten Eintrag mit `project: true` für ein Composer-Hauptprojekt. Hat dein altes Plugin `$with_project_name = true` verwendet und beide Modi unterstützt, erstelle beide Einträge.
3. Wandle die Wörterbücher in `locales/<code>/<style>.json` um. Die Werte in den Quelldateien dienen als Suchschlüssel im Wörterbuch. Beispielsweise erzeugt ein PHP-Eintrag `'enable' => 'Enable audit trail'` weiterhin den Anwendungsschlüssel `audit.enable`, wenn er nach `{locale}/audit.php` veröffentlicht wird. Speichere seine Übersetzung unter `"Enable audit trail"` im Wörterbuch.
4. Führe den folgenden Toolkit-Befehl aus einem ausgecheckten Mona-Repository für das ältere Paket aus. Der Befehl erzeugt `default.json` und `inline.json` aus den alten JSON- und PHP-Wörterbüchern und übernimmt Locale-Ausnahmen. Prüfe die Ausgabe und füge sie mit deinem Manifest und den Quelldateien zum neuen Paket hinzu. Benenne Wörterbuchverzeichnisse mit Aliasen in kanonische Codes um.
5. Füge den aktuellen Publisher als Abhängigkeit zum Composer-Manifest des Pakets hinzu. Ergänze auch `laravel-lang/library`, falls das Paket Übersetzungen aus dem gemeinsamen Wörterbuch erwartet. Installiere das Paket in einer Anwendung und führe `php artisan lang:update` aus, um die veröffentlichten Dateien zu prüfen.

```bash
php src/Toolkit/bin/toolkit migrate-locales path/to/legacy-pack --target=path/to/new-pack/locales
```

Prüfe bei bestehenden Anwendungen, welche Regeln der integrierten Pakete nach dem Upgrade greifen.

- Wenn du die zuvor für Spark Paddle, Spark Stripe, Nova Dusk Suite oder Nova Log Viewer veröffentlichten Laravel-Lang-Übersetzungen weiterhin benötigst, behalte die Schlüssel in anwendungseigenen Dateien oder verschiebe alte Quellen und Wörterbücher in ein eigenes Paket.
- Übersetzungen für Starter Kits werden jetzt mit dem Composer-Wert `name` des Projekts abgeglichen. Wenn du das Projekt umbenannt hast oder ein Kit als Abhängigkeit verwendest, füge den Composer-Namen des Kits, etwa `laravel/livewire-starter-kit`, zu `localization_main.force` hinzu, um seine Übersetzungen zu veröffentlichen. [Übersetzungs-Plugins](./plugins.md)

### JSON-Fallback und Übersetzungswerkzeuge aktualisieren {#update-json-fallback-and-translation-tooling}

Wenn Anwendungscode `LaravelLang\JsonFallback\Translator` importiert oder als Typ angibt, ändere den Typ zu `LaravelLang\JsonFallback\OverridedTranslator` oder verwende Laravels Translator-Contract. Wenn du eine Unterklasse des alten Translators erstellt hast, verschiebe das angepasste Verhalten in einen Anwendungstranslator oder in einen eigenen Übersetzungsdienst. Bei einem Schlüssel ohne Treffer ruft der neue Translator Laravels Callback für fehlende Übersetzungen auf. Überprüfe den Callback, wenn deine Anwendung einen registriert, und teste Schlüssel, die im Fallback-JSON und in PHP für die aktuelle Sprache vorkommen. Das Fallback-JSON wird zuerst geprüft. [JSON-Fallback](./translations.md#json-fallback)

Wenn du das separate Paket `laravel-lang/translator` für maschinelle Übersetzungen verwendest, migriere Aufrufe seiner Fassade `Translate` und Integrationen mit Google, DeepL oder Yandex zu deinem gewählten Übersetzungsdienst, bevor du die Abhängigkeit entfernst. Behalte alle von diesen Skripten bereits erzeugten Übersetzungen in den Wörterbüchern der Anwendung oder des Pakets.

Wenn du Paketkataloge mit `vendor/bin/lang` aus `laravel-lang/status-generator` pflegst, führe nach `composer install` die neuen Befehle aus einem ausgecheckten Mona-Repository aus.

| Bisheriges Skript | Entsprechendes Toolkit-Skript |
| --- | --- |
| `vendor/bin/lang status --path=path/to/pack` | `php src/Toolkit/bin/toolkit status path/to/pack`. Lies die erzeugten Berichte `locales/<code>/_status.json`. |
| `vendor/bin/lang sync --path=path/to/pack` | `php src/Toolkit/bin/toolkit sync:package path/to/pack`. Verwende `sync:library` zur Pflege der gemeinsamen Library. |
| `vendor/bin/lang create` | `php src/Toolkit/bin/toolkit create-locale path/to/pack`. Der Befehl erstellt Verzeichnisse für bekannte Locales. |
| `vendor/bin/lang download` | Übergib die ZIP-URL mit `--url`, das vollständige Ziel mit `--directory` und die Pfade im Archiv mit der wiederholbaren Option `--paths`. |
| `vendor/bin/lang translate` | Verschiebe maschinelle Übersetzungen in einen separaten Dienst. Führe Toolkit `status` aus, um gespeicherte Zeichenketten zu finden, die noch ihrem Quelltext entsprechen. |

Vollständige Beispiele findest du unter [Toolkit-Befehle](./toolkit.md).

## Upgrade überprüfen {#verify-the-upgrade}

Führe die Tests deiner Anwendung aus. Prüfe eine veröffentlichte Meldung in jeder wichtigen Locale, eine Fallback-Übersetzung, eine lokalisierte URL mit deinem konfigurierten Alias und eine Modellübersetzung, die unter dem kanonischen Code gespeichert ist. Prüfe die generierten Dateien und gespeicherten Locale-Werte zusammen mit den Änderungen am Quellcode.
