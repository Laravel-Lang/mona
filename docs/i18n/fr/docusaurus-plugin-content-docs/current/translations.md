---
title: "Gérer les traductions"
description: "Ajoutez, mettez à jour et supprimez les traductions de l’application et de ses dépendances avec le Publisher de Mona."
---

Le Publisher est inclus dans les packs de traduction officiels. Ses commandes Artisan écrivent les traductions PHP et JSON dans le répertoire de langues de l’application, généralement `lang/`. Les fichiers propres à un paquet peuvent aussi être écrits dans `lang/vendor/`.

Exécutez les commandes Artisan à la racine de l’application. `lang_path()` indique le répertoire des traductions.

## Ajouter des langues {#add-locales}

Indiquez un ou plusieurs [codes de langue](./available-locales.md), séparés par des espaces. La commande accepte les alias configurés et utilise les codes canoniques pour les fichiers générés.

```bash
php artisan lang:add fr de_CH
```

La commande crée les répertoires de langues, puis met à jour **toutes les langues installées**, y compris la langue active et la langue de repli de l’application. Sans argument, elle propose d’installer toutes les langues prises en charge. Indiquez les codes explicitement dans les scripts d’automatisation.

Si un code est inconnu, la commande échoue avant de créer des répertoires.

## Mettre à jour les traductions {#update-translations}

```bash
php artisan lang:update
```

`lang:update` actualise toutes les langues installées et n’accepte aucun code de langue en argument.

Le Publisher détecte les langues à partir de la langue active et de la langue de repli de l’application, des fichiers ou répertoires à la racine du répertoire de langues et des fichiers JSON dans `lang/vendor/<package>/`. Un répertoire contenant uniquement des traductions PHP d’un paquet ne suffit pas pour considérer une langue comme installée. Utilisez `lang:add` pour créer le répertoire de cette langue dans l’application.

Les packs de traduction déclarent les paquets pris en charge, leurs versions et les fichiers de destination dans les [manifestes des plugins](./plugins.md). Le Publisher combine les dictionnaires partagés avec ceux des paquets, puis fusionne les traductions dans chaque fichier de destination. Il conserve les clés sans correspondance et remplace les clés correspondantes, y compris les clés numériques. Si une expression est absente des dictionnaires, le Publisher écrit le texte source. Conservez vos traductions personnalisées dans des fichiers distincts ou [excluez](./configure.md#skip-files-and-force-integrations) les fichiers générés que vous gérez vous-même.

Exécutez `lang:update` après avoir modifié les dépendances Composer ou les [paramètres de publication](./configure.md). Examinez les différences générées.

Sur une copie fraîche du projet, exécutez `composer install` avant de publier les traductions. Déployez les fichiers générés avec l’application ou générez-les au déploiement si le répertoire des traductions est accessible en écriture. En l’absence de fichiers, exécutez `php artisan lang:add fr de_CH --no-interaction` avec les codes nécessaires. Si les fichiers sont déjà présents, actualisez-les avec `php artisan lang:update --no-interaction`.

## Supprimer des langues {#remove-locales}

```bash
php artisan lang:rm de_CH
```

Vous pouvez passer les codes canoniques ou les alias configurés.

La commande supprime le répertoire et le fichier JSON du code de langue canonique à la racine du répertoire de langues et dans chaque sous-répertoire direct `lang/vendor/<package>/`, y compris les fichiers personnalisés qu’ils contiennent. La langue active et la langue de repli sont protégées. Sans argument, la commande propose de supprimer toutes les langues non protégées et sélectionne « non » par défaut. Si le répertoire d’une langue est un lien, la commande supprime le lien lui-même.

La commande indique `DONE`, `PROTECTED`, `INVALID`, `SKIP` ou `FAILED` pour chaque langue. Si un fichier ou un répertoire subsiste après la tentative de suppression, la commande affiche son chemin et renvoie un code d’échec. Vérifiez le statut de chaque langue, car `PROTECTED`, `INVALID` et `SKIP` peuvent accompagner un code de sortie indiquant un succès.

## Trouver les commandes Artisan {#find-the-artisan-commands}

Exécutez `php artisan help lang:add` pour vérifier que la commande est enregistrée. Si elle manque, vérifiez l’installation avec `composer show laravel-lang/publisher`, puis exécutez `php artisan package:discover`. Si vous désactivez la découverte automatique de Config et de Publisher, enregistrez `LaravelLang\Config\LangConfigServiceProvider::class` et `LaravelLang\Publisher\LangPublisherServiceProvider::class` dans `bootstrap/providers.php`.

## Repli des traductions JSON {#json-fallback}

Lorsqu’une clé JSON est introuvable, `laravel-lang/json-fallback` la recherche dans le JSON de la langue de repli avant de passer aux traductions PHP. Common inclut ce paquet. Pour l’installer séparément, exécutez `composer require laravel-lang/json-fallback`.

Ajoutez `LaravelLang\JsonFallback\LangJsonFallbackServiceProvider::class` aux fournisseurs de services de l’application dans `bootstrap/providers.php`. Si l’application déclare explicitement `Illuminate\Translation\TranslationServiceProvider`, remplacez cette entrée par le fournisseur de services de Mona. [Enregistrement des fournisseurs de services Laravel](https://laravel.com/docs/13.x/providers#registering-providers)

Si le français est la langue active et l’anglais la langue de repli, une clé absente de `lang/fr.json` peut désormais correspondre à sa valeur dans `lang/en.json`.

Pour limiter la recherche à la langue demandée, appelez `Lang::get($key, fallback: false)` via la façade `Illuminate\Support\Facades\Lang`.
