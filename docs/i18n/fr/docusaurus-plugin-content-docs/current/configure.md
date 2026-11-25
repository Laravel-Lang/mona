---
title: "Configuration"
description: "Configurez les styles de traduction, les alias de langue, les filtres de publication, la ponctuation, les routes et les modèles de Mona."
---

Mona charge automatiquement les paramètres par défaut. Publiez les fichiers à personnaliser dans le répertoire `config/` de votre application.

```bash
php artisan vendor:publish --tag=localization_main
```

Le tag `localization` publie les cinq fichiers. Chaque nom de fichier sans l’extension `.php` sert aussi de tag de publication et de clé de configuration Laravel.

| Fichier | Paramètres |
| --- | --- |
| `localization_main.php` | Styles de traduction, alias et filtres de publication |
| `localization_punctuation.php` | Ponctuation intelligente et guillemets propres à chaque langue |
| `localization_routes.php` | [Noms des routes, sources de langue et middlewares](./routes.md) |
| `localization_models.php` | [Suffixe des modèles de traduction, filtrage et chemins du générateur](./models.md) |
| `localization_map.php` | Métadonnées de langue, par exemple l’écriture, la région et le sens de lecture |

Laravel remplace chaque paramètre de premier niveau par la valeur définie dans l’application. Si vous modifiez le tableau `locales` dans `localization_punctuation.php`, conservez les réglages par défaut des langues dont vous avez encore besoin.

Après une modification, videz le cache de configuration s’il existe avec `php artisan config:clear`. Exécutez `php artisan lang:update` pour régénérer les traductions concernées par les paramètres de publication.

## Styles de traduction {#translation-styles}

Un style de traduction est un dictionnaire nommé qui modifie la formulation de certaines expressions. Par exemple, un pack peut fournir `informal.json` pour adopter un ton familier, ou `john.json` pour reprendre la terminologie d’un produit. Les noms de styles sont indépendants des codes de langue et des noms d’applications ou de paquets. Tout pack de traduction peut fournir un style sous `locales/<locale>/<style>.json` pour les langues qu’il couvre. Un style peut contenir une seule expression. À l’origine, cette fonctionnalité servait à gérer le tutoiement. [Discussion sur les traductions familières](https://github.com/orgs/Laravel-Lang/discussions/124)

Après avoir installé un plugin qui fournit le style `john`, placez ce style en dernier dans `config/localization_main.php` pour lui donner la priorité au sein de chaque source de dictionnaires.

```php
'styles' => ['default', 'informal', 'john'],
```

Lorsque vous exécutez `php artisan lang:update`, le Publisher fusionne les fichiers de styles de chaque langue installée. Pour chaque expression, `john` a la priorité s’il la définit, puis `informal` et enfin `default`. Par exemple, un pack peut redéfinir une expression française dans `locales/fr/john.json`, tandis que l’allemand utilise `locales/de/informal.json` ou `locales/de/default.json` pour la même expression. Le Publisher applique cet ordre séparément à la bibliothèque partagée Library et au pack qui fournit le fichier. Dans ce fichier, l’expression du pack a priorité sur l’entrée correspondante de Library.

Le réglage par défaut est `['default', 'inline']`. Le style `inline` fournit des messages de validation génériques, par exemple « Ce champ doit être accepté. » Pour utiliser uniquement les formulations standard, définissez `styles` sur `['default']`. Si la liste ne contient pas `default`, le Publisher l’ajoute au début.

## Alias de langue {#locale-aliases}

Définissez `localization_main.aliases` sous forme de correspondances entre codes canoniques et alias. Ajoutez par exemple l’entrée suivante au tableau renvoyé par `config/localization_main.php`.

```php
'aliases' => [
    'de_CH' => 'de-CH',
],
```

Artisan peut accepter `de-CH`, tandis que Laravel et les fichiers publiés utilisent `de_CH`. Utilisez les codes canoniques pour `APP_LOCALE` et `APP_FALLBACK_LOCALE`. Consultez [Langues et libellés](./locales.md) pour voir des exemples de conversion et de recherche.

La comparaison des alias est sensible à la casse. Attribuez à chaque langue un alias unique, différent des codes canoniques des autres langues.

## Exclure des fichiers et forcer des intégrations {#skip-files-and-force-integrations}

- `localization_main.skip` compare des motifs `*` sans distinction de casse aux chemins complets, dont le séparateur est `/`. Ce paramètre peut exclure les fichiers sources, les dictionnaires et les destinations de publication. Par exemple, `['*/lang/fr/validation.php']` empêche le Publisher d’écrire ce fichier dans une application qui utilise `lang/`.
- `localization_main.force` accepte des noms de paquets Composer. Ce paramètre permet au Publisher d’utiliser des entrées de manifeste même si la dépendance indiquée est absente, si la version installée ne correspond pas ou si le projet racine porte un autre nom. Utilisez-le lorsque les traductions du manifeste s’appliquent à votre application.

`skip` a la priorité sur `force`.

## Ponctuation intelligente {#smart-punctuation}

Définissez `LOCALIZATION_PUNCTUATION_ENABLED=true` pour activer les guillemets et tirets typographiques ainsi que les points de suspension dans les traductions publiées. Modifiez `localization_punctuation.common` pour les règles générales et `localization_punctuation.locales` pour les règles particulières définies par code de langue canonique. Le Publisher applique ces règles la prochaine fois qu’il écrit les chaînes publiées.

## Métadonnées des langues {#locale-metadata}

`localization_map.locales` associe les codes canoniques au système d’écriture (`type`), aux paramètres régionaux (`regional`) et à un sens d’écriture facultatif (`direction`). Utilisez `Direction::LeftToRight` si le sens d’écriture est absent.

```php
use LaravelLang\LocaleList\Direction;

$metadata = config('localization_map.locales.ar', []);
$script = $metadata['type'];         // 'Arab'
$regional = $metadata['regional'];  // 'ar_AE'
$dir = ($metadata['direction'] ?? Direction::LeftToRight)->value; // 'rtl'
```
