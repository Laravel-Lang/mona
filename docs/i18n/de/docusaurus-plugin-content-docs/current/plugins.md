---
title: "Übersetzungs-Plugins"
description: "Installiere Übersetzungspakete oder veröffentliche ein eigenes Composer-Plugin für Laravel-Lang."
---

Der Publisher erkennt mit Composer installierte Übersetzungspakete. Ein Paket stellt ein Manifest `laravel_lang.json`, PHP- oder JSON-Quelldateien und optional eigene Locale-Wörterbücher bereit.

## Ein Paket installieren {#install-a-pack}

Installiere das Paket in deiner Laravel-Anwendung und aktualisiere die bereits verwendeten Locales. Das Paket Actions ergänzt zum Beispiel Beschriftungen für Schaltflächen und andere Aktionen.

```bash
composer require laravel-lang/actions
php artisan lang:update
```

Unter [Übersetzungen verwalten](./translations.md) erfährst du, wie du eine Locale hinzufügst. Pakete von Drittanbietern findest du unter [Community-Plugins](./plugins-community.md).

### Offizielle Übersetzungspakete {#official-translation-packs}

Common enthält die folgenden Pakete. Du kannst jedes davon auch einzeln mit Composer installieren.

| Paket | Übersetzungen |
| --- | --- |
| `laravel-lang/lang` | Laravel-Framework und Pakete wie Breeze, Cashier, Fortify, Jetstream, Nova, Passkeys und Laravel UI |
| `laravel-lang/starter-kits` | Laravel-Starter-Kits für Livewire, React, Svelte und Vue |
| `laravel-lang/moonshine` | Administrationsoberfläche MoonShine |
| `laravel-lang/actions` | Schaltflächen und andere Aktionsbeschriftungen |
| `laravel-lang/attributes` | Verständliche Namen für Validierungsattribute |
| `laravel-lang/http-statuses` | Texte für HTTP-Statuscodes |

Jedes Paket bindet den Publisher und die gemeinsame Library ein. Sein Manifest legt fest, für welche Integrationen und Versionen es Übersetzungen veröffentlichen kann.

## Ein Paket erstellen {#create-a-pack}

Beginne mit der Übersetzungsvorlage. Füge in `composer.json` deines Pakets `laravel-lang/publisher` als Abhängigkeit hinzu und gib die unterstützten Versionen an. Falls dein Paket die gemeinsamen Übersetzungen verwendet, füge auch `laravel-lang/library` hinzu. Der Publisher sucht in der Anwendung unter `vendor/*/*/laravel_lang.json` nach Manifesten. [Übersetzungsvorlage](https://github.com/Laravel-Lang/mona/tree/main/src/TranslationsTemplate)

Lege das Manifest im Stammverzeichnis deines Pakets ab. Dieses Beispiel veröffentlicht eine JSON-Datei und eine PHP-Übersetzungsgruppe für jede installierte Locale.

```json
{
  "$schema": "https://raw.githubusercontent.com/Laravel-Lang/mona/main/resources/laravel-lang.schema.json",
  "plugins": [
    {
      "files": [
        {"source": "audit.json", "target": "{locale}.json"},
        {"source": "audit.php", "target": "{locale}/audit.php"}
      ]
    }
  ]
}
```

Jeder Pfad `source` ist relativ zum Verzeichnis `source/` des Pakets. Jeder Pfad `target` ist relativ zum Laravel-Sprachverzeichnis der Anwendung und enthält `{locale}`.

Verwende zum Beispiel den folgenden Inhalt in `source/audit.json`.

```json
{
  "Enable audit trail": "Enable audit trail"
}
```

Verwende den folgenden Inhalt in `source/audit.php`.

```php
<?php

return [
    'enable' => 'Enable audit trail',
];
```

Bei der Veröffentlichung für Französisch schlagen beide Einträge den englischen Text in `locales/fr/default.json` deines Pakets nach.

```json
{
  "Enable audit trail": "Activer le journal d’audit"
}
```

In der Anwendung liest `__('Enable audit trail')` die JSON-Übersetzung und `__('audit.enable')` die PHP-Übersetzung. Die Paketübersetzungen haben für denselben Text Vorrang vor den Einträgen der gemeinsamen Library. Fehlt der Text in beiden Wörterbüchern, verwendet der Publisher den Quellwert.

Gib dem Paket einen eigenen Composer-Namen und ersetze `self.version` aus der Vorlage durch eine passende Versionsvorgabe. Verwende kanonische Locale-Codes als Verzeichnisnamen für Wörterbücher. Lege Standardübersetzungen in `default.json` und Stilvarianten in Dateien wie `inline.json` ab.

### Übersetzungen abhängig von Bedingungen anwenden {#apply-translations-conditionally}

| Manifesteigenschaft | Wann die Zuordnung gilt |
| --- | --- |
| Keine Bedingung | Der Publisher berücksichtigt sie immer. |
| `vendor` | Das genannte Composer-Paket ist installiert. |
| `vendor` und `version` | Das installierte Paket erfüllt die Composer-Versionsanforderung. |
| `vendor` und `project: true` | Der Composer-Name des Hauptprojekts entspricht `vendor`. In diesem Modus prüft der Publisher den Namen. |

Lege zwei Manifesteinträge an, wenn eine Zuordnung sowohl bei einer installierten Abhängigkeit als auch bei einem gleichnamigen Hauptprojekt gelten soll. Der [Upgrade-Leitfaden](./upgrade.md) beschreibt den Umstieg von einem älteren Provider. Unter [Konfiguration](./configure.md) findest du Regeln zum Ausschließen und Erzwingen von Paketzuordnungen.

### Ein Paket in einer Laravel-Anwendung testen {#test-a-pack-in-a-laravel-application}

Führe die Befehle im Wurzelverzeichnis der Testanwendung aus, wenn das Paket in einem Nachbarverzeichnis liegt. Prüfe `lang/fr.json` und `lang/fr/audit.php`, setze `fr` als Anwendungs-Locale und teste beide Übersetzungsschlüssel. Führe nach Paketänderungen `php artisan lang:update` aus.

```bash
composer config repositories.audit-translations path ../audit-translations
composer require "acme/audit-translations:*@dev"
php artisan lang:add fr
```
