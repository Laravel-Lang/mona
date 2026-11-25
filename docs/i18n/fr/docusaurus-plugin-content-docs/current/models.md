---
title: "Traductions des modèles"
description: "Stockez, lisez et interrogez les attributs Eloquent localisés avec Laravel-Lang Models."
---

`laravel-lang/models` ajoute des attributs traduits à un modèle Eloquent existant. Chaque ligne de traduction stocke un code de langue et les valeurs des attributs dans cette langue. Common inclut ce paquet, qui nécessite l’extension PHP Intl.

```bash
composer require laravel-lang/models
```

## Ajouter des traductions à un modèle {#add-translations-to-a-model}

Pour un modèle `App\Models\Article` existant qui possède déjà une migration pour sa table principale, exécutez la commande suivante.

```bash
php artisan make:model-translation 'App\Models\Article' --columns=title --columns=description
php artisan migrate
```

Le générateur ajoute le trait `LaravelLang\Models\HasTranslations` au modèle principal et crée le modèle associé `ArticleTranslation`, une migration et un fichier d’aide pour l’IDE. La migration crée une clé étrangère vers l’article, une contrainte d’unicité sur la paire `(item_id, locale)` et des colonnes de type chaîne qui acceptent la valeur nulle pour les valeurs traduites. Si la table principale contient déjà ces valeurs, déplacez-les dans les lignes de traduction avant d’utiliser les accesseurs du nouveau modèle.

Si le modèle se trouve en dehors de `app/Models`, ajoutez son répertoire à [`localization_models.directories`](./configure.md) avant de lancer le générateur.

Lancez `php artisan make:model-translation` pour choisir le modèle et ses colonnes de manière interactive. Sans saisie, le générateur utilise `title` et `description`. Pour ajouter un champ plus tard, créez une migration et ajoutez le champ aux attributs assignables et aux conversions du modèle de traduction.

## Écrire et lire les attributs traduits {#write-and-read-translated-attributes}

Une fois [le français installé](./translations.md), vous pouvez stocker une traduction en passant son code canonique, son alias, un cas de l’énumération `Locale` ou un objet `LocaleData`. Utilisez la méthode `save()` du modèle principal pour enregistrer les changements.

```php
use App\Models\Article;
use LaravelLang\LocaleList\Locale;

$article = Article::create(['title' => 'First article']);
$article->setTranslation('title', 'Premier article', Locale::French)->save();

$article->title;                         // current locale, then fallback for a null value
$article->getTranslation('title', 'fr');  // 'Premier article'
```

Si vous indiquez une langue, la lecture utilise sa valeur. Sans argument de langue, l’écriture utilise la langue active et la lecture se rabat sur la langue de repli configurée dans Laravel lorsque la valeur active est `null`. Les alias sont convertis en codes canoniques pour la recherche et le stockage. Un code explicitement fourni qui désigne une langue inconnue ou non installée déclenche une exception `LaravelLang\Exceptions\UnknownLocaleException`.

Utilisez `setTranslations()` pour définir plusieurs valeurs d’un même champ. Vous pouvez aussi passer à `create()` ou `fill()`, pour chaque champ traduit, un tableau indexé par code de langue. L’exemple suppose que l’anglais et le français sont installés.

```php
$article->setTranslations('title', [
    'en' => 'First article',
    'fr' => 'Premier article',
])->save();
```

Utilisez `isTranslatable('title')` pour vérifier les champs configurés dans le modèle et `hasTranslated('title', 'fr')` pour vérifier qu’une valeur est renseignée. `forgetTranslation('fr')` supprime la ligne française et `forgetAllTranslations()` supprime toutes les lignes de traduction du modèle.

## Interroger les traductions {#query-translations}

`translated()`, `whereTranslation()` et `whereTranslationLike()` effectuent leurs requêtes via la relation `translations()`. Par défaut, cette relation filtre les lignes sur la langue active et la langue de repli. Pour interroger d’autres langues avec ces méthodes de requête, définissez `localization_models.filter` sur `false`. Pour lire toutes les lignes d’un seul modèle, utilisez `translationsRaw()`.

Lorsque la langue active est le français, voici comment filtrer et trier selon le titre français.

```php
use App\Models\Article;

$articles = Article::query()
    ->whereTranslationLike('title', 'Premier%', 'fr')
    ->orderByTranslation('title', 'asc', 'fr')
    ->get();
```

Les modèles préchargent la relation `translations()`. `orWhereTranslation()` et `orWhereTranslationLike()` ajoutent des conditions alternatives. `orderByTranslation()` trie selon une seule langue, celle en cours par défaut.

## Observer les changements et actualiser les fichiers d’aide pour l’IDE {#observe-changes-and-refresh-ide-helpers}

L’espace de noms `LaravelLang\Models\Events` contient `TranslationHasBeenSetEvent`, `TranslationHasBeenForgetEvent` et `AllTranslationsHasBeenForgetEvent`. L’événement d’affectation contient le modèle, la colonne, l’alias de langue facultatif, l’ancienne valeur et la nouvelle. Il est déclenché pendant l’affectation de l’attribut, avant l’enregistrement qui suit.

Exécutez `php artisan lang:models-helper` pour régénérer les fichiers d’aide de tous les modèles détectés. Ils sont enregistrés par défaut dans `vendor/_laravel_lang`. Ce chemin est défini par `localization_models.helpers`.
