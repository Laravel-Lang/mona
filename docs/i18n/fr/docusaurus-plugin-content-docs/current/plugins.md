---
title: "Plugins de traduction"
description: "Installez des packs de traduction ou publiez votre propre plugin Composer pour Laravel-Lang."
---

Le Publisher détecte les packs de traduction installés avec Composer. Un pack fournit un manifeste `laravel_lang.json`, des fichiers sources PHP ou JSON et, éventuellement, ses propres dictionnaires de langue.

## Installer un pack {#install-a-pack}

Installez le pack dans votre application Laravel et mettez à jour les langues que vous utilisez déjà. Par exemple, le pack Actions ajoute des libellés pour les boutons et autres commandes.

```bash
composer require laravel-lang/actions
php artisan lang:update
```

Consultez [Gérer les traductions](./translations.md) pour ajouter une langue et [Plugins de la communauté](./plugins-community.md) pour trouver les packs tiers.

### Packs de traduction officiels {#official-translation-packs}

Common inclut ces packs. Vous pouvez aussi les installer séparément avec Composer.

| Paquet | Traductions |
| --- | --- |
| `laravel-lang/lang` | Framework Laravel et paquets tels que Breeze, Cashier, Fortify, Jetstream, Nova, Passkeys et Laravel UI |
| `laravel-lang/starter-kits` | Kits de démarrage Laravel pour Livewire, React, Svelte et Vue |
| `laravel-lang/moonshine` | Panneau d’administration MoonShine |
| `laravel-lang/actions` | Libellés de boutons et d’autres actions |
| `laravel-lang/attributes` | Noms d’attributs lisibles pour la validation |
| `laravel-lang/http-statuses` | Libellés des codes de statut HTTP |

Chaque pack installe le Publisher et la bibliothèque partagée Library comme dépendances. Son manifeste précise pour quelles intégrations et versions il peut publier des traductions.

## Créer un pack {#create-a-pack}

Partez du modèle de traduction. Dans le fichier `composer.json` de votre pack, ajoutez `laravel-lang/publisher` aux dépendances et déclarez les versions prises en charge. Ajoutez aussi `laravel-lang/library` si votre pack utilise les traductions partagées. Le Publisher recherche les manifestes dans l’application, sous `vendor/*/*/laravel_lang.json`. [Modèle de pack de traduction](https://github.com/Laravel-Lang/mona/tree/main/src/TranslationsTemplate)

Placez le manifeste à la racine de votre pack. Cet exemple publie un fichier JSON et un groupe de traductions PHP pour chaque langue installée.

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

Chaque chemin `source` est relatif au répertoire `source/` du pack. Chaque chemin `target` est relatif au répertoire de langues Laravel de l’application et contient le paramètre `{locale}`.

Voici un exemple pour `source/audit.json`.

```json
{
  "Enable audit trail": "Enable audit trail"
}
```

Voici l’équivalent dans `source/audit.php`.

```php
<?php

return [
    'enable' => 'Enable audit trail',
];
```

Lors de la publication du français, les deux fichiers recherchent la phrase anglaise dans `locales/fr/default.json` de votre pack.

```json
{
  "Enable audit trail": "Activer le journal d’audit"
}
```

Dans l’application, `__('Enable audit trail')` lit la traduction JSON et `__('audit.enable')` lit la traduction PHP. Les traductions du pack sont prioritaires sur les entrées de Library pour une même phrase. Si la phrase est absente des deux dictionnaires, le Publisher utilise la valeur source.

Donnez un nom Composer propre au paquet et remplacez `self.version` dans le modèle par une contrainte adaptée. Utilisez les codes de langue canoniques pour les répertoires des dictionnaires. Placez les traductions standard dans `default.json` et les variantes dans des fichiers comme `inline.json`.

### Appliquer des traductions sous conditions {#apply-translations-conditionally}

| Propriété du manifeste | Quand la correspondance s’applique |
| --- | --- |
| Aucune condition | Le Publisher l’inclut toujours. |
| `vendor` | Le paquet Composer indiqué est installé. |
| `vendor` et `version` | Le paquet installé satisfait la contrainte de version Composer. |
| `vendor` et `project: true` | Le nom Composer du projet racine correspond à `vendor`. Dans ce mode, le Publisher vérifie le nom. |

Utilisez deux entrées de manifeste si une correspondance doit s’appliquer à la fois à une dépendance installée et à un projet racine du même nom. Consultez le [guide de mise à niveau](./upgrade.md) pour migrer un ancien fournisseur de services et la [configuration](./configure.md) pour les exclusions et la sélection forcée de paquets.

### Tester un paquet dans une application Laravel {#test-a-pack-in-a-laravel-application}

Si le paquet se trouve dans un répertoire voisin, lancez ces commandes à la racine de l’application de test. Vérifiez `lang/fr.json` et `lang/fr/audit.php`, passez l’application en `fr` et testez les deux clés. Après avoir modifié le paquet, lancez `php artisan lang:update`.

```bash
composer config repositories.audit-translations path ../audit-translations
composer require "acme/audit-translations:*@dev"
php artisan lang:add fr
```
