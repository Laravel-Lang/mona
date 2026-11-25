---
title: "Konfiguration"
description: "Konfiguriere Übersetzungsstile, Locale-Aliasse, Veröffentlichungsfilter, Satzzeichen, Routen und Modelle in Mona."
---

Mona lädt die Standardeinstellungen automatisch. Veröffentliche die Dateien, die du anpassen möchtest, im Verzeichnis `config/` deiner Anwendung.

```bash
php artisan vendor:publish --tag=localization_main
```

Der Tag `localization` veröffentlicht alle fünf Dateien. Jeder Dateiname ohne `.php` ist zugleich ein Veröffentlichungs-Tag und ein Laravel-Konfigurationsschlüssel.

| Datei | Einstellungen |
| --- | --- |
| `localization_main.php` | Übersetzungsstile, Aliasse und Veröffentlichungsfilter |
| `localization_punctuation.php` | Intelligente Zeichensetzung und sprachspezifische Anführungszeichen |
| `localization_routes.php` | [Routennamen, Locale-Quellen und Middleware](./routes.md) |
| `localization_models.php` | [Suffix für Übersetzungsmodelle, Filter und Generatorpfade](./models.md) |
| `localization_map.php` | Locale-Metadaten wie Schrift, Region und Schreibrichtung |

Laravel überschreibt Einstellungen der obersten Ebene mit den Werten der Anwendung. Wenn du das Array `locales` in `localization_punctuation.php` änderst, behalte die Standardwerte für Locales bei, die du weiterhin nutzen willst.

Leere nach Konfigurationsänderungen einen vorhandenen Cache mit `php artisan config:clear`. Führe `php artisan lang:update` aus, um die von den Veröffentlichungseinstellungen betroffenen Übersetzungen neu zu generieren.

## Übersetzungsstile {#translation-styles}

Ein Übersetzungsstil ist ein benanntes Wörterbuch, das die Formulierung ausgewählter Textbausteine ändert. Ein Paket kann zum Beispiel `informal.json` für eine ungezwungene Ansprache oder `john.json` für die Wortwahl eines Produkts bereitstellen. Stilnamen sind unabhängig von Locale-Codes, Anwendungsnamen und Paketnamen. Jedes Übersetzungspaket kann für seine Sprachen einen Stil unter `locales/<locale>/<style>.json` bereitstellen. Ein Stil kann aus einem einzigen Textbaustein bestehen. Der ursprüngliche Anwendungsfall war die informelle Ansprache. [Diskussion zu informellen Übersetzungen](https://github.com/orgs/Laravel-Lang/discussions/124)

Wenn du ein Plugin installiert hast, das `john` bereitstellt, setze den Stil in `config/localization_main.php` ans Ende der Liste, damit er innerhalb jeder Wörterbuchquelle Vorrang hat.

```php
'styles' => ['default', 'informal', 'john'],
```

Wenn du `php artisan lang:update` ausführst, führt der Publisher die Stildateien für jede installierte Locale zusammen. Für jeden Textbaustein gilt zuerst `john`, sofern der Stil den Textbaustein enthält, dann `informal` und schließlich `default`. Ein französisches Paket kann beispielsweise einen Textbaustein in `locales/fr/john.json` überschreiben, während Deutsch für denselben Textbaustein `locales/de/informal.json` oder `locales/de/default.json` verwendet. Der Publisher wendet diese Reihenfolge getrennt auf die gemeinsame Library und das Paket an, aus dem die Datei stammt. Für diese Datei hat ein passender Textbaustein aus dem Paket Vorrang vor dem Eintrag in der Library.

Die Standardeinstellung lautet `['default', 'inline']`. Der Stil `inline` enthält allgemeine Validierungsmeldungen wie „Dieses Feld muss akzeptiert werden.“ Setze `styles` auf `['default']`, um ausschließlich die Standardformulierungen zu verwenden. Wenn `default` in der Liste fehlt, setzt der Publisher den Eintrag an den Anfang.

## Locale-Aliasse {#locale-aliases}

Weise in `localization_main.aliases` jedem kanonischen Code den gewünschten Alias zu. Ergänze beispielsweise den folgenden Eintrag im Array, das `config/localization_main.php` zurückgibt.

```php
'aliases' => [
    'de_CH' => 'de-CH',
],
```

Artisan kann `de-CH` akzeptieren. Laravel und die veröffentlichten Dateien verwenden `de_CH`. Setze `APP_LOCALE` und `APP_FALLBACK_LOCALE` auf kanonische Codes. Beispiele zur Umwandlung und Abfrage findest du unter [Locales und Namen](./locales.md).

Bei Aliasen wird Groß- und Kleinschreibung unterschieden. Verwende für jede Locale einen eindeutigen Alias, der sich von den kanonischen Codes anderer Locales unterscheidet.

## Dateien ausschließen und Integrationen erzwingen {#skip-files-and-force-integrations}

- `localization_main.skip` gleicht vollständige Pfade mit `/` als Trennzeichen mit `*`-Mustern ab und ignoriert dabei die Groß- und Kleinschreibung. So kannst du Quelldateien, Wörterbücher und Veröffentlichungsziele ausschließen. Zum Beispiel verhindert `['*/lang/fr/validation.php']`, dass der Publisher diese Zieldatei in einer Anwendung mit `lang/` schreibt.
- `localization_main.force` akzeptiert Composer-Paketnamen. Damit kann der Publisher Manifesteinträge auch dann verwenden, wenn die genannte Abhängigkeit fehlt, die installierte Version abweicht oder das Hauptprojekt einen anderen Namen hat. Verwende die Einstellung, wenn die Übersetzungen aus dem Manifest für deine Anwendung gelten.

`skip` hat Vorrang vor `force`.

## Intelligente Zeichensetzung {#smart-punctuation}

Setze `LOCALIZATION_PUNCTUATION_ENABLED=true`, um typografische Anführungszeichen, Gedankenstriche und Auslassungspunkte in veröffentlichten Übersetzungen zu aktivieren. Die allgemeinen Regeln bearbeitest du in `localization_punctuation.common`, die Ausnahmen nach kanonischem Locale-Code in `localization_punctuation.locales`. Der Publisher übernimmt die Regeln, wenn er die Übersetzungen das nächste Mal schreibt.

## Locale-Metadaten {#locale-metadata}

`localization_map.locales` ordnet kanonischen Codes die Schrift (`type`), die regionale Locale (`regional`) und die optionale Schreibrichtung (`direction`) zu. Verwende `Direction::LeftToRight`, wenn keine Richtung eingestellt ist.

```php
use LaravelLang\LocaleList\Direction;

$metadata = config('localization_map.locales.ar', []);
$script = $metadata['type'];         // 'Arab'
$regional = $metadata['regional'];  // 'ar_AE'
$dir = ($metadata['direction'] ?? Direction::LeftToRight)->value; // 'rtl'
```
