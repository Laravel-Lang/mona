---
title: "Mitwirken"
description: "Trage aus einem lokalen Mona-Checkout Übersetzungen, PHP-Pakete und Dokumentation bei."
---

Erstelle einen Fork des [Mona-Repositorys](https://github.com/Laravel-Lang/mona) und eröffne einen Pull Request für dessen Branch `main`. Mona enthält die Quellvorlagen, gemeinsamen Übersetzungen, PHP-Pakete und die Dokumentation. Die einzelnen Paket-Repositorys übernehmen ihre Inhalte aus Mona.

## Eine Übersetzung korrigieren oder hinzufügen {#fix-or-add-a-translation}

1. Suche die englische Formulierung im Verzeichnis `src/<Package>/source/` des Pakets. Die Quellen für Laravel Framework und die offiziellen Laravel-Pakete liegen beispielsweise unter `src/Lang/source/`. In der Datei `laravel_lang.json` des Pakets steht, welche Quelldateien zu den jeweiligen Versionen des vorgelagerten Pakets gehören.
2. Öffne `src/Library/locales/<locale>/default.json`. Bearbeite den Wert für den englischen Text oder füge einen fehlenden Eintrag hinzu. Verwende den genauen Quellwert als JSON-Schlüssel, auch bei Texten aus PHP-Dateien.
3. Behalte in der Übersetzung Platzhalter wie `:count`, `:other` und `:attribute` bei. Verwende für eine Inline-Validierungsmeldung denselben Quellschlüssel in `inline.json` und ersetze `:attribute` durch eine Formulierung ohne Feldnamen. Behalte alle anderen Platzhalter und Pluralformen bei.
4. Wenn eine Übersetzung absichtlich dem englischen Text entspricht, ergänze ihn in `_settings.json` der Locale unter `excludes`. Toolkit verwendet diese Liste, um den Übersetzungsstatus zu ermitteln.
5. Erstelle einen Pull Request mit dem Locale-Code im Titel, etwa `[fr]: Updated validation messages`.

Wenn du einen Tippfehler im Quelltext eines vorgelagerten Projekts entdeckst, melde ihn dort, damit der Quelltext und der zugehörige Suchschlüssel gemeinsam aktualisiert werden können.

Der französische Standardeintrag in `src/Library/locales/fr/default.json` sieht beispielsweise so aus.

```json
{
  "The :attribute field is invalid.": "Le champ :attribute est invalide."
}
```

So sieht der dazugehörige Inline-Eintrag in `src/Library/locales/fr/inline.json` aus.

```json
{
  "The :attribute field is invalid.": "Ce champ est invalide."
}
```

Bearbeite für Übersetzungen, die Mona in Anwendungen veröffentlicht, `default.json` und `inline.json` in Library. Einige Pakete enthalten noch die früheren Wörterbuchdateien `php.json` und `json.json`. Die automatische Statusprüfung der Pakete verwendet sie ebenfalls.

Um Länder-, Währungs- oder Locale-Namen zu aktualisieren, bearbeite die JSON-Datei der Locale unter `src/CountryNames/data/`, `src/CurrencyNames/data/` oder `src/LocaleNames/data/`. Siehe [Locales und übersetzte Namen](./locales.md).

## Eine Locale vorschlagen {#propose-a-locale}

Sieh dir die [kanonischen Locale-Codes](./available-locales.md) an. Ist der Code bereits gelistet, füge das zugehörige Wörterbuch in `src/Library/locales/` hinzu oder aktualisiere es. Toolkit kann [fehlende Locale-Verzeichnisse erstellen](./toolkit.md#manage-locale-directories). Um einen neuen Code vorzuschlagen, eröffne eine [Diskussion zu Laravel-Lang](https://github.com/orgs/Laravel-Lang/discussions/new?category=ideas&title=I%20want%20to%20add%20a%20new%20localization).

## Pakete entwickeln {#develop-packages}

Verwende Git, Composer 2 und PHP `^8.3` mit `intl`, `json` und `zip`. Aktiviere `pdo_sqlite` für die Tests, die eine Datenbank im Arbeitsspeicher verwenden. Führe die Befehle im Stammverzeichnis des Repositorys aus.

```bash
composer install
composer check-platform-reqs
```

Das Composer-Projekt im Stammverzeichnis lädt den Code in `src/<Package>/src/` automatisch. Hinterlege die Abhängigkeiten jedes veröffentlichten Pakets in seiner Datei `src/<Package>/composer.json`. Für interne Paketabhängigkeiten gilt `self.version`, damit sich Pakete aus demselben Release gemeinsam installieren lassen.

Verwende die Testbench-Anwendung in `workbench/`, um das Paketverhalten lokal auszuprobieren. `composer serve` baut die Workbench und startet den Entwicklungsserver. Zum Anzeigen der Routen oder Artisan-Befehle kannst du die Testbench-CLI im Stammverzeichnis ausführen.

```bash
composer serve
```

```bash
php vendor/bin/testbench route:list
php vendor/bin/testbench help lang:update
```

Die CI installiert jedes Paket separat. Ein Composer-Repository vom Typ `path` stellt die anderen ausgecheckten Pakete bereit. Wenn du Paketabhängigkeiten änderst, prüfe die Einzelinstallation und die Tests im Stammverzeichnis. Die Einrichtung und die Laravel-Versionsmatrix stehen im [Workflow Test Dependencies](https://github.com/Laravel-Lang/mona/blob/main/.github/workflows/test-dependencies.yml).

## Lokale Prüfungen ausführen {#run-local-checks}

Installiere nach Änderungen an Wörterbüchern die JavaScript-Abhängigkeiten im Stammverzeichnis und führe die Biome-Prüfungen aus. Der Toolkit-Befehl `status` erstellt Berichte über Einträge, deren Wert mit dem Quellschlüssel übereinstimmt. Prüfe die Quelldateien des Pakets auf Texte, für die das Wörterbuch noch keinen Eintrag enthält. Mit `git status --short` kannst du die erzeugten Berichte prüfen. [Toolkit-Befehle](./toolkit.md)

```bash
npm install
npx biome check src/Library/locales/fr/default.json
php src/Toolkit/bin/toolkit status src/Library
```

Führe bei PHP-Änderungen Pest und PHPStan im Stammverzeichnis aus. Starte die Funktionstests wie in der CI nacheinander, weil die Tests zur Modellerzeugung das Verzeichnis der Testbench-Anwendung gemeinsam nutzen. Architekturtests können parallel laufen. Für einen einzelnen Funktionsbereich übergib Pest die entsprechende Testdatei oder das Testverzeichnis.

```bash
php vendor/bin/pest --exclude-group=arch
php vendor/bin/pest --group=arch --parallel
composer test:types
```

Wenn die Routentests wegen der Testbench-Entwicklungseinstellungen URLs mit `http://localhost:8000` erzeugen, setze `APP_URL` vor dem Aufruf von Pest im Testprozess auf `http://localhost`.

`composer style` normalisiert die Composer-Dateien und führt die Korrekturen von Rector und Pint aus. Prüfe die Änderungen vor dem Einreichen des Pull Requests. Aktiviere zur Messung der Zeilenabdeckung Xdebug und führe `composer run-script --timeout=0 test:coverage` aus.

Führe nach Manifeständerungen dieselbe Schemaprüfung wie in der CI aus.

```bash
npx --yes ajv-cli@5.0.0 validate --spec=draft2020 -s resources/laravel-lang.schema.json -d 'src/*/laravel_lang.json'
```

Das Manifestformat findest du unter [Übersetzungs-Plugins](./plugins.md).

## Dokumentation bearbeiten {#edit-the-documentation}

Die englischen Seiten zum aktuellen Release liegen in `docs/docs/`. Die Übersetzungen liegen in `docs/i18n/<language>/docusaurus-plugin-content-docs/current/`. Aktualisiere bei geänderten Anleitungen die zugehörigen Seiten und verwende dieselben Überschriften-IDs für Links in allen Sprachen. Die archivierte Version vor Mona liegt in `docs/versioned_docs/version-legacy/`. Ihre Übersetzungen liegen in den jeweiligen `version-legacy/`-Verzeichnissen unter `docs/i18n/`.

Verwende die in `docs/package.json` geforderte Node.js-Version. Führe die Dokumentationsbefehle in `docs/` aus.

```bash
cd docs
npm ci
npm start
```

Mit `npm start -- --locale fr` kannst du eine einzelne Übersetzung in der Vorschau ansehen. Beende den Entwicklungsserver und erstelle den Produktionsbuild, um Links, Anker und Suchindizes für alle konfigurierten Sprachen zu prüfen.

```bash
npm run typecheck
npm run build
npm run serve
```

Öffne die von `serve` angezeigte Adresse, um die Suche und die erzeugten Markdown-Exporte zu prüfen. Die statische Website liegt nach dem Build in `docs/build/`.

## Ein Release veröffentlichen {#publish-a-release}

Ein Merge in `main` exportiert jeden Paket-Unterbaum in den Branch `main` seines Repositorys und aktualisiert den Release-Entwurf von Mona. Prüfe den Entwurf und veröffentliche das Mona-Release mit dem gemeinsamen Versions-Tag. Die Release-Workflows reagieren auf die Veröffentlichung des Releases.

Die Veröffentlichung exportiert den Tag und erstellt das zugehörige Release in jedem Paket-Repository. Sie startet auch den Build und das Deployment der Dokumentationswebsite. Prüfe beide Workflows danach. Um die Aufteilung der Pakete vorab zu prüfen, starte **Export Packages** manuell aus `main` mit aktiviertem `dry-run`. [Export Packages](https://github.com/Laravel-Lang/mona/actions/workflows/export-packages.yml) · [Documentation](https://github.com/Laravel-Lang/mona/actions/workflows/docs.yml)
