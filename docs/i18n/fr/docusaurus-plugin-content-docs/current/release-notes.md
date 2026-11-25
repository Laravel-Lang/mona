---
title: "Notes de version"
description: "Gestion des versions et compatibilité des plateformes de Laravel-Lang."
---

## Gestion des versions et compatibilité {#versioning-and-compatibility}

Laravel-Lang suit le versionnage sémantique. Les changements incompatibles et la fin de la prise en charge d’une plateforme interviennent dans les versions majeures. Les paquets officiels partagent le même numéro de version. [Versionnage sémantique](https://semver.org/)

La version actuelle nécessite PHP `^8.3` et prend en charge Laravel 12 et 13. Votre version de Laravel peut exiger une version plus récente de PHP. Common dépend également des extensions `intl` et `json` par l’intermédiaire de ses dépendances.

Les tags de version et les notes des correctifs sont disponibles dans les [versions de Mona](https://github.com/Laravel-Lang/mona/releases). Consultez la [politique de prise en charge de Laravel](https://laravel.com/docs/releases#support-policy) pour ses dates de maintenance.

## Disponibilité des paquets {#package-availability}

Le workflow d’export met à jour les branches `main` des dépôts des paquets après les envois sur `main` dans Mona. La publication d’une version de Mona déclenche l’export de son tag vers les dépôts des paquets.

Avant d’installer `20.x` ou de passer à cette version, vérifiez que la version du paquet Composer est disponible sur Packagist. La version Mona et les paquets Composer correspondants peuvent être disponibles à des moments différents. [Exécutions de l’export des paquets](https://github.com/Laravel-Lang/mona/actions/workflows/export-packages.yml)
