---
title: "Übersetzungs-Toolkit"
description: "Pflege, migriere und importiere Übersetzungsquellen und Wörterbücher mit einer lokalen Kopie des Mona-Repositorys."
---

Toolkit ist das Kommandozeilenwerkzeug zur Pflege von Übersetzungspaketen. Installiere die Composer-Abhängigkeiten des Mona-Repositorys und führe das Werkzeug dort aus. Um Übersetzungen in einer Laravel-Anwendung zu veröffentlichen, verwende die [Artisan-Befehle](./translations.md).

```bash
composer install
php src/Toolkit/bin/toolkit list
php src/Toolkit/bin/toolkit migrate-locales --help
```

## Übersetzungsstatus prüfen {#check-translation-status}

```bash
php src/Toolkit/bin/toolkit status src/Library
```

Standardmäßig schreibt der Befehl `locales/<locale>/_status.json` in das Projekt. Der Bericht führt nach Stil gruppiert die Einträge auf, deren gespeicherte Übersetzung dem Quelltext entspricht. Der Befehl fasst die Ausnahmen aus `locales/_settings.json` und `locales/<locale>/_settings.json` zusammen und löscht leere Berichte. Um Texte zu finden, die in einem Wörterbuch ganz fehlen, vergleiche es mit den Quelldateien des Pakets.

Mit `--output-dir` schreibst du die Berichte als `<locale>.json` in ein separates Verzeichnis. Der Befehl löst relative Pfade ausgehend vom aktuellen Arbeitsverzeichnis auf und legt das Zielverzeichnis an, sobald er einen Bericht schreibt.

```bash
php src/Toolkit/bin/toolkit status src/Library --output-dir=workbench/status
```

## Locale-Verzeichnisse verwalten {#manage-locale-directories}

```bash
php src/Toolkit/bin/toolkit create-locale src/Library
php src/Toolkit/bin/toolkit delete-locale src/Library
```

`create-locale` erstellt fehlende Verzeichnisse für alle kanonischen [Locale-Codes](./available-locales.md). Neue Verzeichnisse sind zunächst leer. `delete-locale` löscht jedes direkt unter `locales/` liegende Verzeichnis, dessen Name außerhalb dieser Liste liegt. Prüfe das Ziel vor der Ausführung, insbesondere wenn es Locale-Aliasse oder benutzerdefinierte Codes enthält.

## Veraltete Wörterbucheinträge entfernen {#remove-obsolete-dictionary-entries}

```bash
php src/Toolkit/bin/toolkit sync:library src
php src/Toolkit/bin/toolkit sync:package path/to/translation-pack
```

- `sync:library` sammelt Quelltexte aus Paketen mit einem Manifest `laravel_lang.json` unter `src/` und schreibt die Wörterbücher in `src/Library/locales/` neu.
- `sync:package` liest das Verzeichnis `source/` eines Pakets und entfernt ungenutzte Einträge in dessen Verzeichnis `locales/`. Wörterbücher, die dadurch leer werden, löscht der Befehl.

Beide Befehle behalten bestehende Übersetzungen für aktive Texte und Dateien mit vorangestelltem Unterstrich wie `_settings.json` bei. Ergänze Übersetzungen für neue Texte direkt im Wörterbuch.

## Ältere Wörterbücher migrieren {#migrate-legacy-dictionaries}

```bash
php src/Toolkit/bin/toolkit migrate-locales path/to/legacy-pack --target=path/to/new-pack/locales
```

Übergib ein älteres Paket mit `source/` und `locales/`. Das Ziel ist das Verzeichnis für die Locale-Unterverzeichnisse. Übergib `--target` in Skripten. Bei interaktiver Ausführung fragt das Toolkit danach, wenn die Angabe fehlt.

- Aus `php.json` und `json.json` wird `default.json`. Die ursprünglichen Quelltexte werden zu den neuen Schlüsseln.
- Aus `php-inline.json` und `json-inline.json` wird bei Quelltexten mit `:attribute` die Datei `inline.json`.
- Einträge aus `_excludes.json` werden im Zielverzeichnis der Locale in `_settings.json` unter `excludes` übernommen.

Toolkit behält die älteren Dateien bei und führt ihre Einträge mit den Zielwörterbüchern zusammen. Bei gleichen Schlüsseln gewinnt die migrierte Übersetzung. Liefern beide älteren Formate für denselben Text eine Übersetzung, hat das PHP-Wörterbuch Vorrang. Verwende ein separates Ziel, um die Migration zu prüfen, bevor du ein aktives Wörterbuch ersetzt. Änderungen an der Anwendung oder am Plugin-Manifest beschreibt der [Upgrade-Leitfaden](./upgrade.md).

## Quellen aus einem ZIP-Archiv importieren {#import-sources-from-a-zip-archive}

```bash
php src/Toolkit/bin/toolkit download \
  --url=https://github.com/laravel/framework/archive/refs/heads/12.x.zip \
  --directory=workbench/downloads/framework/12.x \
  --paths=src/Illuminate/Translation/lang/en \
  --only-copy
```

- `--directory` legt das endgültige Ziel fest. Du kannst `--paths` mehrfach für verschiedene Verzeichnisse relativ zum Stamm des Archivs angeben.
- Ausgewählte PHP- und JSON-Dateien werden unter ihrem Dateinamen ins Ziel kopiert. PHP-Dateien werden bytegenau kopiert. JSON-Dateien mit demselben Namen werden zusammengeführt, wobei eingehende Werte Vorrang haben.
- Ohne `--only-copy` sucht Toolkit nach unterstützten Übersetzungsaufrufen mit literalen Schlüsseln. Wenn es Texte findet, schreibt es eine JSON-Quelldatei. Ihr Name entspricht dem Namen des übergeordneten Zielverzeichnisses, etwa `framework.json` im Beispiel.
- Der Befehl benötigt die PHP-Erweiterung ZIP. Er bereitet Änderungen vor der Veröffentlichung vor und versucht, das vorherige Ziel wiederherzustellen, falls die Veröffentlichung fehlschlägt.
