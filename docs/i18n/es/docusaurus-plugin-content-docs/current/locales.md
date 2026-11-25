---
title: Configuraciones regionales y nombres
description: Encuentra configuraciones regionales compatibles e instaladas, resuelve alias y consulta nombres localizados.
---

Laravel-Lang usa códigos canónicos como `en`, `fr` y `pt_BR` en Laravel, en los nombres de archivos de traducción y en las filas de traducción de modelos. Un alias de configuración regional es un nombre que usa la aplicación, por ejemplo `pt-br` en una URL. Define cada correspondencia de código canónico a alias en [`localization_main.aliases`](./configure.md).

## Buscar configuraciones regionales compatibles e instaladas {#find-supported-and-installed-locales}

`laravel-lang/locale-list` proporciona la enumeración con valores `LaravelLang\LocaleList\Locale`. Sus casos corresponden a los [códigos canónicos compatibles](./available-locales.md).

```php
use LaravelLang\LocaleList\Locale;

Locale::French->value;        // 'fr'
Locale::tryFrom('pt_BR');     // Locale::PortugueseBrazil
Locale::values();            // arreglo de códigos canónicos
Locale::collection();        // colección de códigos canónicos
Locale::collection(false);   // colección de casos de enumeración
```

`laravel-lang/locales` agrega la fachada `LaravelLang\Locales\Facades\Locales`, que tiene en cuenta la aplicación. Estos métodos devuelven colecciones de cadenas. Pasa `false` como argumento para obtener códigos canónicos. De forma predeterminada, cada código se representa con su alias configurado, si existe.

| Método | Devuelve |
| --- | --- |
| `Locales::available()` | Todas las configuraciones regionales conocidas |
| `Locales::installed()` | Las configuraciones regionales detectadas en los archivos de idiomas de la aplicación, más las protegidas |
| `Locales::uninstalled()` | Las configuraciones regionales disponibles que no están instaladas |
| `Locales::protects()` | Las configuraciones regionales actual y de reserva efectivas, sin duplicados |

La detección lee las entradas inmediatas de `lang_path()` y los archivos JSON que coinciden con `vendor/*/*.json` respecto de esa ruta. El nombre del archivo o del directorio debe corresponder a un código compatible. Los directorios de traducciones que contienen solo PHP en `lang/vendor/<package>/` quedan fuera de estos patrones de detección. Consulta [Gestionar traducciones](./translations.md) para saber cómo publicar una configuración regional.

## Leer o establecer la configuración regional de la aplicación {#read-or-set-the-application-locale}

```php
use LaravelLang\Locales\Facades\Locales;

if (Locales::isInstalled('fr')) {
    Locales::set('fr');
}

$canonicalCode = Locales::current(false);
$displayCode = Locales::current();
```

`set()` acepta un código, un alias, un caso de la enumeración `Locale` o un objeto `LocaleData`. Si la configuración regional está instalada, establece su código canónico como la configuración regional de Laravel. Usa `isAvailable()` para comprobar si el código es compatible e `isInstalled()` cuando necesites comprobar los archivos o la protección. `isProtected()` identifica las configuraciones regionales actual y de reserva.

`current()` usa la configuración regional actual de Laravel si la reconoce y llama a `fallback()` en caso contrario. `fallback()` resuelve la configuración regional de reserva de Laravel y devuelve `en` si no la reconoce. Ambos métodos aceptan `false` como argumento para obtener códigos canónicos.

Usa `fromAlias()` para convertir un alias en un código canónico y `toAlias()` para obtener el alias de un código canónico. Estas conversiones conservan las cadenas no vacías que no reconocen. Usa `isAvailable()` al validar los datos de entrada.

## Mostrar opciones de configuración regional {#display-locale-choices}

`Locales::info('fr')` devuelve un objeto `LaravelLang\LocaleNames\LocaleData` con las propiedades `code`, `alias`, `native` y `localized`. El idioma de presentación es el de la configuración regional actual de la aplicación. Omite el argumento para consultar la configuración regional actual. Se puede consultar una configuración regional conocida antes de instalarla. Un código desconocido provoca `LaravelLang\Exceptions\UnknownLocaleException`.

Usa `Locales::data()->installed()` para obtener las configuraciones regionales instaladas como objetos `LocaleData`. `data()` ofrece también `available()`, `uninstalled()` y `protects()` para otras colecciones, y `current()` y `fallback()` para objetos individuales. Un selector de idioma puede enviar `alias` como valor y mostrar el nombre `native` como etiqueta.

## Consultar nombres de configuraciones regionales, países y monedas {#look-up-locale-country-and-currency-names}

Los paquetes `laravel-lang/locale-names`, `laravel-lang/country-names` y `laravel-lang/currency-names` proporcionan cada uno un servicio que se puede inyectar desde el contenedor de Laravel. Common incluye los tres.

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

El primer parámetro es el idioma de presentación. Usa `null` para los nombres en su idioma original. El segundo parámetro de `one()` es la configuración regional de la entrada, como `fr` en los ejemplos anteriores. En las consultas de países y monedas, el objeto devuelto incluye el código ISO. Ambos parámetros aceptan códigos canónicos, alias configurados y casos de la enumeración `Locale`.

`all($forLocale)` devuelve una colección indexada por código canónico de configuración regional. Pasa `SortDirection::Ascending` o `SortDirection::Descending` para ordenar por la propiedad `localized`. Cada valor es un objeto de solo lectura. Los valores de países y monedas también incluyen la propiedad `locale` de la entrada.
