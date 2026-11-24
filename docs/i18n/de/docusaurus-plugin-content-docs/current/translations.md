---
title: "Übersetzungen verwalten"
description: "Füge Anwendungs- und Paketübersetzungen mit dem Mona-Publisher hinzu, aktualisiere oder entferne sie."
---

Die offiziellen Übersetzungspakete enthalten den Publisher. Seine Artisan-Befehle schreiben PHP- und JSON-Übersetzungen in das Sprachverzeichnis der Anwendung, üblicherweise `lang/`. Paketspezifische Dateien können auch unter `lang/vendor/` geschrieben werden.

## Locales hinzufügen {#add-locales}

Gib einen oder mehrere [Locale-Codes](./available-locales.md) an und trenne sie durch Leerzeichen. Du kannst konfigurierte Aliasse eingeben. Die generierten Dateien verwenden kanonische Codes.

```bash
php artisan lang:add fr de_CH
```

Der Befehl erstellt die Locale-Verzeichnisse und aktualisiert dann **alle installierten Locales**, einschließlich der aktuellen Locale und der Fallback-Locale der Anwendung. Ohne Argumente fragt er, ob er alle unterstützten Locales installieren soll. Übergib in automatisierten Abläufen die Codes ausdrücklich.

## Übersetzungen aktualisieren {#update-translations}

```bash
php artisan lang:update
```

Der Publisher ermittelt Locales anhand der aktuellen Locale und der Fallback-Locale der Anwendung, der Dateien oder Verzeichnisse direkt im Sprachverzeichnis sowie der JSON-Dateien unter `lang/vendor/<package>/`. Ein PHP-Übersetzungsverzeichnis eines Drittanbieters allein zählt nicht als Installation einer Locale. Verwende `lang:add` für diese Locale, um ihr Anwendungsverzeichnis anzulegen.

Übersetzungspakete geben unterstützte Pakete, Versionen und Zieldateien in [Plugin-Manifesten](./plugins.md) an. Der Publisher kombiniert die gemeinsamen Wörterbücher und die Paketwörterbücher und führt ihre Übersetzungen mit jeder Zieldatei zusammen. Dabei behält er andere Schlüssel bei und ersetzt übereinstimmende Schlüssel, auch numerische. Fehlt ein Text in den Wörterbüchern, schreibt der Publisher den Quelltext. Lege eigene Überschreibungen in separaten Übersetzungsdateien ab oder [schließe](./configure.md#skip-files-and-force-integrations) die von dir selbst gepflegten generierten Ziele aus.

Führe `lang:update` nach Änderungen an Composer-Abhängigkeiten oder [Veröffentlichungseinstellungen](./configure.md) aus. Prüfe die generierten Änderungen.

## Locales entfernen {#remove-locales}

```bash
php artisan lang:rm de_CH
```

Der Befehl löscht das Verzeichnis mit dem kanonischen Locale-Code und die zugehörige JSON-Datei im Stamm des Sprachverzeichnisses und in jedem direkten Paketverzeichnis `lang/vendor/<package>/`. Eigene Dateien in den gelöschten Locale-Verzeichnissen werden dabei ebenfalls entfernt. Die aktuelle Locale und die Fallback-Locale sind geschützt. Ohne Argumente fragt der Befehl, ob er alle ungeschützten Locales entfernen soll, und schlägt „Nein“ als Standard vor. Bei einem verknüpften Locale-Verzeichnis entfernt der Befehl die Verknüpfung selbst.

Der Befehl meldet für jede Locale `DONE`, `PROTECTED`, `INVALID`, `SKIP` oder `FAILED`. Bleibt eine Datei oder ein Verzeichnis nach dem Löschversuch bestehen, gibt der Befehl den Pfad und einen Fehlercode aus. Prüfe den Status jeder Locale, da bei `PROTECTED`, `INVALID` oder `SKIP` der Exit-Code weiterhin Erfolg anzeigen kann.

## JSON-Fallback {#json-fallback}

`laravel-lang/json-fallback` ergänzt bei fehlendem JSON-Schlüssel eine Suche im JSON der Fallback-Locale vor der Suche in PHP-Übersetzungen. Das Paket ist in Common enthalten. Für eine separate Installation führe `composer require laravel-lang/json-fallback` aus.

Füge `LaravelLang\JsonFallback\LangJsonFallbackServiceProvider::class` in `bootstrap/providers.php` zu den Providern der Anwendung hinzu. Falls die Anwendung `Illuminate\Translation\TranslationServiceProvider` ausdrücklich aufführt, ersetze diesen Eintrag durch den Mona-Provider. [Provider-Registrierung in Laravel](https://laravel.com/docs/13.x/providers#registering-providers)

Wenn Französisch die aktuelle Locale und Englisch die Fallback-Locale ist, kann ein fehlender Schlüssel aus `lang/fr.json` jetzt zu seinem Wert in `lang/en.json` aufgelöst werden.
