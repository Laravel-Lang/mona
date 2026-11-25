---
title: "Contribuer"
description: "Contribuez aux traductions, aux paquets PHP et à la documentation depuis une copie locale de Mona."
---

Créez un fork du [dépôt Mona](https://github.com/Laravel-Lang/mona) et ouvrez une pull request vers sa branche `main`. Mona rassemble les modèles de fichiers sources, les traductions partagées, les paquets PHP et la documentation. Les dépôts des paquets reçoivent leurs contenus depuis Mona.

## Corriger ou ajouter une traduction {#fix-or-add-a-translation}

1. Repérez l’expression anglaise dans le répertoire `src/<Package>/source/` du pack. Par exemple, les sources du framework Laravel et des paquets officiels Laravel se trouvent sous `src/Lang/source/`. Le fichier `laravel_lang.json` du pack répertorie les fichiers sources utilisés pour chaque version du paquet amont.
2. Ouvrez `src/Library/locales/<locale>/default.json`. Modifiez la valeur correspondant à l’expression anglaise ou ajoutez l’entrée si elle manque. Utilisez la valeur source exacte comme clé JSON, y compris pour les expressions tirées d’un fichier PHP.
3. Conservez les paramètres tels que `:count`, `:other` et `:attribute` dans la traduction. Pour un message de validation en ligne, utilisez la même clé source dans `inline.json` et remplacez `:attribute` par une formulation qui évite de nommer le champ. Conservez les autres paramètres et les formes du pluriel.
4. Si une traduction reprend volontairement l’expression anglaise, ajoutez l’expression sous `excludes` dans le fichier `_settings.json` de la langue. Toolkit utilise cette liste pour produire l’état des traductions.
5. Ouvrez une pull request dont le titre contient le code de langue, par exemple `[fr]: Updated validation messages`.

Si vous trouvez une faute dans une expression source qui vient d’un projet amont, signalez-la dans ce projet pour que l’expression et sa clé de recherche soient mises à jour ensemble.

Par exemple, l’entrée française par défaut dans `src/Library/locales/fr/default.json` est la suivante.

```json
{
  "The :attribute field is invalid.": "Le champ :attribute est invalide."
}
```

L’entrée correspondante dans `src/Library/locales/fr/inline.json` est la suivante.

```json
{
  "The :attribute field is invalid.": "Ce champ est invalide."
}
```

Pour les traductions publiées dans les applications par Mona, modifiez `default.json` et `inline.json` dans Library. Certains packs contiennent encore les anciens dictionnaires `php.json` et `json.json`, que la vérification automatique de l’état des traductions des packs utilise également.

Pour mettre à jour les noms de pays, de devises ou de langues, modifiez le fichier JSON de la langue sous `src/CountryNames/data/`, `src/CurrencyNames/data/` ou `src/LocaleNames/data/`. Consultez [Langues et libellés traduits](./locales.md).

## Proposer une langue {#propose-a-locale}

Consultez les [codes de langue canoniques](./available-locales.md). Si le code figure déjà dans la liste, ajoutez ou mettez à jour son dictionnaire sous `src/Library/locales/`. Toolkit peut [créer les répertoires manquants](./toolkit.md#manage-locale-directories). Pour proposer un nouveau code, ouvrez une [discussion Laravel-Lang](https://github.com/orgs/Laravel-Lang/discussions/new?category=ideas&title=I%20want%20to%20add%20a%20new%20localization).

## Développer les paquets {#develop-packages}

Utilisez Git, Composer 2 et PHP `^8.3` avec `intl`, `json` et `zip`. Activez `pdo_sqlite` pour les tests, qui utilisent une base de données en mémoire. Exécutez les commandes depuis la racine du dépôt.

```bash
composer install
composer check-platform-reqs
```

Le projet Composer à la racine charge automatiquement le code sous `src/<Package>/src/`. Déclarez les dépendances de chaque paquet publié dans son propre fichier `src/<Package>/composer.json`. Les dépendances internes utilisent `self.version`, ce qui permet d’installer ensemble les paquets d’une même version.

Utilisez l’application Testbench dans `workbench/` pour essayer les paquets en local. `composer serve` prépare Workbench et démarre le serveur de développement. Pour consulter ses routes ou ses commandes Artisan, utilisez l’interface en ligne de commande de Testbench depuis la racine.

```bash
composer serve
```

```bash
php vendor/bin/testbench route:list
php vendor/bin/testbench help lang:update
```

L’intégration continue installe chaque paquet séparément. Un dépôt Composer de type `path` donne accès aux autres paquets de la copie locale. Si vous modifiez des dépendances, vérifiez l’installation du paquet seul et exécutez les tests à la racine. La configuration et la matrice des versions de Laravel figurent dans le [workflow Test Dependencies](https://github.com/Laravel-Lang/mona/blob/main/.github/workflows/test-dependencies.yml).

## Effectuer les vérifications locales {#run-local-checks}

Pour les changements de dictionnaire, installez les dépendances JavaScript à la racine et lancez les vérifications de Biome. La commande `status` de Toolkit produit des rapports sur les entrées dont la valeur correspond à la clé source. Consultez les fichiers sources du pack pour les expressions encore absentes du dictionnaire. Utilisez `git status --short` pour contrôler les rapports produits. [Commandes Toolkit](./toolkit.md)

```bash
npm install
npx biome check src/Library/locales/fr/default.json
php src/Toolkit/bin/toolkit status src/Library
```

Pour les changements PHP, exécutez Pest et PHPStan depuis la racine. Exécutez les tests fonctionnels en série, comme dans l’intégration continue, car les tests de génération de modèles partagent le répertoire de l’application Testbench. Les tests d’architecture peuvent s’exécuter en parallèle. Pour cibler une fonctionnalité, passez son fichier ou son répertoire de tests à Pest.

```bash
php vendor/bin/pest --exclude-group=arch
php vendor/bin/pest --group=arch --parallel
composer test:types
```

Si les tests de routage génèrent des URL avec `http://localhost:8000` à cause de la configuration de développement de Testbench, définissez `APP_URL` sur `http://localhost` dans le processus de test avant de lancer Pest.

Le script `composer style` lance la normalisation des fichiers Composer et les corrections de Rector et de Pint. Vérifiez les modifications avant de soumettre la pull request. Pour mesurer la couverture des lignes, activez Xdebug et exécutez `composer run-script --timeout=0 test:coverage`.

Pour les modifications de manifestes, exécutez la même validation du schéma que dans l’intégration continue.

```bash
npx --yes ajv-cli@5.0.0 validate --spec=draft2020 -s resources/laravel-lang.schema.json -d 'src/*/laravel_lang.json'
```

Consultez [Plugins de traduction](./plugins.md).

## Modifier la documentation {#edit-the-documentation}

Les pages en anglais de la version actuelle se trouvent dans `docs/docs/` et leurs traductions dans `docs/i18n/<language>/docusaurus-plugin-content-docs/current/`. Lorsque les instructions changent, mettez à jour les pages correspondantes et conservez les mêmes identifiants de titres pour les liens dans chaque langue. La version archivée d’avant Mona se trouve dans `docs/versioned_docs/version-legacy/` et ses traductions dans les répertoires `version-legacy/` correspondants sous `docs/i18n/`.

Utilisez la version de Node.js exigée dans `docs/package.json`. Exécutez les commandes de documentation dans `docs/`.

```bash
cd docs
npm ci
npm start
```

Utilisez `npm start -- --locale fr` pour prévisualiser une seule traduction. Arrêtez le serveur de développement et lancez la compilation de production pour vérifier les liens, les ancres et les index de recherche dans toutes les langues configurées.

```bash
npm run typecheck
npm run build
npm run serve
```

Ouvrez l’adresse affichée par `serve` pour vérifier la recherche et les exports Markdown générés. Les fichiers du site statique sont écrits dans `docs/build/`.

## Publier une version {#publish-a-release}

Une fusion dans `main` exporte chaque sous-arborescence de paquet vers la branche `main` de son dépôt et met à jour le brouillon de la publication Mona. Relisez le brouillon et publiez la version sur Mona avec le tag de version commun. Les workflows de publication se déclenchent à la publication de la version.

La publication exporte son tag et crée la version correspondante dans chaque dépôt de paquet. Elle déclenche aussi la compilation et le déploiement du site de documentation. Vérifiez ensuite les deux workflows. Pour prévisualiser les sous-arborescences de paquet avant publication, lancez manuellement **Export Packages** depuis `main` avec `dry-run` activé. [Export Packages](https://github.com/Laravel-Lang/mona/actions/workflows/export-packages.yml) · [Documentation](https://github.com/Laravel-Lang/mona/actions/workflows/docs.yml)
