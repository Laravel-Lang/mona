---
title: "Erste Schritte"
description: "Installiere Laravel-Lang und veröffentliche deine ersten Laravel-Übersetzungen."
---

Laravel-Lang veröffentlicht Übersetzungen für Laravel und sein Ökosystem. Das Paket `laravel-lang/common` bietet außerdem [Locale- und Namensabfragen](./locales.md), [lokalisierte Routen](./routes.md) und [Modellübersetzungen](./models.md). Für das Laravel-Übersetzungspaket mit seinen Abhängigkeiten zum Veröffentlichen genügt `laravel-lang/lang`.

## Schnellstart {#quick-start}

Führe diese Befehle im Stammverzeichnis einer Laravel-12- oder Laravel-13-Anwendung mit Composer, PHP `^8.3` und den PHP-Erweiterungen `intl` und `json` aus. Deine Laravel-Version kann eine neuere PHP-Version voraussetzen.

```bash
composer require laravel-lang/common
php artisan lang:add fr
```

Verwende die Übersetzungsfunktion von Laravel in deiner Anwendung, um eine veröffentlichte Meldung auf Französisch abzurufen.

```php
__('auth.failed', locale: 'fr');
// Ces identifiants ne correspondent pas à nos enregistrements.
```

Setze `APP_LOCALE=fr` in der Datei `.env` deiner Anwendung, um Französisch als Standardsprache zu verwenden. Setze `APP_FALLBACK_LOCALE=en`, damit eine Abfrage mit Fallback auf Englisch zurückgreift, wenn für das aktuelle Locale eine Übersetzung fehlt. Leere einen vorhandenen Konfigurationscache mit `php artisan config:clear`. Aufrufe wie `__('auth.failed')` verwenden dann das aktuelle Locale der Anwendung.

- [Frühere Laravel-Lang-Versionen](/legacy)
- [Übersetzungen verwalten](./translations.md) und [verfügbare Locales ansehen](./available-locales.md)
- [Lokalisierung konfigurieren](./configure.md)
- [Kompatibilität prüfen](./release-notes.md#versioning-and-compatibility) oder [eine bestehende Installation aktualisieren](./upgrade.md)

## Einen Release Candidate testen {#try-a-release-candidate}

Verwende zum Testen eines veröffentlichten Release Candidate für 20.x eine separate Laravel-Testanwendung. Setze die Mindeststabilität in dieser Anwendung auf `RC`, damit Composer auch die passenden Laravel-Lang-Abhängigkeiten installieren kann. Wähle eine Version auf [Packagist](https://packagist.org/packages/laravel-lang/common). Ersetze `<release-candidate>` durch die gewählte Version.

```bash
composer config minimum-stability RC
composer config prefer-stable true
composer require "laravel-lang/common:<release-candidate>"
php artisan lang:add fr
```
