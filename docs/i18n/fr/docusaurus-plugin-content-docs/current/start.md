---
title: "Prise en main"
description: "Installez Laravel-Lang et publiez vos premières traductions pour Laravel."
---

Laravel-Lang publie des traductions pour Laravel et son écosystème. L’ensemble `laravel-lang/common` permet aussi de [consulter les langues et leurs noms](./locales.md), de [localiser les routes](./routes.md) et de [traduire les modèles](./models.md). Pour installer uniquement le paquet de traductions Laravel et ses dépendances de publication, choisissez `laravel-lang/lang`.

## Démarrage rapide {#quick-start}

Exécutez ces commandes à la racine d’une application Laravel 12 ou 13 avec Composer, PHP `^8.3` et les extensions PHP `intl` et `json`. Votre version de Laravel peut exiger une version plus récente de PHP.

```bash
composer require laravel-lang/common
php artisan lang:add fr
```

Utilisez la fonction de traduction de Laravel dans votre application pour lire un message publié en français.

```php
__('auth.failed', locale: 'fr');
// Ces identifiants ne correspondent pas à nos enregistrements.
```

Pour utiliser le français par défaut, définissez `APP_LOCALE=fr` dans le fichier `.env` de votre application. Définissez `APP_FALLBACK_LOCALE=en` pour utiliser l’anglais lorsqu’une recherche avec repli ne trouve pas de traduction dans la langue courante. Si la configuration a été mise en cache, videz ce cache avec `php artisan config:clear`. Les appels tels que `__('auth.failed')` utilisent alors la langue courante de l’application.

- [Versions précédentes de Laravel-Lang](/legacy)
- [Gérer les traductions](./translations.md) et [consulter les langues disponibles](./available-locales.md)
- [Configurer la localisation](./configure.md)
- [Vérifier la compatibilité](./release-notes.md#versioning-and-compatibility) ou [mettre à niveau une installation existante](./upgrade.md)

## Essayer une version candidate {#try-a-release-candidate}

Utilisez une application Laravel de test distincte pour essayer une version candidate de 20.x déjà publiée. Réglez la stabilité minimale de cette application sur `RC` pour que Composer puisse aussi installer les dépendances Laravel-Lang de la même version. Choisissez une version répertoriée sur [Packagist](https://packagist.org/packages/laravel-lang/common). Remplacez `<release-candidate>` par la version choisie.

```bash
composer config minimum-stability RC
composer config prefer-stable true
composer require "laravel-lang/common:<release-candidate>"
php artisan lang:add fr
```
