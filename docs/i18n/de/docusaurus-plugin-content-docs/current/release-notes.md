---
title: "Versionshinweise"
description: "Versionierung und Plattformkompatibilität von Laravel-Lang."
---

## Versionierung und Kompatibilität {#versioning-and-compatibility}

Laravel-Lang folgt der semantischen Versionierung. Inkompatible Änderungen und das Einstellen der Unterstützung für eine Plattform erfolgen in Hauptversionen. Die offiziellen Pakete verwenden dieselbe Versionsnummer. [Semantische Versionierung](https://semver.org/)

Die aktuelle Version benötigt PHP `^8.3` und unterstützt Laravel 12 und 13. Deine Laravel-Version erfordert unter Umständen eine neuere PHP-Version. Common benötigt über seine Abhängigkeiten außerdem die Erweiterungen `intl` und `json`.

Versions-Tags und Hinweise zu Patch-Versionen findest du unter [Mona-Releases](https://github.com/Laravel-Lang/mona/releases). Die Wartungszeiträume für Laravel stehen in der [Laravel-Support-Richtlinie](https://laravel.com/docs/releases#support-policy).

## Verfügbarkeit der Pakete {#package-availability}

Der Export-Workflow aktualisiert `main` in den Paket-Repositories nach Pushes auf `main` in Mona. Bei der Veröffentlichung eines Mona-Release exportiert er dessen Tag in die Paket-Repositories.

Prüfe vor der Installation von `20.x` oder dem Upgrade, ob die benötigte Composer-Paketversion auf Packagist verfügbar ist. Das Mona-Release und die passenden Composer-Pakete können zu unterschiedlichen Zeiten erscheinen. [Läufe des Paketexports](https://github.com/Laravel-Lang/mona/actions/workflows/export-packages.yml)
