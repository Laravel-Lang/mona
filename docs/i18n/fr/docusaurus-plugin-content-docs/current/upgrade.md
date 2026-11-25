---
title: "Mettre à niveau vers Mona"
description: "Mettez à niveau les dépendances et la configuration de Laravel-Lang, les traductions publiées et les intégrations de votre application."
---

Mona est le dépôt qui publie les paquets Laravel-Lang ensemble. Continuez d’utiliser les paquets Composer dont votre application a besoin.

:::info[Pourquoi la version 20 ?]

Laravel-Lang s’est développé à partir de `laravel-lang/lang`. Les paquets sont apparus à des moments différents, chacun avec sa propre numérotation et son propre rythme de publication. Les mises à niveau majeures demandaient de plus en plus de coordination à l’équipe. De votre côté, il fallait déterminer quelles versions pouvaient être installées ensemble.

Nous avons créé `laravel-lang/common` pour faciliter l’installation en déclarant des dépendances compatibles entre elles. Une mise à niveau pouvait tout de même concerner plusieurs paquets dont les versions évoluaient indépendamment.

Par exemple, quelles versions auriez-vous installées à l’époque dans une application qui utilisait Laravel 11 et PHP 8.2 ?

<details>
<summary>Versions des paquets avant Mona</summary>

| Paquet | Version |
| --- | --- |
| Common | `6.9.0` |
| Actions | `1.13.2` |
| Attributes | `2.16.6` |
| Lang | `15.39.2` |
| MoonShine | `1.9.3` |
| HTTP Statuses | `3.13.2` |
| Currency Names | `1.11.0` |
| Locale Names | `2.10.0` |
| Country Names | `1.8.0` |
| Starter Kits | `1.17.4` |
| Locales | `2.11.0` |
| Models | `1.8.0` |
| Routes | `1.11.0` |
| JSON Fallback | `2.3.0` |
| Locale List | `1.7.0` |
| Config | `1.17.1` (version de la branche 1.x que les autres paquets Laravel-Lang continuaient d’utiliser) et `2.3.2` (dernière version stable avant Mona, peu utilisée par ces paquets) |
| Publisher | `16.9.0` |

</details>

Avec le monorepo Mona, nous publions les paquets ensemble sous un même numéro de version.

Publisher avait le numéro de version le plus élevé. Sa prochaine version majeure aurait été la `17.0`. Nous avons consacré beaucoup de travail et de soin à réunir l’écosystème et choisi la version 20 pour marquer cette étape. On peut aussi y lire **Laravel-Lang 2.0**. 🙂

:::

:::info[Codes de langue et alias]

Laravel-Lang utilise désormais les codes de langue canoniques pour les valeurs stockées, notamment les chemins des traductions publiées et les lignes de traduction des modèles. Laravel utilise aussi des codes canoniques pour la langue active et la langue de repli. À l’exécution, utilisez les alias pour les segments d’URL et les sélecteurs de langue. Les méthodes de façade qui renvoient des codes utilisent par défaut l’alias configuré, ou le code canonique en l’absence d’alias. Par exemple, ajoutez `'pt_BR' => 'pt-br'` dans `localization_main.aliases` pour garder l’URL `/pt-br/account` tout en publiant dans `lang/pt_BR.json` et en stockant `pt_BR` dans la base de données.

:::

## Mettre à jour les dépendances {#updating-dependencies}

La version actuelle prend en charge Laravel 12 et 13 et nécessite PHP `^8.3`. Dans le fichier `composer.json` de votre application, définissez la contrainte `^20.0` pour chacun des paquets ci-dessous qui figure dans `require` ou `require-dev`. Si vous avez uniquement installé `laravel-lang/common`, c’est la seule dépendance à modifier.

Avant d’installer `20.x` ou de passer à cette version, vérifiez que la version du paquet Composer est disponible sur Packagist. La version Mona et les paquets Composer correspondants peuvent être disponibles à des moments différents.

| Usage | Paquets Composer |
| --- | --- |
| Ensemble de paquets | `laravel-lang/common` |
| Packs de traduction | `laravel-lang/lang`, `laravel-lang/starter-kits`, `laravel-lang/moonshine`, `laravel-lang/actions`, `laravel-lang/attributes`, `laravel-lang/http-statuses` |
| Publication et configuration | `laravel-lang/publisher`, `laravel-lang/config`, `laravel-lang/json-fallback`, `laravel-lang/library` |
| Intégrations à l’application | `laravel-lang/locales`, `laravel-lang/locale-list`, `laravel-lang/routes`, `laravel-lang/models` |
| Utilitaires partagés | `laravel-lang/exceptions`, `laravel-lang/filesystem` |
| Modèle de pack | `laravel-lang/translations-template` |

Si votre installation utilise uniquement le processus de traduction standard, mettez à jour les dépendances et republiez vos traductions.

```bash
composer update
php artisan lang:update
```

Si vous avez enregistré des fournisseurs de services, publié des paramètres personnalisés, utilisé des alias ou des packs de traduction tiers, ou encore appelé les API des paquets dans votre application, suivez les sections pertinentes ci-dessous avant de republier.

## Remplacer les dépendances renommées ou obsolètes {#replace-renamed-or-retired-dependencies}

Mettez à jour dans `composer.json` toute dépendance directe aux anciens paquets de libellés avant d’exécuter `composer update`.

