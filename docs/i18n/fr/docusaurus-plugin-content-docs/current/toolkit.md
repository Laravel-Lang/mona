---
title: "Toolkit de traduction"
description: "Entretenez, migrez et importez les sources et dictionnaires de traduction depuis une copie locale de Mona."
---

Toolkit est l’outil en ligne de commande qui sert à entretenir les packs de traduction. Lancez-le depuis une copie locale du dépôt Mona après avoir installé les dépendances Composer à sa racine. Pour publier des traductions dans une application Laravel, utilisez les [commandes Artisan](./translations.md).

```bash
composer install
php src/Toolkit/bin/toolkit list
php src/Toolkit/bin/toolkit migrate-locales --help
```

## Vérifier l’état des traductions {#check-translation-status}

```bash
php src/Toolkit/bin/toolkit status src/Lang
```

Par défaut, la commande écrit `locales/<locale>/_status.json` dans le projet. Elle répertorie par style les entrées dont la traduction enregistrée correspond au texte source. Elle combine les exclusions de `locales/_settings.json` et de `locales/<locale>/_settings.json`, puis supprime les rapports vides. Pour repérer les expressions totalement absentes d’un dictionnaire, comparez-le avec les fichiers sources du pack.

Utilisez `--output-dir` pour écrire les rapports nommés `<locale>.json` dans un répertoire distinct. La commande résout les chemins relatifs à partir du répertoire de travail courant et crée le répertoire de destination lorsqu’elle a un rapport à écrire.

```bash
php src/Toolkit/bin/toolkit status src/Library --output-dir=workbench/status
```

Ajoutez les expressions volontairement inchangées à `excludes` dans le fichier `_settings.json` concerné.

```json
{
  "excludes": ["Laravel"]
}
```

## Gérer les répertoires de langues {#manage-locale-directories}

```bash
php src/Toolkit/bin/toolkit create-locale src/Library
php src/Toolkit/bin/toolkit delete-locale src/Library
```

`create-locale` crée les répertoires manquants pour tous les [codes de langue](./available-locales.md) canoniques. Les nouveaux répertoires sont vides. `delete-locale` supprime chacun des sous-répertoires directs de `locales/` dont le nom ne figure pas dans cette liste. Vérifiez le répertoire cible avant d’exécuter cette commande, surtout s’il contient des alias de langue ou des codes personnalisés.

## Supprimer les entrées de dictionnaire obsolètes {#remove-obsolete-dictionary-entries}

```bash
php src/Toolkit/bin/toolkit sync:library src
php src/Toolkit/bin/toolkit sync:package path/to/translation-pack
```

- `sync:library` collecte les expressions sources des paquets qui possèdent un manifeste `laravel_lang.json` sous `src/`, puis réécrit les dictionnaires de `src/Library/locales/`.
- `sync:package` lit le répertoire `source/` d’un pack et supprime les entrées inutilisées de son répertoire `locales/`. Elle supprime les dictionnaires devenus vides.

Les deux commandes gardent les traductions existantes des expressions encore utilisées ainsi que les fichiers dont le nom commence par un tiret bas, comme `_settings.json`. Ajoutez directement au dictionnaire les traductions des nouvelles expressions.

Sans argument, `sync:library` utilise le répertoire `src/` du dépôt et `sync:package` le répertoire de travail courant. Les deux traitent les répertoires portant un code de langue canonique.

## Migrer les anciens dictionnaires {#migrate-legacy-dictionaries}

```bash
php src/Toolkit/bin/toolkit migrate-locales path/to/legacy-pack --target=path/to/new-pack/locales
```

Indiquez un ancien pack qui contient `source/` et `locales/`. L’option `--target` définit le répertoire qui doit accueillir les sous-répertoires de langues. Par défaut, Toolkit utilise le répertoire `locales/` de l’ancien pack.

- Les entrées de `php.json` et `json.json` sont fusionnées dans `default.json`.
- Les entrées de `php-inline.json` et `json-inline.json` sont fusionnées dans `inline.json`.
- Les entrées de `_excludes.json` sont déplacées sous `excludes` dans le fichier `_settings.json` de la langue cible.

Toolkit transfère directement les entrées JSON, qui utilisent déjà les expressions sources comme clés. Pour les dictionnaires PHP, il recherche les clés imbriquées dans les fichiers PHP de `source/` et utilise les expressions sources correspondantes comme clés. Si une clé existe déjà, la traduction migrée a la priorité. Si les deux anciens formats traduisent la même phrase, le dictionnaire PHP a la priorité.

Toolkit conserve les fichiers de `source/`. Il supprime les anciens fichiers de dictionnaire et d’exclusions (`_excludes.json`) dont il a transféré des entrées, même si la destination est distincte. Consultez le [guide de mise à niveau](./upgrade.md) pour adapter votre application ou le manifeste du plugin.

## Importer des sources depuis une archive ZIP {#import-sources-from-a-zip-archive}

```bash
php src/Toolkit/bin/toolkit download \
  --url=https://github.com/laravel/framework/archive/refs/heads/12.x.zip \
  --directory=workbench/downloads/framework/12.x \
  --paths=src/Illuminate/Translation/lang/en \
  --only-copy
```

- `--directory` définit le chemin de destination complet. Vous pouvez répéter `--paths` pour indiquer plusieurs répertoires relatifs à la racine de l’archive.
- Les fichiers PHP et JSON sélectionnés sont copiés dans la destination en conservant leur nom. Les fichiers PHP sont copiés à l’identique, octet par octet. Les fichiers JSON de même nom sont fusionnés, avec priorité aux valeurs entrantes.
- Sans `--only-copy`, Toolkit recherche les appels de traduction pris en charge dont les clés sont littérales. S’il trouve des expressions, il écrit un fichier source JSON nommé d’après le répertoire parent de la destination, par exemple `framework.json`.
- La commande nécessite l’extension PHP ZIP. Elle prépare les changements avant de les publier et tente de restaurer l’ancienne destination si la publication échoue.

Les options `--url` et `--directory` sont requises. Les répertoires de `--paths` sont parcourus récursivement. Omettez `--paths` pour rechercher uniquement les clés de traduction. Vérifiez les clés avant de les ajouter au paquet.
