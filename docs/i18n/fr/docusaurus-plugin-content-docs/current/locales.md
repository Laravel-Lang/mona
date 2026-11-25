---
title: "Langues et libellés"
description: "Retrouvez les langues prises en charge ou installées, résolvez leurs alias et obtenez leurs libellés traduits."
---

Laravel-Lang utilise des codes canoniques comme `en`, `fr` et `pt_BR` dans Laravel, dans les noms des fichiers de traduction et dans les lignes de traduction des modèles. Un alias de langue est un nom utilisé par l’application, par exemple `pt-br` dans une URL. Associez les codes canoniques à leurs alias dans [`localization_main.aliases`](./configure.md).

## Trouver les langues prises en charge et installées {#find-supported-and-installed-locales}

`laravel-lang/locale-list` fournit l’énumération à valeurs associées `LaravelLang\LocaleList\Locale`. Ses cas correspondent aux [codes canoniques pris en charge](./available-locales.md).

```php
use LaravelLang\LocaleList\Locale;

Locale::French->value;        // 'fr'
Locale::tryFrom('pt_BR');     // Locale::PortugueseBrazil
Locale::values();            // tableau de codes canoniques
Locale::collection();        // collection de codes canoniques
Locale::collection(false);   // collection de cas d’énumération
```

`laravel-lang/locales` ajoute la façade `LaravelLang\Locales\Facades\Locales`, qui tient compte de la configuration de l’application. Ces méthodes renvoient des collections de chaînes. Passez `false` en argument pour obtenir les codes canoniques. Par défaut, chaque code est représenté par son alias configuré, s’il en a un.

| Méthode | Valeur renvoyée |
| --- | --- |
| `Locales::available()` | Toutes les langues connues |
| `Locales::installed()` | Langues détectées dans les fichiers de langue de l’application, ainsi que les langues protégées |
| `Locales::uninstalled()` | Langues disponibles qui ne sont pas installées |
| `Locales::protects()` | Langue active et langue de repli effectives, sans doublon |

La détection examine les entrées au premier niveau de `lang_path()` et les fichiers JSON qui correspondent au motif `vendor/*/*.json` à partir de ce chemin. Le nom du fichier ou du répertoire doit correspondre à un code pris en charge. Les répertoires qui ne contiennent que des traductions PHP sous `lang/vendor/<package>/` ne correspondent pas à ces motifs. Consultez [Gérer les traductions](./translations.md) pour savoir comment publier les fichiers d’une langue.

## Lire ou définir la langue de l’application {#read-or-set-the-application-locale}

```php
use LaravelLang\Locales\Facades\Locales;

if (Locales::isInstalled('fr')) {
    Locales::set('fr');
}

$canonicalCode = Locales::current(false);
$displayCode = Locales::current();
```

`set()` accepte un code, un alias, un cas de l’énumération `Locale` ou un objet `LocaleData`. Si la langue est installée, la méthode définit le code canonique comme langue de Laravel. Utilisez `isAvailable()` pour vérifier si un code est pris en charge, et `isInstalled()` si la présence de fichiers ou la protection de la langue importe. `isProtected()` identifie la langue active et la langue de repli.

`current()` utilise la langue active de Laravel si elle est reconnue, puis recourt à `fallback()`. `fallback()` résout la langue de repli configurée dans Laravel et utilise `en` si elle est inconnue. Les deux méthodes acceptent l’argument `false` pour renvoyer les codes canoniques.

Utilisez `fromAlias()` pour obtenir le code canonique d’un alias et `toAlias()` pour obtenir l’alias d’un code canonique. Ces conversions laissent les chaînes non vides non reconnues telles quelles. Utilisez `isAvailable()` pour valider les entrées.

## Afficher les langues à sélectionner {#display-locale-choices}

`Locales::info('fr')` renvoie un objet `LaravelLang\LocaleNames\LocaleData` qui contient les propriétés `code`, `alias`, `native` et `localized`. La langue d’affichage est celle de l’application. Omettez l’argument pour obtenir la langue active. Vous pouvez interroger une langue connue avant son installation. Un code inconnu déclenche une exception `LaravelLang\Exceptions\UnknownLocaleException`.

Utilisez `Locales::data()->installed()` pour obtenir les langues installées sous forme d’objets `LocaleData`. Avec `data()`, `available()`, `uninstalled()` et `protects()` renvoient d’autres collections, tandis que `current()` et `fallback()` renvoient des objets individuels. Un sélecteur de langue peut transmettre `alias` comme valeur et afficher le nom `native` comme libellé.

## Rechercher les noms de langues, de pays et de devises {#look-up-locale-country-and-currency-names}

Les paquets `laravel-lang/locale-names`, `laravel-lang/country-names` et `laravel-lang/currency-names` fournissent chacun un service injectable depuis le conteneur Laravel. Common inclut les trois paquets.

```php
use LaravelLang\CountryNames\CountryNames;
use LaravelLang\CurrencyNames\CurrencyNames;
use LaravelLang\LocaleNames\LocaleNames;

$locale = app(LocaleNames::class)->one('de', 'fr');
$locale->localized; // 'Französisch'

$country = app(CountryNames::class)->one('de', 'fr');
$country->code;      // 'FR'
$country->localized; // 'Frankreich'

$currency = app(CurrencyNames::class)->one('de', 'fr');
$currency->code;    // 'EUR'
$currency->numeric; // 978

$nativeLocaleNames = app(LocaleNames::class)->all(null, \SortDirection::Ascending);
```

Le premier paramètre est la langue d’affichage. Passez `null` pour obtenir les noms dans leur langue d’origine. Le second paramètre de `one()` est le code de langue de l’entrée, par exemple `fr` ci-dessus. Pour les pays et les devises, le code ISO figure dans l’objet renvoyé. Les deux paramètres acceptent les codes canoniques, les alias configurés et les cas de l’énumération `Locale`.

`all($forLocale)` renvoie une collection indexée par code de langue canonique. Passez `SortDirection::Ascending` ou `SortDirection::Descending` pour trier selon la propriété `localized`. Chaque valeur est un objet en lecture seule. Pour les pays et les devises, elle contient aussi la propriété `locale` de l’entrée.
