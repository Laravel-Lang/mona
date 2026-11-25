---
title: "Routes localisées"
description: "Enregistrez des URL localisées et déterminez la langue de Laravel à partir d’une requête."
---

`laravel-lang/routes` détermine la langue lors du traitement de la requête et génère les URL des groupes de routes localisées. Common inclut ce paquet.

```bash
composer require laravel-lang/routes
```

Assurez-vous que [les langues utilisées dans vos URL sont installées](./translations.md). Configurez les noms des paramètres, les middlewares et le comportement de la langue par défaut dans [`config/localization_routes.php`](./configure.md).

## Enregistrer un groupe de routes localisées {#register-a-localized-route-group}

Dans `routes/web.php`, utilisez la macro `Route::localizedGroup()`. La façade `LaravelLang\Routes\Facades\LocalizationRoute::group()` permet de déclarer le même groupe.

```php
use Illuminate\Support\Facades\Route;

Route::localizedGroup(function (): void {
    Route::get('/products/{product}', fn (string $product) => view('products.show', [
        'product' => $product,
    ]))->name('products.show');
});
```

Avec la configuration par défaut, la fonction de rappel enregistre les deux motifs d’URL.

| Motif | Nom de la route |
| --- | --- |
| `/products/{product}` | `products.show` |
| `/{locale}/products/{product}` | `localized.products.show` |

Le middleware de langue traite le paramètre `locale` avant d’appeler le gestionnaire de la route. Si la route est nommée, une requête dont l’URL contient un code de langue inconnu ou non installé redirige vers la route sans préfixe.

Laravel appelle deux fois la fonction du groupe. Si le groupe parent préfixe ses URL par `admin` et ses noms par `admin.`, la route localisée se nomme `admin.localized.products.show`.

## Générer une URL localisée {#generate-a-localized-url}

```php
use function LaravelLang\Routes\localizedRoute;

$url = localizedRoute('products.show', [
    'product' => 42,
    'locale' => 'fr',
], absolute: false);
// /fr/products/42
```

La fonction utilise le nom de paramètre de langue configuré et privilégie la route préfixée si elle existe. Elle transmet le code fourni tel quel à la génération de l’URL. Pour générer l’URL avec un alias, passez cet alias ou récupérez-le avec [`Locales::toAlias()`](./locales.md). Si vous omettez la langue, la fonction utilise `Locales::fallback()`. Pour utiliser la langue active, passez explicitement `Locales::current()`.

`localization_routes.hide` retire la langue de repli des URL générées par la fonction. `localization_routes.redirect` redirige les requêtes pour la langue active du groupe préfixé vers la route sans préfixe. Les deux paramètres valent `false` par défaut.

Passez `Locale::French->value` pour utiliser le code canonique ou un objet `LocaleData` pour utiliser son alias. Validez le choix de l’utilisateur avec `Locales::isInstalled()` avant de générer l’URL.

## Déterminer la langue avec un middleware {#select-a-locale-with-middleware}

Vous pouvez appliquer chaque middleware directement par sa classe dans `LaravelLang\Routes\Middlewares`.

| Middleware | Source |
| --- | --- |
| `LocalizationByParameterMiddleware` | Un paramètre de route, `locale` par défaut |
| `LocalizationByParameterWithRedirectMiddleware` | Un paramètre de route. Si la route est nommée et le paramètre déclaré, la langue active remplace la langue demandée lorsqu’elle est absente ou non installée |
| `LocalizationByCookieMiddleware` | Un cookie nommé `Accept-Language` par défaut |
| `LocalizationByHeaderMiddleware` | La première locale installée selon l’ordre de préférence de l’en-tête `Accept-Language`. Le middleware essaie d’abord la balise complète, puis la langue de base. Il ignore les jokers et les entrées dont le poids vaut 0. |
| `LocalizationBySessionMiddleware` | Une clé de session nommée `Accept-Language` par défaut |
| `LocalizationByModelMiddleware` | L’attribut `locale` de l’utilisateur Eloquent authentifié par défaut. Ajoutez `:guard_name` au nom de la classe du middleware pour choisir un garde d’authentification |

Le groupe préfixé utilise par défaut, dans cet ordre, le paramètre d’URL, le cookie, l’en-tête, la session puis le modèle. Le groupe sans préfixe suit le même ordre sans l’étape du paramètre d’URL. Une source valide consultée plus tard peut remplacer la valeur d’une source précédente. Un changement de langue met à jour Laravel et enregistre le code canonique dans la clé de session configurée. Utilisez le groupe de middlewares `web` si vous avez besoin des sessions et des cookies. Le middleware d’en-tête nécessite l’extension PHP Intl.

Lorsque le middleware change la langue, il déclenche `LaravelLang\Routes\Events\LocaleHasBeenSetEvent`. La propriété `locale` de l’événement est un objet `LaravelLang\LocaleNames\LocaleData`. Utilisez `$event->locale->code` pour obtenir le code canonique.

Pour donner la priorité à l’URL, conservez uniquement `LocalizationByParameterPrefixMiddlewareMiddleware::class` dans `localization_routes.middlewares.prefix`. Configurez séparément la chaîne sans préfixe.