| Ancien paquet Composer | Dépendance à ajouter en version `^20.0` |
| --- | --- |
| `laravel-lang/native-country-names` | `laravel-lang/country-names` |
| `laravel-lang/native-currency-names` | `laravel-lang/currency-names` |
| `laravel-lang/native-locale-names` | `laravel-lang/locale-names` |

Common dépend déjà des trois paquets de libellés. Mettez également à jour votre code PHP. Consultez [Remplacer les API de recherche de libellés](#replace-the-name-lookup-apis).

Pour chaque plugin tiers, vérifiez qu’une version est compatible avec le nouveau Publisher. Migrez toute utilisation de `laravel-lang/translator` ou `laravel-lang/status-generator` avant de supprimer ces dépendances. Consultez [les outils de traduction](#update-json-fallback-and-translation-tooling).

## Mettre à jour les fournisseurs de services {#update-service-providers}

Laravel détecte les fournisseurs de services des paquets Config, Publisher, Routes et Models à partir de leurs manifestes. Si vous utilisez la détection automatique, mettez à jour le code ou les tests qui font référence aux anciennes classes. Si votre application enregistre ces paquets manuellement, effectuez les remplacements ci-dessous dans la même liste de fournisseurs, par exemple dans `bootstrap/providers.php` ou `config/app.php`.

- `LaravelLang\Config\ServiceProvider` → `LaravelLang\Config\LangConfigServiceProvider`
- `LaravelLang\Publisher\ServiceProvider` → `LaravelLang\Publisher\LangPublisherServiceProvider`
- `LaravelLang\Routes\ServiceProvider` → `LaravelLang\Routes\LangRoutesServiceProvider`
- `LaravelLang\Models\ServiceProvider` → `LaravelLang\Models\LangModelsServiceProvider`

Supprimez les enregistrements manuels de `LaravelLang\Locales\LaravelLangLocalesServiceProvider` et des anciennes classes `ServiceProvider` dans `LaravelLang\Actions`, `LaravelLang\Attributes`, `LaravelLang\HttpStatuses`, `LaravelLang\Lang`, `LaravelLang\MoonShine` et `LaravelLang\StarterKits`. Ces paquets de traduction sont détectés grâce à leurs manifestes.

Si vous utilisez le repli JSON, remplacez `LaravelLang\JsonFallback\TranslationServiceProvider` par `LaravelLang\JsonFallback\LangJsonFallbackServiceProvider` à l’endroit où vous l’avez enregistré. Le remplacement du fournisseur de traduction Laravel reste ainsi actif.

Mettez à jour les références aux anciens fournisseurs avant d’exécuter `composer update`, afin que la détection des paquets puisse démarrer l’application. Si un script Composer échoue toujours parce qu’il charge une ancienne classe, exécutez `composer update --no-scripts`, mettez à jour les références, puis exécutez `composer dump-autoload` pour relancer la détection.

## Migrer la configuration {#migrate-configuration}

Si vous avez personnalisé `config/localization.php`, conservez-le comme référence le temps de reporter vos changements. Les paquets chargent automatiquement les nouvelles valeurs par défaut. Publiez les [fichiers de configuration](./configure.md) à modifier. Le tag `localization` publie les cinq fichiers. Pour n’en publier qu’un, utilisez son nom sans l’extension `.php` comme tag.

```bash
php artisan vendor:publish --tag=localization
```

### Alias, styles de traduction et formatage des fichiers {#aliases-translation-styles-and-file-formatting}

- Copiez vos correspondances `aliases` dans `config/localization_main.php`. Gardez les codes canoniques comme clés et les alias comme valeurs, par exemple `'de_CH' => 'de-CH'`.
- Remplacez `inline` par `styles` dans `config/localization_main.php`. Définissez `'styles' => ['default']` pour retrouver les anciens messages de validation standard par défaut. Conservez `['default', 'inline']`, le nouveau réglage par défaut, si vous utilisiez les messages de validation en ligne. Retirez `LOCALIZATION_INLINE` et `LANG_PUBLISHER_INLINE` des environnements où vous les aviez définies.
- Supprimez la clé `align` et les variables d’environnement `LOCALIZATION_ALIGN` et `LANG_PUBLISHER_ALIGN`. Pour aligner les opérateurs `=>` dans les tableaux PHP publiés, configurez l’outil de formatage PHP de votre projet et exécutez-le sur les fichiers de langue après publication. Par exemple, la règle `binary_operator_spaces` de Laravel Pint permet d’appliquer `align_single_space_minimal` à `=>`.

### Ponctuation intelligente {#smart-punctuation}

Si vous avez activé la ponctuation intelligente, renommez `LOCALIZATION_SMART_ENABLED` en `LOCALIZATION_PUNCTUATION_ENABLED` et laissez la valeur à `true`. Si vous avez défini `smart_punctuation.enable` directement dans le PHP, déplacez la valeur booléenne dans la clé `enabled` à la racine de `config/localization_punctuation.php`. La ponctuation intelligente est désactivée par défaut.

Déplacez les guillemets personnalisés de `smart_punctuation.common` et `smart_punctuation.locales` dans les tableaux `common` et `locales` à la racine de ce fichier. Utilisez les codes canoniques comme clés de `locales`. Les guillemets propres à chaque langue remplacent les clés correspondantes de `common`. Si vous avez copié un ancien exemple français avec `&nbsp;`, utilisez dans les chaînes de guillemets un caractère d’espacement insécable ou `"\u{00A0}"`.

### Routes localisées {#localized-routes}

Reportez les paramètres personnalisés qui figuraient sous `routes` dans l’ancienne configuration dans `config/localization_routes.php`.

| Paramètre personnalisé | Changement dans `config/localization_routes.php` | Variable d’environnement à renommer |
| --- | --- | --- |
| Paramètre de langue, en-tête, cookie, clé de session ou colonne du modèle utilisateur | Copiez vos entrées `names` personnalisées et conservez les autres valeurs par défaut. | |
| Préfixe des noms de routes localisées | Renommez `name_prefix` en `prefix`. Si la valeur est vide, le préfixe `localized.` est utilisé. | `LOCALIZATION_NAME_PREFIX` → `LOCALIZATION_PREFIX` |
| Redirections depuis la route préfixée | Renommez `redirect_default` en `redirect`. | `LOCALIZATION_REDIRECT_DEFAULT` → `LOCALIZATION_REDIRECT` |
| Masquage de la langue de repli lors de la génération de l’URL | Renommez `hide_default` en `hide`. | `LOCALIZATION_HIDE_DEFAULT` → `LOCALIZATION_HIDE` |
| Middlewares des groupes de routes localisées | Déplacez `group.middlewares.default` vers `middlewares.default` pour les routes sans préfixe et `group.middlewares.prefix` vers `middlewares.prefix` pour les routes préfixées. Mettez à jour les noms des classes comme indiqué dans [la migration des routes](#update-localized-routes-and-middleware). | |

### Traductions des modèles {#model-translations}

Déplacez vos paramètres personnalisés sous `models` dans `config/localization_models.php`. Copiez les valeurs personnalisées de `suffix` et `helpers` à la racine du fichier. Utilisez un booléen `filter` à la place de l’ancienne clé `filter.enabled`. Si vous réglez le filtrage et le chemin des fichiers d’aide de l’IDE via les variables d’environnement, conservez `LOCALIZATION_FILTER_ENABLED` et `VENDOR_PATH`.

Les commandes de modèles parcourent désormais `app/Models` par défaut. Si vous comptiez auparavant sur le parcours de tout `app/` ou si vous avez défini `localization-private.models.directory`, ajoutez chaque répertoire à parcourir dans le tableau `directories`. Par exemple, définissez `'directories' => [app_path()]` pour conserver l’ancien périmètre.

### Configuration utilisée dans le code de l’application {#configuration-used-by-application-code}

- Remplacez les lectures et écritures via `LaravelLang\Config\Facades\Config` par des appels à la configuration de Laravel avec les nouvelles clés. Par exemple, remplacez `Config::shared()->models->filter->enabled` par `config('localization_models.filter')`. Pour une lecture typée, injectez `LaravelLang\Config\Services\ModelLangConfig` et lisez sa propriété `filter`. Pour les réglages des routes, injectez `RouteLangConfig` du même espace de noms.
- Si votre code lit les métadonnées de langue depuis `localization-private.map`, utilisez désormais `localization_map.locales`. Copiez vos réglages dans le tableau `locales` de `config/localization_map.php`.
- Remplacez l’enregistrement par `localization-private.plugins` et `localization-private.packages` par un [manifeste de plugin](./plugins.md).
- Supprimez l’ancien bloc `translators` et les réglages `TRANSLATION_*` utilisés uniquement par `laravel-lang/translator`.

Après avoir déplacé vos réglages et mis à jour les références dans l’application, supprimez `config/localization.php` et exécutez `php artisan config:clear`.

## Déplacer les fichiers de langue vers les chemins canoniques {#move-language-files-to-canonical-paths}

1. Définissez `APP_LOCALE` et `APP_FALLBACK_LOCALE` avec des codes canoniques. Déplacez vos alias dans `localization_main.aliases` pour que les URL et les paramètres de langue continuent de fonctionner.
2. Renommez les fichiers et répertoires qui utilisent un alias. Par exemple, remplacez `lang/pt-br.json` et `lang/pt-br/` par `lang/pt_BR.json` et `lang/pt_BR/`. Recherchez les mêmes alias dans `lang/vendor/<package>/`. Lorsque les deux noms coexistent, fusionnez les contenus et résolvez les conflits entre traductions personnalisées avant de supprimer le chemin qui utilise l’alias.
3. Installez chaque langue représentée uniquement par des traductions PHP de dépendances, avec `php artisan lang:add <locale>`. La liste des langues installées s’appuie sur les entrées à la racine du répertoire de langues, les fichiers JSON sous `lang/vendor/<package>/` et la langue active et la langue de repli effectives.
4. Exécutez `php artisan lang:update` après avoir migré la configuration et les plugins, puis examinez les différences. Le Publisher peut conserver les clés d’intégrations qui ne publient plus de fichier. Gardez les clés que votre application utilise encore et supprimez les clés obsolètes après avoir vérifié leurs usages. Consultez [Scripts de publication et traductions personnalisées](#update-publication-scripts-and-keep-custom-translations) pour régénérer ou conserver les fichiers.

## Mettre à jour les intégrations {#update-integrations}

### Locales {#locales}

Conservez l’import de `LaravelLang\Locales\Facades\Locales` et adaptez les appels aux données dont chaque appelant a besoin.

| Ancien appel | Remplacement |
| --- | --- |
| `Locales::available()`, `installed()` ou `protects()` renvoie des objets | Appelez la même méthode sur `Locales::data()`. |
| `Locales::raw()->available()`, `installed()` ou `protects()` | Appelez directement la même méthode sur `Locales` pour obtenir une collection de chaînes contenant les alias. Passez `false` pour obtenir les codes canoniques et appelez `->all()` si l’appelant attend un tableau. |
| `Locales::installed(false)` ou `Locales::raw()->installed(false)` | `false` demande désormais les codes canoniques. La langue active et la langue de repli sont incluses même sans fichiers de langue. Remplacez aussi l’ancien argument nommé `withProtects: false`. Utilisez `Locales::installed(false)->diff(Locales::protects(false))->values()` pour les exclure. Pour des objets, utilisez `Locales::data()->installed()->reject(fn ($locale) => Locales::isProtected($locale))->values()`. |
| `Locales::notInstalled()` ou `Locales::raw()->notInstalled()` | Utilisez `Locales::data()->uninstalled()` pour obtenir des objets ou `Locales::uninstalled()` pour les alias sous forme de chaînes. Passez `false` pour obtenir les codes canoniques sous forme de chaînes. |
| `Locales::getCurrent()` ou `Locales::getDefault()` | Utilisez `Locales::data()->current()` pour obtenir un objet de transfert de données (DTO) ou `Locales::current()` pour une chaîne. Remplacez `Locales::raw()->getDefault()` par `Locales::current()`. Si `Locales::raw()->getCurrent()` vous servait à lire la valeur stockée par Laravel, utilisez `app()->getLocale()`. |
| `Locales::getFallback()` ou `Locales::raw()->getFallback()` | Utilisez `Locales::data()->fallback()` pour un DTO ou `Locales::fallback()` pour une chaîne. Si la langue de repli est inconnue, l’anglais est désormais utilisé. Si la langue active est inconnue, la langue de repli effective est utilisée. |
| `Locales::get($locale)` ou `Locales::raw()->get($locale)` | Utilisez `isInstalled()` et choisissez explicitement la langue de repli. Pour une langue prise en charge mais potentiellement non installée, appelez `isAvailable()`, puis `info()`. |
| `Locales::info($locale)` ou `Locales::raw()->info($locale)` | Utilisez `Locales::info($locale)` et lisez `->alias` ou `->code` si vous avez besoin d’une chaîne. Passez `null` pour obtenir la langue active. Pour un code inconnu ou vide, gérez `LaravelLang\Exceptions\UnknownLocaleException` ou vérifiez `isAvailable()` avant la recherche. |

Pour conserver l’ancien comportement de `get()`, qui utilisait la langue active si le code saisi était invalide ou désignait une langue non installée, faites ce choix dans le code appelant.

```php
use LaravelLang\Locales\Facades\Locales;

$locale = Locales::acceptsInput($requested) && Locales::isInstalled($requested)
    ? Locales::info($requested)
    : Locales::data()->current();

$code = $locale->code;   // À utiliser dans les chemins, les lignes de base de données et les autres données persistées.
$alias = $locale->alias; // À utiliser dans les URL et les sélecteurs de langue.
```

Mettez à jour les gestionnaires de requêtes qui transmettaient le résultat de `raw()->get()` à `app()->setLocale()`. Utilisez `Locales::set($locale)` après avoir accepté la valeur de la requête. Cette méthode accepte les codes des langues installées, les alias configurés, les cas d’énumération et le nouveau DTO, et affecte à Laravel le code canonique. Pour la valeur de requête `pt-br`, Laravel reçoit `pt_BR`, tandis que `Locales::current()` renvoie `pt-br`. Appelez `Locales::current(false)` ou `Locales::fallback(false)` là où vous avez besoin des codes canoniques.

Pour enregistrer ces données ailleurs dans l’application, validez la valeur avec `isAvailable()` ou `isInstalled()`, puis utilisez `fromAlias()` pour obtenir son code canonique. Utilisez `toAlias()` pour préparer l’affichage d’un code canonique. Remplacez l’ancien `Services\Resolver` par `acceptsInput()` pour vérifier les types PHP acceptés ou par `normalize()` pour extraire une chaîne d’une valeur typée. `normalize()` conserve les chaînes telles qu’elles sont fournies, y compris un alias comme `pt-br`.

Remplacez `LaravelLang\Locales\Data\LocaleData` par `LaravelLang\LocaleNames\LocaleData` dans les imports, les déclarations de types et les tests. Le nouvel objet en lecture seule possède les propriétés de type chaîne `code`, `alias`, `native` et `localized`. Pour les URL et les valeurs de l’interface, remplacez l’ancienne propriété `code` par `alias`. Utilisez `Locale::from($data->code)` là où vous lisiez `$data->locale` et importez `LaravelLang\LocaleList\Locale`. Si vos gabarits ou formateurs utilisent `type`, `regional` et `direction`, lisez-les depuis `config("localization_map.locales.{$data->code}", [])`. Définissez vos valeurs de repli pour les entrées manquantes. Pour conserver les anciennes valeurs par défaut, utilisez `Latn` pour l’écriture, le code canonique pour la région et `Direction::LeftToRight` pour le sens de lecture.

Supprimez les options `withCountry`, `withCurrency`, `withCountries` et `withCurrencies`. Récupérez explicitement le pays et la devise pour l’entrée de langue. Remplacez les imports des anciens types `LaravelLang\Locales\Data\CountryData` et `CurrencyData` par ceux des classes de données des paquets de libellés correspondants.

```php
use LaravelLang\CountryNames\CountryNames;
use LaravelLang\CurrencyNames\CurrencyNames;
use LaravelLang\Locales\Facades\Locales;

$locale = Locales::info('pt_BR');
$displayLanguage = Locales::current(false);

$country = app(CountryNames::class)->one($displayLanguage, $locale->code);
$currency = app(CurrencyNames::class)->one($displayLanguage, $locale->code);
```

Si vous appeliez `localizedLocales()`, `localizedCountries()` ou `localizedCurrencies()` sur l’ancien service, utilisez `all(null)` et `all($displayLanguage)` sur le service de libellés correspondant pour obtenir respectivement les noms d’origine et les noms traduits. Remplacez l’injection directe des anciennes classes `Services\Locales` ou `Services\RawLocales` par la façade ou par les services correspondants `Services\MainService` et `Services\DataService`. Consultez [Langues et libellés](./locales.md) pour les nouvelles API.

#### Méthodes utilitaires de l’énumération Locale {#locale-enum-helpers}

Les cas des énumérations `Locale` et `Direction` gardent leurs valeurs. Remplacez les méthodes supprimées de `archtechx/enums` lors de la mise à niveau des listes ou sélecteurs de langue personnalisés.

| Ancienne méthode | Remplacement |
| --- | --- |
| `names()` | `array_column(Locale::cases(), 'name')` |
| `options()` | Créez la correspondance entre noms et valeurs avec `array_column(Locale::cases(), 'value', 'name')`. |
| `stringOptions()` | Générez les options dans la vue à partir de `Locale::cases()`. Utilisez la valeur du cas pour obtenir un code canonique ou `Locales::toAlias($case)` pour obtenir un alias d’affichage. |
| `fromName()` ou `tryFromName()` | Recherchez le cas par son `name` dans `Locale::cases()` et gérez l’absence de correspondance. Pour une valeur associée comme `pt_BR`, utilisez `Locale::from()` ou `Locale::tryFrom()`. |
| `Locale::onlyValues()` | Créez la correspondance entre valeurs identiques avec `array_column(Locale::cases(), 'value', 'value')`. Pour une simple liste de valeurs, utilisez `Locale::values()`. |
| `Direction::values()` | Utilisez `array_column(Direction::cases(), 'value')`. |

### Remplacer les API de recherche de libellés {#replace-the-name-lookup-apis}

Remplacez les imports `LaravelLang\NativeCountryNames`, `LaravelLang\NativeCurrencyNames` et `LaravelLang\NativeLocaleNames` par `LaravelLang\CountryNames`, `LaravelLang\CurrencyNames` et `LaravelLang\LocaleNames`. Récupérez `CountryNames`, `CurrencyNames` ou `LocaleNames` via le conteneur Laravel ou injectez le service dans votre classe.

| Ancien usage | Remplacement |
| --- | --- |
| `SomeNames::get($displayLocale)` | `$names->all($displayLocale)` |
| `SomeNames::get()` pour les noms d’origine | `$names->all(null)` |
| `SortBy::None` | Omettez l’argument de tri. |
| `SortBy::Key` | Appelez `->sortKeys()` sur la collection retournée. |
| `SortBy::Value` | Passez `SortDirection::Ascending` à `all()` pour trier par libellé traduit. |

Utilisez `one($displayLocale, $entryLocale)` pour récupérer une entrée connue. La clé de recherche est un code de langue, par exemple `fr`. Ainsi, `app(CountryNames::class)->one('de', 'fr')` renvoie le pays correspondant au français, avec son nom en allemand et la propriété `code` définie sur `FR`. Les DTO de pays et de devises se trouvent dans l’espace de noms racine de leur paquet et exposent la clé de recherche dans `locale`. Le champ `code` contient toujours le code de pays ou de devise.

Les trois services renvoient des collections indexées par code de langue canonique. Si vous utilisiez le tableau de chaînes de l’ancien paquet de noms de langues, extrayez la propriété `localized` de chaque DTO. Si vous appeliez `toArray()` sur les anciens DTO de pays ou de devises ou que vous modifiiez leurs propriétés, construisez explicitement votre tableau dans l’application. Les nouveaux DTO sont en lecture seule.

```php
use LaravelLang\LocaleNames\LocaleData;
use LaravelLang\LocaleNames\LocaleNames;

$options = app(LocaleNames::class)
    ->all('de', SortDirection::Ascending)
    ->map(fn (LocaleData $locale): string => $locale->localized)
    ->all();
```

Validez les langues choisies par l’utilisateur dans le catalogue concerné avant d’appeler `one()`. Une langue d’affichage inconnue peut amener `all()` à renvoyer une liste vide. Passez `null` quand l’application a besoin des noms d’origine. Si votre code utilisait une énumération personnalisée à valeurs associées, passez sa valeur sous forme de chaîne. [Langues et libellés](./locales.md)

### Mettre à jour les routes localisées et les middlewares {#update-localized-routes-and-middleware}

Importez la fonction avec son espace de noms dans les fichiers PHP et les gabarits Blade qui génèrent des URL localisées. Vous pouvez également appeler directement `\LaravelLang\Routes\localizedRoute()`.

```php
use LaravelLang\Locales\Facades\Locales;

use function LaravelLang\Routes\localizedRoute;

$url = localizedRoute('products.show', [
    'product' => 42,
    'locale' => Locales::current(),
]);
```

`Route::localizedGroup()` et `LocalizationRoute::group()` enregistrent à la fois les routes sans préfixe et les routes préfixées par une langue. Conservez le groupe existant et revoyez les appels à la fonction de génération d’URL. Elle utilise l’alias de la langue de repli lorsque le paramètre de langue est omis. Pour un sélecteur de langue, transmettez explicitement l’alias souhaité avec `Locales::toAlias($locale)` ou `$localeData->alias`. Si vous transmettez une chaîne comme code de langue, elle est utilisée telle quelle. Si vous avez renommé le paramètre, utilisez dans le tableau la clé définie par `localization_routes.names.parameter`.

Remplacez les imports et les enregistrements dans vos routes, `bootstrap/app.php` et les listes de middlewares personnalisés. Toutes les classes de ce tableau se trouvent dans `LaravelLang\Routes\Middlewares`.

| Ancien middleware | Remplacement |
| --- | --- |
| `LocalizationByCookie` | `LocalizationByCookieMiddleware` |
| `LocalizationByHeader` | `LocalizationByHeaderMiddleware` |
| `LocalizationByModel` | `LocalizationByModelMiddleware` |
| `LocalizationBySession` | `LocalizationBySessionMiddleware` |
| `LocalizationByParameter` | `LocalizationByParameterMiddleware` |
| `LocalizationByParameterWithRedirect` | `LocalizationByParameterWithRedirectMiddleware` |
| `LocalizationByParameterPrefix` | `LocalizationByParameterPrefixMiddlewareMiddleware` |

Si vous construisiez le middleware du modèle avec un garde d’authentification, passez-le comme paramètre de middleware Laravel, par exemple `LocalizationByModelMiddleware::class . ':admin'`. Son mécanisme de résolution de l’utilisateur attend un modèle Eloquent. Dans les sous-classes de middlewares personnalisés, injectez `LaravelLang\Config\Services\RouteLangConfig`, lisez les noms des sources depuis `$this->routeConfig->name` et renvoyez `int|string|null` depuis `detect()`. Remplacez les références directes à `LaravelLang\Routes\Services\Route` par `LaravelLang\Routes\Services\RouteService`.

Testez chaque source utilisée par votre application. La détection dans l’en-tête utilise PHP Intl pour vérifier les langues installées dans l’ordre de préférence d’`Accept-Language`. Elle essaie l’étiquette complète avant la langue de base. Un middleware exécuté plus tard peut remplacer la langue choisie par une source précédente. Pour donner la priorité à l’URL dans les routes préfixées, conservez uniquement le middleware qui sélectionne la langue dans l’URL dans la chaîne de ces routes.

Testez séparément la génération d’URL et les requêtes. `localization_routes.hide` omet le segment de la langue de repli lors de la génération d’une URL. `localization_routes.redirect` redirige les requêtes préfixées pour la langue active vers la route sans préfixe. Une route préfixée et nommée redirige aussi une requête dont la langue est invalide ou non installée vers la route sans préfixe.

Dans les écouteurs de `LocaleHasBeenSetEvent`, utilisez `$event->locale->code` pour le code canonique et `$event->locale->alias` pour l’affichage. Les données de l’événement sont désormais un objet `LaravelLang\LocaleNames\LocaleData`. Le middleware déclenche l’événement lorsque la langue sélectionnée change et enregistre le code canonique dans la session. Remplacez les appels manuels à `LocaleHasBeenSetEvent::dispatch($data)` par `event(new LocaleHasBeenSetEvent($data))`. [Routes localisées](./routes.md)

### Migrer le code et les données des traductions de modèles {#migrate-model-translation-code-and-data}

Dans chaque table de traduction, recherchez les lignes contenant un alias dans la colonne `locale`. Pour chaque alias, remplacez-le par son code canonique dans une migration de données. Avant la mise à jour, vérifiez si le même `item_id` possède déjà une ligne sous ce code canonique. Fusionnez les valeurs à conserver dans une seule ligne pour respecter la contrainte d’unicité `(item_id, locale)`. Certaines versions de l’ancien trait écrivaient déjà les codes canoniques. Basez cette migration sur les valeurs effectivement stockées dans vos tables.

Mettez à jour les imports et les classes générées.

| Ancien usage | Remplacement |
| --- | --- |
| `LaravelLang\Locales\Data\LocaleData` | `LaravelLang\LocaleNames\LocaleData` |
| `LaravelLang\Models\Casts\TrimCast` | `LaravelLang\Models\Casts\TrimStringCast` |
| `LaravelLang\Models\Exceptions\UnavailableLocaleException` | `LaravelLang\Exceptions\UnknownLocaleException` |
| `LaravelLang\Models\Exceptions\AttributeIsNotTranslatableException` ou `UnknownModelPathException` | La classe de même nom dans `LaravelLang\Exceptions`. |
| `translationModelName()` | `translationModel(): string`. Mettez à jour les redéfinitions qui sélectionnent une classe de traduction personnalisée. |
| `getTranslationTable()` | Utilisez `translationQualifyColumn($column)` pour une colonne qualifiée ou `translationsRaw()->getRelated()->getTable()` pour le nom de la table. |

`getTranslation()`, `setTranslation()`, `hasTranslated()` et `forgetTranslation()` acceptent un code de langue sous forme de chaîne, `Locale` ou le nouveau `LocaleData`. Installez toute langue que vous passez explicitement à ces méthodes avant de les appeler. Une lecture sans langue explicite utilise la langue de repli lorsque la valeur actuelle est `null`. Avec une langue explicite, la lecture accède directement à la valeur de cette langue. Si vous appelez la méthode de bas niveau `translation()`, passez un code de langue sous forme de chaîne ou `null`. Pour convertir un DTO, utilisez `$locale->code`.

```php
$article->setTranslation('title', 'Schweizer Titel', 'de-CH')->save();
$article->translation('de_CH');
```

Avec l’alias `de_CH` → `de-CH` et l’allemand de Suisse installé, les deux appels désignent la ligne stockée sous `de_CH`. Pour renseigner plusieurs valeurs d’un champ traduit, passez un tableau indexé par langue, par exemple `['en' => 'Title', 'de-CH' => 'Schweizer Titel']`. Les listes indexées numériquement déclenchent désormais une exception `InvalidLocaleInputException`. Le code personnalisé qui renseigne la relation chargée `translations` doit fournir une collection Laravel de modèles de traduction.

Appelez `translated()`, `whereTranslation()` et les méthodes de requête associées via le générateur de requêtes Eloquent. Passez le code ou l’alias de langue comme chaîne. Pour `orderByTranslation()`, utilisez `'asc'`, `'desc'` ou `SortDirection`. Pour rechercher dans une langue autre que la langue active ou de repli avec `whereTranslation()` ou `whereTranslationLike()`, définissez `localization_models.filter` sur `false` ou interrogez directement `translationsRaw()`.

Mettez à jour les consommateurs d’événements qui appellent `$event->locale->value` ou comparent `$event->locale` à un cas de l’énumération `Locale`. `TranslationHasBeenSetEvent` et `TranslationHasBeenForgetEvent` contiennent désormais un alias sous forme de chaîne pour une langue passée explicitement. L’événement d’affectation contient `null` lorsque l’appelant omet la langue. Convertissez l’alias avec `Locales::fromAlias()` avant de l’utiliser comme clé de stockage. L’événement d’affectation se déclenche lors de l’affectation de l’attribut, avant `save()`. Pour déclencher manuellement l’un des trois événements de traduction des modèles, utilisez `event(new …)`.

Si vous conservez les fichiers d’aide générés pour l’IDE, exécutez `php artisan lang:models-helper` après avoir mis à jour les imports et les chemins de recherche de modèles. [Traductions des modèles](./models.md)

### Mettre à jour les scripts de publication et conserver les traductions personnalisées {#update-publication-scripts-and-keep-custom-translations}

| Ancienne commande ou hypothèse | Migration |
| --- | --- |
| `lang:add <locale>` mettait uniquement à jour les langues ajoutées | La commande met désormais à jour toutes les langues installées. Passez les codes explicitement dans les scripts et tenez compte des modifications des fichiers existants. |
| `lang:reset` | Utilisez `lang:update` pour une fusion. Pour régénérer les fichiers de zéro, sauvegardez les entrées personnalisées, supprimez les fichiers à régénérer, puis exécutez `lang:add` en indiquant explicitement les codes à reconstruire. |
| `lang:rm <locale> --force` | Utilisez `lang:rm <locale>`. La langue active et la langue de repli de Laravel sont protégées. |
| Vérification du seul code de sortie de `lang:rm` | Vérifiez chaque résultat. `DONE` confirme la suppression et `FAILED` indique qu’un chemin subsiste après la tentative. `PROTECTED`, `INVALID` et `SKIP` peuvent accompagner un code de sortie indiquant un succès. |

Si vous appelez Artisan via les classes de commandes, remplacez `LaravelLang\Publisher\Console\Add`, `Remove` et `Update` par `AddCommand`, `RemoveCommand` et `UpdateCommand`. Pour un décorateur de texte personnalisé, implémentez et enregistrez `LaravelLang\Publisher\Contracts\Decorator` à la place de `Contracts\TextDecorator`. Sa méthode `convert(string $locale, string $value): string` reçoit le code de langue canonique.

Exécutez le premier `lang:update` avec vos fichiers de langue existants sous contrôle de version. Le Publisher fusionne les données dans les destinations existantes et remplace les clés correspondantes, y compris les clés numériques. Gardez si possible les traductions personnalisées dans un fichier de traduction distinct de l’application. Pour gérer vous-même un fichier généré entier, ajoutez son motif de chemin dans `localization_main.skip`. Vérifiez les valeurs qui reprennent le texte source. Le Publisher utilise celui-ci lorsque les dictionnaires ne contiennent pas de traduction.

Si une mise à jour antérieure a réindexé vos fichiers `http-statuses.php`, sauvegardez et supprimez les fichiers générés concernés. Retirez `'*/http-statuses.php'` de `localization_main.skip` si vous l’aviez ajouté pour contourner le problème. Exécutez `php artisan lang:update` pour recréer les fichiers, puis restaurez vos traductions personnalisées sous les codes de statut correspondants. [Gérer les traductions](./translations.md)

### Remplacer l’ancien enregistrement des plugins {#replace-legacy-plugin-registration}

Si vous utilisez un pack de traduction tiers, passez à sa version avec manifeste. Si vous maintenez un pack, supprimez le fournisseur de services et l’entrée de détection Composer qui enregistrent un ancien `LaravelLang\Publisher\Plugins\Provider`. Si ce fournisseur configure aussi des services Laravel indépendants du pack, conservez ces enregistrements dans un fournisseur de services Laravel ordinaire.

1. Ajoutez `laravel_lang.json` à la racine du pack installé sous `vendor/`. Transformez chaque entrée de `Plugin::files()` en un chemin `source` relatif au répertoire `source/` du pack et un chemin `target` relatif au répertoire de langues de l’application. Incluez `{locale}` dans la destination.
2. Déplacez les règles de correspondance Composer dans le manifeste. Utilisez `vendor` et `version` pour une dépendance installée et une entrée distincte avec `project: true` pour un projet Composer racine. Si votre ancien plugin utilisait `$with_project_name = true` et prenait en charge les deux modes, créez les deux entrées.
3. Convertissez les dictionnaires au format `locales/<code>/<style>.json`. Les valeurs de vos fichiers sources servent de clés de recherche dans les dictionnaires. Par exemple, l’entrée PHP `'enable' => 'Enable audit trail'` crée toujours la clé d’application `audit.enable` lorsqu’elle est publiée dans `{locale}/audit.php`. Stockez sa traduction sous `"Enable audit trail"` dans le dictionnaire.
4. Depuis une copie locale du dépôt Mona, exécutez la commande Toolkit ci-dessous sur l’ancien pack. Elle crée `default.json` et `inline.json` à partir des anciens dictionnaires JSON et PHP et reprend les exclusions par langue. Examinez le résultat et ajoutez-le au nouveau pack avec le manifeste et les fichiers sources. Renommez les répertoires de dictionnaires qui portent un alias avec leur code canonique.
5. Déclarez le Publisher actuel comme dépendance dans le manifeste Composer du pack. Ajoutez également `laravel-lang/library` si votre pack utilise les traductions du dictionnaire partagé. Installez le nouveau pack dans une application et exécutez `php artisan lang:update` pour vérifier les fichiers publiés.

```bash
php src/Toolkit/bin/toolkit migrate-locales path/to/legacy-pack --target=path/to/new-pack/locales
```

Pour les applications existantes, vérifiez quelles règles des packs intégrés s’appliquent après la mise à niveau.

- Si vous avez encore besoin des traductions Laravel-Lang qui étaient publiées pour Spark Paddle, Spark Stripe, Nova Dusk Suite ou Nova Log Viewer, conservez les clés dans les fichiers de l’application ou déplacez les anciennes sources et les anciens dictionnaires dans un pack personnalisé.
- Les traductions des kits de démarrage correspondent désormais au `name` Composer du projet. Si vous avez renommé le projet ou utilisez un kit comme dépendance, ajoutez le nom Composer du kit, par exemple `laravel/livewire-starter-kit`, à `localization_main.force` pour publier ses traductions. [Plugins de traduction](./plugins.md)

### Mettre à jour le repli JSON et les outils de traduction {#update-json-fallback-and-translation-tooling}

Si le code de l’application importe ou utilise `LaravelLang\JsonFallback\Translator` dans ses déclarations de types, remplacez le type par `LaravelLang\JsonFallback\OverridedTranslator` ou utilisez le contrat de traduction de Laravel. Si vous avez créé une sous-classe de l’ancien traducteur, déplacez le comportement personnalisé dans un traducteur ou un service de l’application. Pour une clé introuvable, le traducteur de remplacement appelle la fonction de rappel de Laravel pour les traductions manquantes. Vérifiez cette fonction si votre application en enregistre une, puis testez les clés présentes dans le JSON de repli et dans le PHP de la langue active, car le JSON de repli est consulté en premier. [Repli des traductions JSON](./translations.md#json-fallback)

Si vous utilisez le paquet distinct `laravel-lang/translator` pour la traduction automatique, migrez les usages de sa façade `Translate` et les intégrations Google, DeepL ou Yandex vers le service de traduction choisi avant de retirer cette dépendance. Conservez les traductions déjà générées par ces scripts dans les dictionnaires de l’application ou du pack.

Si vous entretenez des catalogues de packs avec la commande `vendor/bin/lang` de `laravel-lang/status-generator`, exécutez les nouvelles commandes depuis une copie locale du dépôt Mona après `composer install`.

| Ancien script | Script Toolkit à utiliser |
| --- | --- |
| `vendor/bin/lang status --path=path/to/pack` | `php src/Toolkit/bin/toolkit status path/to/pack`. Consultez les rapports `locales/<code>/_status.json` générés. |
| `vendor/bin/lang sync --path=path/to/pack` | `php src/Toolkit/bin/toolkit sync:package path/to/pack`. Utilisez `sync:library` pour entretenir la bibliothèque partagée Library. |
| `vendor/bin/lang create` | `php src/Toolkit/bin/toolkit create-locale path/to/pack`. La commande crée des répertoires pour les langues connues. |
| `vendor/bin/lang download` | Indiquez l’URL du ZIP avec `--url`, la destination complète avec `--directory` et les chemins de l’archive en répétant `--paths`. |
| `vendor/bin/lang translate` | Déplacez la traduction automatique vers un service distinct. Lancez `status` dans Toolkit pour trouver les chaînes enregistrées qui correspondent encore au texte source. |

Consultez les [commandes de Toolkit](./toolkit.md) pour des exemples complets.

## Vérifier la mise à niveau {#verify-the-upgrade}

Exécutez les tests de votre application. Vérifiez un message publié dans chaque langue importante, une traduction de repli, une URL localisée avec l’alias configuré et une traduction de modèle stockée sous le code canonique. Examinez les fichiers générés et les valeurs de langue enregistrées en même temps que les changements du code source.
