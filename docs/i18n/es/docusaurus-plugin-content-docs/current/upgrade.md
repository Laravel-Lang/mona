---
title: Actualizar a Mona
description: Actualiza las dependencias de Laravel-Lang, la configuración, las traducciones publicadas y las integraciones de la aplicación.
---

Mona es el repositorio que publica conjuntamente los paquetes de Laravel-Lang. Sigue usando los paquetes individuales de Composer que necesite tu aplicación.

:::info[¿Por qué la versión 20?]

Laravel-Lang creció a partir de `laravel-lang/lang`. Los paquetes nacieron en distintos momentos y cada uno tenía su propia numeración y su propio ritmo de publicación. Coordinar las actualizaciones a nuevas versiones mayores nos llevaba cada vez más trabajo. Quienes usaban los paquetes tenían que averiguar qué versiones podían instalar juntas.

Creamos `laravel-lang/common` para facilitar la instalación mediante un conjunto de dependencias compatibles. Aun así, una actualización podía afectar a varios paquetes con versiones independientes.

Por ejemplo, ¿qué versiones de los paquetes habrías instalado entonces en una aplicación con Laravel 11 y PHP 8.2?

<details>
<summary>Versiones de los paquetes antes de Mona</summary>

| Paquete | Versión |
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
| Config | `1.17.1` (versión de la serie 1.x que seguían usando los demás paquetes de Laravel-Lang) y `2.3.2` (última versión estable antes de Mona, poco usada por esos paquetes) |
| Publisher | `16.9.0` |

</details>

Con el monorepositorio Mona publicamos los paquetes juntos y con un número de versión común.

Publisher tenía el número de versión más alto, así que `17.0` habría sido su siguiente versión mayor. Dedicamos mucho trabajo y cuidado a reunir el ecosistema y elegimos la versión 20 para marcar ese hito. También se puede leer como **Laravel-Lang 2.0**. 🙂

:::

:::info[Códigos y alias de configuración regional]

Laravel-Lang usa ahora códigos canónicos de configuración regional para los valores que almacena, entre ellos las rutas de idiomas publicadas y las filas de traducción de modelos. Las configuraciones regionales activa y de reserva de Laravel también usan códigos canónicos. En tiempo de ejecución, usa alias en los segmentos de URL y los selectores de idioma. Los métodos de fachada que devuelven códigos de configuración regional usan de forma predeterminada el alias configurado o, si no existe, el código canónico. Por ejemplo, configura `'pt_BR' => 'pt-br'` en `localization_main.aliases` para conservar `/pt-br/account` mientras publicas en `lang/pt_BR.json` y almacenas `pt_BR` en la base de datos.

:::

## Actualizar dependencias {#updating-dependencies}

La versión actual es compatible con Laravel 12 y 13 y requiere PHP `^8.3`. En `composer.json` de tu aplicación, establece la restricción `^20.0` para cada paquete de la siguiente tabla que figure en `require` o `require-dev`. Si solo tienes instalado `laravel-lang/common`, ese es el único requisito que necesitas cambiar.

| Uso | Paquetes de Composer |
| --- | --- |
| Paquete conjunto | `laravel-lang/common` |
| Paquetes de traducción | `laravel-lang/lang`, `laravel-lang/starter-kits`, `laravel-lang/moonshine`, `laravel-lang/actions`, `laravel-lang/attributes`, `laravel-lang/http-statuses` |
| Publicación y configuración | `laravel-lang/publisher`, `laravel-lang/config`, `laravel-lang/json-fallback` |
| Integraciones con la aplicación | `laravel-lang/locales`, `laravel-lang/locale-list`, `laravel-lang/routes`, `laravel-lang/models` |
| Plantilla de paquete | `laravel-lang/translations-template` |

Si tu instalación solo usa el flujo de trabajo de traducción estándar, actualiza las dependencias y vuelve a publicar las traducciones.

```bash
composer update
php artisan lang:update
```

Si registraste proveedores, publicaste ajustes personalizados, usas alias o paquetes de traducción de terceros, o llamas a las API de los paquetes en tu aplicación, sigue las secciones que correspondan antes de volver a publicar.

## Sustituir dependencias renombradas u obsoletas {#replace-renamed-or-retired-dependencies}

Actualiza las dependencias directas de los paquetes de nombres anteriores en `composer.json` antes de ejecutar `composer update`.

| Paquete de Composer anterior | Dependencia con versión `^20.0` |
| --- | --- |
| `laravel-lang/native-country-names` | `laravel-lang/country-names` |
| `laravel-lang/native-currency-names` | `laravel-lang/currency-names` |
| `laravel-lang/native-locale-names` | `laravel-lang/locale-names` |

Common ya depende de los tres paquetes de nombres. Actualiza también el código PHP. Consulta [Sustituir las API de consulta de nombres](#replace-the-name-lookup-apis).

Comprueba que cada complemento de terceros tenga una versión compatible con el nuevo Publisher. Migra cualquier uso de `laravel-lang/translator` o `laravel-lang/status-generator` antes de retirar estas dependencias. Consulta [Herramientas de traducción](#update-json-fallback-and-translation-tooling).

## Actualizar proveedores de servicios {#update-service-providers}

Laravel detecta los proveedores de Config, Publisher, Routes y Models a partir de los manifiestos de sus paquetes. Si usas la detección automática, actualiza el código o las pruebas de la aplicación que hagan referencia a las clases de proveedor anteriores. Si tu aplicación registra los paquetes manualmente, aplica estos cambios en la misma lista de proveedores, como `bootstrap/providers.php` o `config/app.php`.

- `LaravelLang\Config\ServiceProvider` → `LaravelLang\Config\LangConfigServiceProvider`
- `LaravelLang\Publisher\ServiceProvider` → `LaravelLang\Publisher\LangPublisherServiceProvider`
- `LaravelLang\Routes\ServiceProvider` → `LaravelLang\Routes\LangRoutesServiceProvider`
- `LaravelLang\Models\ServiceProvider` → `LaravelLang\Models\LangModelsServiceProvider`

Elimina los registros manuales de `LaravelLang\Locales\LaravelLangLocalesServiceProvider` y de las clases `ServiceProvider` anteriores de `LaravelLang\Actions`, `LaravelLang\Attributes`, `LaravelLang\HttpStatuses`, `LaravelLang\Lang`, `LaravelLang\MoonShine` y `LaravelLang\StarterKits`. Estos paquetes de traducción se detectan a partir de sus manifiestos.

Si usas JSON de reserva, reemplaza `LaravelLang\JsonFallback\TranslationServiceProvider` por `LaravelLang\JsonFallback\LangJsonFallbackServiceProvider` en el lugar donde lo registraste. Así se mantiene la sustitución del proveedor de traducción de Laravel.

Actualiza las referencias obsoletas a proveedores antes de ejecutar `composer update` para que la detección de paquetes pueda iniciar la aplicación. Si un script de Composer aún falla porque carga una clase anterior, ejecuta `composer update --no-scripts`, actualiza las referencias y ejecuta `composer dump-autoload` para repetir la detección.

## Migrar la configuración {#migrate-configuration}

Si personalizaste `config/localization.php`, consérvalo como referencia mientras trasladas tus ajustes. Los paquetes cargan los nuevos valores predeterminados de forma automática. Publica los [archivos de configuración](./configure.md) que necesites editar. La etiqueta `localization` publica los cinco archivos. Para publicar uno solo, usa su nombre sin la extensión `.php` como etiqueta.

```bash
php artisan vendor:publish --tag=localization
```

### Alias, estilos de traducción y formato de archivos {#aliases-translation-styles-and-file-formatting}

- Copia el mapa `aliases` a `config/localization_main.php`. Conserva los códigos canónicos como claves y los alias como valores, por ejemplo `'de_CH' => 'de-CH'`.
- Reemplaza `inline` por `styles` en `config/localization_main.php`. Usa `'styles' => ['default']` para mantener el comportamiento predeterminado anterior con mensajes de validación estándar. Si usabas mensajes en línea, mantén `['default', 'inline']`, el nuevo valor predeterminado. Quita `LOCALIZATION_INLINE` y `LANG_PUBLISHER_INLINE` de los entornos donde las configuraste.
- Quita la clave `align` y las variables de entorno `LOCALIZATION_ALIGN` y `LANG_PUBLISHER_ALIGN`. Para alinear los operadores `=>` de los arreglos PHP publicados, configura el formateador PHP de tu proyecto y ejecútalo sobre los archivos de idiomas después de publicarlos. Por ejemplo, la regla `binary_operator_spaces` de Laravel Pint puede aplicar `align_single_space_minimal` a `=>`.

### Puntuación tipográfica {#smart-punctuation}

Si habilitaste la puntuación tipográfica, cambia `LOCALIZATION_SMART_ENABLED` por `LOCALIZATION_PUNCTUATION_ENABLED` y conserva el valor `true`. Si configuraste `smart_punctuation.enable` directamente en PHP, mueve el valor booleano a la clave `enabled` de primer nivel en `config/localization_punctuation.php`. La puntuación tipográfica está deshabilitada de forma predeterminada.

Mueve las comillas personalizadas de `smart_punctuation.common` y `smart_punctuation.locales` a los arreglos `common` y `locales` de primer nivel en ese archivo. Usa códigos canónicos como claves de `locales`. Las comillas de cada configuración regional tienen prioridad sobre las claves correspondientes de `common`. Si copiaste un ejemplo antiguo del francés que usa `&nbsp;`, usa un espacio de no separación real o `"\u{00A0}"` en las cadenas de comillas.

### Rutas localizadas {#localized-routes}

Mueve tus ajustes de `routes` de la configuración anterior a `config/localization_routes.php`.

| Ajuste personalizado | Cambio en `config/localization_routes.php` | Variable de entorno que se debe renombrar |
| --- | --- | --- |
| Parámetro de configuración regional, encabezado, cookie, clave de sesión o columna del modelo de usuario | Copia los ajustes de `names` y conserva las demás entradas predeterminadas. | |
| Prefijo de los nombres de rutas localizadas | Mueve `name_prefix` a `prefix`. Si el valor está vacío, se usa `localized.`. | `LOCALIZATION_NAME_PREFIX` → `LOCALIZATION_PREFIX` |
| Redirecciones desde la ruta con prefijo | Mueve `redirect_default` a `redirect`. | `LOCALIZATION_REDIRECT_DEFAULT` → `LOCALIZATION_REDIRECT` |
| Si la función de URL omite la configuración regional de reserva | Mueve `hide_default` a `hide`. | `LOCALIZATION_HIDE_DEFAULT` → `LOCALIZATION_HIDE` |
| Middleware aplicado a los grupos de rutas localizadas | Mueve `group.middlewares.default` a `middlewares.default` para las rutas sin prefijo y `group.middlewares.prefix` a `middlewares.prefix` para las rutas con prefijo. Actualiza los nombres de clase de middleware como se indica en [la migración de rutas](#update-localized-routes-and-middleware). | |

### Traducciones de modelos {#model-translations}

Mueve tus ajustes de `models` a `config/localization_models.php`. Copia los valores de `suffix` y `helpers` personalizados al nivel superior. Usa un valor booleano `filter` en lugar de la clave anterior `filter.enabled`. Si controlas el filtrado y la ruta de los archivos de ayuda del IDE mediante variables de entorno, conserva `LOCALIZATION_FILTER_ENABLED` y `VENDOR_PATH`.

Los comandos de modelos ahora buscan en `app/Models` de forma predeterminada. Si dependías de la búsqueda anterior en todo `app/` o configuraste `localization-private.models.directory`, agrega cada directorio en el que quieras buscar al arreglo `directories`. Por ejemplo, configura `'directories' => [app_path()]` para mantener la búsqueda anterior.

### Configuración que usa el código de la aplicación {#configuration-used-by-application-code}

- Sustituye las lecturas y escrituras mediante `LaravelLang\Config\Facades\Config` por llamadas a la configuración de Laravel sobre las claves nuevas. Por ejemplo, cambia `Config::shared()->models->filter->enabled` por `config('localization_models.filter')`. Si prefieres una lectura con tipos, inyecta `LaravelLang\Config\Services\ModelLangConfig` y lee su propiedad `filter`. Para los ajustes de rutas, puedes inyectar `RouteLangConfig` del mismo espacio de nombres.
- Si tu código lee metadatos de configuración regional desde `localization-private.map`, usa `localization_map.locales`. Copia los ajustes al arreglo `locales` de `config/localization_map.php`.
- Sustituye el registro mediante `localization-private.plugins` y `localization-private.packages` por un [manifiesto de complemento](./plugins.md).
- Elimina el bloque anterior `translators` y los ajustes `TRANSLATION_*` antiguos que solo usaba `laravel-lang/translator`.

Después de trasladar los ajustes y actualizar las referencias de la aplicación, elimina `config/localization.php` y ejecuta `php artisan config:clear`.

## Mover los archivos de idiomas a rutas canónicas {#move-language-files-to-canonical-paths}

1. Establece códigos canónicos en `APP_LOCALE` y `APP_FALLBACK_LOCALE`. Mueve tus alias a `localization_main.aliases` para mantener las URL y las entradas de configuración regional.
2. Cambia los nombres de los archivos y directorios con alias, como `lang/pt-br.json` y `lang/pt-br/`, por `lang/pt_BR.json` y `lang/pt_BR/`. Busca los mismos alias en `lang/vendor/<package>/`. Si existen ambos nombres, combina el contenido y resuelve los conflictos de las traducciones personalizadas antes de quitar la ruta con alias.
3. Instala cada configuración regional que solo figure en traducciones PHP de dependencias con `php artisan lang:add <locale>`. La lista de configuraciones regionales instaladas usa las entradas que están directamente en el directorio de idiomas, los archivos JSON de `lang/vendor/<package>/` y las configuraciones regionales actual y de reserva efectivas.
4. Ejecuta `php artisan lang:update` después de migrar la configuración y los complementos y revisa el diff. Publisher puede conservar claves de integraciones que ya no publican un archivo. Conserva las claves que siga usando la aplicación y, antes de eliminar las obsoletas, comprueba qué las usa. Consulta [Scripts de publicación y traducciones personalizadas](#update-publication-scripts-and-keep-custom-translations) para regenerar o conservar archivos.

## Actualizar integraciones {#update-integrations}

### Locales {#locales}

Conserva la importación de `LaravelLang\Locales\Facades\Locales` y actualiza las llamadas según los datos que use cada código que las invoque.

| Llamada anterior | Sustitución |
| --- | --- |
| `Locales::available()`, `installed()` o `protects()` con objetos como resultado | Llama al mismo método en `Locales::data()`. |
| `Locales::raw()->available()`, `installed()` o `protects()` | Llama al mismo método directamente en `Locales` para obtener una colección de cadenas con alias. Pasa `false` para obtener códigos canónicos y llama a `->all()` si necesitas un arreglo. |
| `Locales::installed(false)` o `Locales::raw()->installed(false)` | `false` ahora selecciona códigos canónicos. Las configuraciones regionales actual y de reserva se incluyen aunque no haya archivos de idioma. Sustituye también el argumento con nombre anterior `withProtects: false`. Usa `Locales::installed(false)->diff(Locales::protects(false))->values()` para excluirlas. Para objetos, usa `Locales::data()->installed()->reject(fn ($locale) => Locales::isProtected($locale))->values()`. |
| `Locales::notInstalled()` o `Locales::raw()->notInstalled()` | Usa `Locales::data()->uninstalled()` para obtener objetos o `Locales::uninstalled()` para cadenas con alias. Pasa `false` para obtener cadenas canónicas. |
| `Locales::getCurrent()` o `Locales::getDefault()` | Usa `Locales::data()->current()` para un DTO o `Locales::current()` para una cadena. Reemplaza `Locales::raw()->getDefault()` por `Locales::current()`. Si usabas `Locales::raw()->getCurrent()` para obtener el valor de Laravel, usa `app()->getLocale()`. |
| `Locales::getFallback()` o `Locales::raw()->getFallback()` | Usa `Locales::data()->fallback()` para un DTO o `Locales::fallback()` para una cadena. Una configuración regional de reserva desconocida ahora se resuelve al inglés. Una configuración regional activa desconocida se resuelve a la de reserva efectiva. |
| `Locales::get($locale)` o `Locales::raw()->get($locale)` | Usa `isInstalled()` y elige explícitamente la configuración regional de reserva. Si la configuración regional es compatible pero puede no estar instalada, usa `isAvailable()` seguido de `info()`. |
| `Locales::info($locale)` o `Locales::raw()->info($locale)` | Usa `Locales::info($locale)` y lee `->alias` o `->code` cuando necesites una cadena. Pasa `null` para la configuración regional actual. Para códigos desconocidos o vacíos, maneja `LaravelLang\Exceptions\UnknownLocaleException` o comprueba `isAvailable()` antes de consultarlos. |

Para conservar el comportamiento anterior de `get()`, que recurría a la configuración regional actual con entradas no instaladas o no válidas, toma esa decisión en el código que lo llama.

```php
use LaravelLang\Locales\Facades\Locales;

$locale = Locales::acceptsInput($requested) && Locales::isInstalled($requested)
    ? Locales::info($requested)
    : Locales::data()->current();

$code = $locale->code;   // Úsalo en rutas, filas de base de datos y otros datos persistentes.
$alias = $locale->alias; // Úsalo en URL y selectores de idioma.
```

Actualiza los manejadores de solicitudes que antes pasaban el resultado de `raw()->get()` a `app()->setLocale()`. Usa `Locales::set($locale)` después de aceptar el valor de la solicitud. Acepta códigos de configuraciones regionales instaladas, alias configurados, casos de enumeración y el DTO nuevo, y asigna a Laravel el código canónico. Para una solicitud con valor `pt-br`, Laravel recibe `pt_BR` y `Locales::current()` devuelve `pt-br`. Llama a `Locales::current(false)` o `Locales::fallback(false)` donde necesites códigos canónicos.

Para guardar el valor en otros lugares de tu aplicación, valídalo con `isAvailable()` o `isInstalled()` y luego usa `fromAlias()` para resolver su código canónico. Usa `toAlias()` para dar formato a un código canónico antes de mostrarlo. Sustituye el uso de `Services\Resolver` por `acceptsInput()` para comprobar los tipos de PHP aceptados o por `normalize()` para extraer una cadena de un valor con tipo. `normalize()` conserva las cadenas tal como se suministran, incluido un alias como `pt-br`.

Sustituye `LaravelLang\Locales\Data\LocaleData` por `LaravelLang\LocaleNames\LocaleData` en las importaciones, declaraciones de tipos y pruebas. El nuevo objeto de solo lectura tiene las propiedades de cadena `code`, `alias`, `native` y `localized`. Para las URL y los valores de interfaz, usa `alias` en los lugares donde usabas `code`. Usa `Locale::from($data->code)` donde antes leías `$data->locale` e importa `LaravelLang\LocaleList\Locale`. Si tus plantillas o formateadores los usan, lee `type`, `regional` y `direction` desde `config("localization_map.locales.{$data->code}", [])`. Define tus valores predeterminados para las entradas ausentes. Para mantener los anteriores, usa `Latn` para la escritura, el código canónico para la región y `Direction::LeftToRight` para la dirección.

Elimina los indicadores `withCountry`, `withCurrency`, `withCountries` y `withCurrencies`. Consulta explícitamente el país y la moneda de la fila de la configuración regional. Cambia las importaciones de los tipos anteriores `LaravelLang\Locales\Data\CountryData` y `CurrencyData` por las clases de datos de los paquetes de nombres correspondientes.

```php
use LaravelLang\CountryNames\CountryNames;
use LaravelLang\CurrencyNames\CurrencyNames;
use LaravelLang\Locales\Facades\Locales;

$locale = Locales::info('pt_BR');
$displayLanguage = Locales::current(false);

$country = app(CountryNames::class)->one($displayLanguage, $locale->code);
$currency = app(CurrencyNames::class)->one($displayLanguage, $locale->code);
```

Si llamabas a `localizedLocales()`, `localizedCountries()` o `localizedCurrencies()` en el servicio anterior, usa `all(null)` y `all($displayLanguage)` en el servicio de nombres correspondiente para obtener los valores en el idioma original y los localizados, respectivamente. Sustituye la inyección directa de las clases anteriores `Services\Locales` o `Services\RawLocales` por la fachada o por `Services\MainService` y `Services\DataService` según corresponda. Consulta [Configuraciones regionales y nombres](./locales.md) para conocer las nuevas API de servicio.

#### Funciones auxiliares de la enumeración de configuración regional {#locale-enum-helpers}

Los casos de las enumeraciones `Locale` y `Direction` conservan sus valores. Al actualizar listas o selectores de configuración regional personalizados, sustituye las funciones auxiliares eliminadas de `archtechx/enums`.

| Función auxiliar anterior | Sustitución |
| --- | --- |
| `names()` | `array_column(Locale::cases(), 'name')` |
| `options()` | Genera el mapa de nombres a valores con `array_column(Locale::cases(), 'value', 'name')`. |
| `stringOptions()` | Genera las opciones en la vista a partir de `Locale::cases()`. Usa el valor del caso para obtener un código canónico o `Locales::toAlias($case)` para un alias de presentación. |
| `fromName()` o `tryFromName()` | Busca el caso por su `name` en `Locale::cases()` y maneja el caso en que no haya coincidencia. Para un valor asociado como `pt_BR`, usa `Locale::from()` o `Locale::tryFrom()`. |
| `Locale::onlyValues()` | Genera el mapa de valores a valores con `array_column(Locale::cases(), 'value', 'value')`. Para una lista de valores, usa `Locale::values()`. |
| `Direction::values()` | Usa `array_column(Direction::cases(), 'value')`. |

### Sustituir las API de consulta de nombres {#replace-the-name-lookup-apis}

Cambia las importaciones de `LaravelLang\NativeCountryNames`, `LaravelLang\NativeCurrencyNames` y `LaravelLang\NativeLocaleNames` por `LaravelLang\CountryNames`, `LaravelLang\CurrencyNames` y `LaravelLang\LocaleNames`. Obtén `CountryNames`, `CurrencyNames` o `LocaleNames` mediante el contenedor de Laravel o inyecta el servicio en tu clase.

| Uso anterior | Sustitución |
| --- | --- |
| `SomeNames::get($displayLocale)` | `$names->all($displayLocale)` |
| `SomeNames::get()` para nombres en su idioma original | `$names->all(null)` |
| `SortBy::None` | Omite el argumento de ordenación. |
| `SortBy::Key` | Llama a `->sortKeys()` en la colección devuelta. |
| `SortBy::Value` | Pasa `SortDirection::Ascending` a `all()` para ordenar por nombre localizado. |

Usa `one($displayLocale, $entryLocale)` para obtener una entrada conocida. La clave de búsqueda es una configuración regional como `fr`. Por ejemplo, `app(CountryNames::class)->one('de', 'fr')` devuelve el país para el francés, con su nombre en alemán y `code` igual a `FR`. Los DTO de países y monedas se encuentran en el espacio de nombres raíz de su paquete y exponen la clave de búsqueda en `locale`. Sus campos `code` siguen conteniendo el código de país o moneda.

Los tres servicios devuelven colecciones indexadas por configuración regional canónica. Si usabas el arreglo de cadenas del paquete anterior de nombres de configuraciones regionales, toma la propiedad `localized` de cada DTO. Si llamabas a `toArray()` en los DTO de país o moneda anteriores o cambiabas sus propiedades, construye explícitamente el arreglo de la aplicación. Los nuevos DTO son de solo lectura.

```php
use LaravelLang\LocaleNames\LocaleData;
use LaravelLang\LocaleNames\LocaleNames;

$options = app(LocaleNames::class)
    ->all('de', SortDirection::Ascending)
    ->map(fn (LocaleData $locale): string => $locale->localized)
    ->all();
```

Valida las configuraciones regionales elegidas por el usuario contra el catálogo correspondiente antes de llamar a `one()`. Una configuración regional de presentación desconocida puede hacer que `all()` devuelva una lista vacía. Pasa `null` cuando la aplicación necesite nombres en su idioma original. Si tu código usaba una enumeración personalizada con valores asociados, pasa su valor de cadena. [Configuraciones regionales y nombres](./locales.md)

### Actualizar las rutas localizadas y el middleware {#update-localized-routes-and-middleware}

Importa la función auxiliar con espacio de nombres en los archivos PHP y las plantillas Blade que generan URL localizadas. También puedes llamar directamente a `\LaravelLang\Routes\localizedRoute()`.

```php
use LaravelLang\Locales\Facades\Locales;

use function LaravelLang\Routes\localizedRoute;

$url = localizedRoute('products.show', [
    'product' => 42,
    'locale' => Locales::current(),
]);
```

`Route::localizedGroup()` y `LocalizationRoute::group()` registran tanto rutas sin prefijo como rutas con un prefijo de configuración regional. Conserva el grupo existente y revisa las llamadas a la función auxiliar de URL. Esta usa el alias de reserva cuando se omite el parámetro de configuración regional. Para un selector de idioma, pasa explícitamente el alias deseado con `Locales::toAlias($locale)` o `$localeData->alias`. La cadena de configuración regional que se indique se usa tal cual. Si cambiaste el nombre del parámetro, usa la misma clave en el arreglo que en `localization_routes.names.parameter`.

Reemplaza las importaciones y los registros en tus rutas, `bootstrap/app.php` y las listas de middleware personalizadas. Todas las clases de esta tabla están en `LaravelLang\Routes\Middlewares`.

| Middleware anterior | Sustitución |
| --- | --- |
| `LocalizationByCookie` | `LocalizationByCookieMiddleware` |
| `LocalizationByHeader` | `LocalizationByHeaderMiddleware` |
| `LocalizationByModel` | `LocalizationByModelMiddleware` |
| `LocalizationBySession` | `LocalizationBySessionMiddleware` |
| `LocalizationByParameter` | `LocalizationByParameterMiddleware` |
| `LocalizationByParameterWithRedirect` | `LocalizationByParameterWithRedirectMiddleware` |
| `LocalizationByParameterPrefix` | `LocalizationByParameterPrefixMiddlewareMiddleware` |

Si construías el middleware de modelos con un guard, pásalo como parámetro de middleware de Laravel, por ejemplo `LocalizationByModelMiddleware::class . ':admin'`. Su función de resolución de usuario espera un modelo de Eloquent. En subclases de middleware personalizadas, inyecta `LaravelLang\Config\Services\RouteLangConfig`, lee los nombres de las fuentes de `$this->routeConfig->name` y devuelve `int|string|null` desde `detect()`. Reemplaza las referencias directas a `LaravelLang\Routes\Services\Route` por `LaravelLang\Routes\Services\RouteService`.

Prueba cada fuente que use la aplicación. La detección por encabezado ahora usa PHP Intl y toma el campo de idioma extraído de `Accept-Language`. Si dependías del orden de las preferencias del navegador o de coincidencias regionales, implementa esa selección en un middleware personalizado. Un middleware posterior puede sustituir una fuente anterior, incluida la configuración regional de la URL. Reordena las listas configuradas si la URL debe tener prioridad.

Prueba por separado la generación de URL y las solicitudes. `localization_routes.hide` oculta el segmento de configuración regional de reserva al generar una URL. `localization_routes.redirect` redirige las solicitudes con prefijo de la configuración regional actual a la ruta sin prefijo. Una ruta con nombre y prefijo también redirige las configuraciones regionales no válidas o no instaladas a la ruta sin prefijo.

En los escuchadores de `LocaleHasBeenSetEvent`, usa `$event->locale->code` para obtener el código canónico y `$event->locale->alias` para mostrarlo. La carga del evento ahora es `LaravelLang\LocaleNames\LocaleData`. El middleware envía el evento cuando cambia la configuración regional elegida y guarda su código canónico en la sesión. Sustituye las llamadas manuales a `LocaleHasBeenSetEvent::dispatch($data)` por `event(new LocaleHasBeenSetEvent($data))`. [Rutas localizadas](./routes.md)

### Migrar el código y los datos de traducción de modelos {#migrate-model-translation-code-and-data}

Revisa la columna `locale` de cada tabla de traducciones para detectar filas con alias. Para cada alias, actualiza esas filas a su código canónico en una migración de datos. Antes de actualizar, comprueba si el mismo `item_id` ya tiene una fila para el código canónico. Combina en una sola fila los valores que quieras conservar para cumplir la restricción de unicidad `(item_id, locale)`. Algunas versiones del trait anterior ya escribían códigos canónicos, por lo que debes basar esta migración en los valores que realmente están en tus tablas.

Actualiza las importaciones y las clases generadas.

| Uso anterior | Sustitución |
| --- | --- |
| `LaravelLang\Locales\Data\LocaleData` | `LaravelLang\LocaleNames\LocaleData` |
| `LaravelLang\Models\Casts\TrimCast` | `LaravelLang\Models\Casts\TrimStringCast` |
| `LaravelLang\Models\Exceptions\UnavailableLocaleException` | `LaravelLang\Exceptions\UnknownLocaleException` |
| `LaravelLang\Models\Exceptions\AttributeIsNotTranslatableException` o `UnknownModelPathException` | El mismo nombre de clase en `LaravelLang\Exceptions`. |
| `translationModelName()` | `translationModel(): string`. Actualiza las redefiniciones que seleccionan una clase de traducción personalizada. |
| `getTranslationTable()` | Usa `translationQualifyColumn($column)` para una columna calificada o `translationsRaw()->getRelated()->getTable()` para el nombre de la tabla. |

`getTranslation()`, `setTranslation()`, `hasTranslated()` y `forgetTranslation()` aceptan una cadena de configuración regional, `Locale` o el nuevo `LocaleData`. Instala cualquier configuración regional explícita que usen estos métodos antes de llamarlos. Si lees sin indicar una configuración regional, se usa la de reserva cuando el valor actual es `null`. Una configuración regional explícita lee su valor directamente. Si llamas al método de nivel inferior `translation()`, pasa una cadena de configuración regional o `null`. Convierte un DTO con `$locale->code`.

```php
$article->setTranslation('title', 'Schweizer Titel', 'de-CH')->save();
$article->translation('de_CH');
```

Con un alias `de_CH` → `de-CH` y el alemán de Suiza instalado, ambas llamadas apuntan a la fila almacenada como `de_CH`. Para asignar varios valores a un campo traducido, pasa un mapa indexado por configuración regional, como `['en' => 'Title', 'de-CH' => 'Schweizer Titel']`. Las listas con índices ahora producen `InvalidLocaleInputException`. El código personalizado que establezca la relación cargada `translations` debe proporcionar una colección de Laravel con modelos de traducción.

Llama a `translated()`, `whereTranslation()` y otros ámbitos relacionados mediante el generador de consultas de Eloquent. Pasa un código o alias como cadena de configuración regional al ámbito. Para `orderByTranslation()`, usa `'asc'`, `'desc'` o `SortDirection`. Si consultas una configuración regional distinta de la actual o de reserva mediante `whereTranslation()` o `whereTranslationLike()`, establece `localization_models.filter` en `false` o consulta `translationsRaw()` directamente.

Actualiza los consumidores de eventos que llaman a `$event->locale->value` o comparan `$event->locale` con una enumeración `Locale`. `TranslationHasBeenSetEvent` y `TranslationHasBeenForgetEvent` ahora contienen un alias de cadena cuando se indica una configuración regional. Ambos eventos contienen `null` si se omite. Convierte el alias con `Locales::fromAlias()` antes de usarlo como clave persistente. El evento de asignación se ejecuta al asignar el atributo, antes de `save()`. Para emitir manualmente cualquiera de los tres eventos de traducción de modelos, usa `event(new …)`.

Si conservas los archivos de ayuda generados para el IDE, ejecuta `php artisan lang:models-helper` después de actualizar las importaciones y las rutas de detección de modelos. [Traducciones de modelos](./models.md)

### Actualizar scripts de publicación y conservar traducciones personalizadas {#update-publication-scripts-and-keep-custom-translations}

| Comando o supuesto anterior | Migración |
| --- | --- |
| `lang:add <locale>` solo actualizaba las configuraciones regionales que se agregaban | Ahora actualiza todas las configuraciones regionales instaladas. Pasa los códigos explícitamente en los scripts y ten en cuenta los cambios en archivos de idiomas existentes. |
| `lang:reset` | Usa `lang:update` para combinar. Para regenerar desde cero, haz una copia de las entradas personalizadas, elimina los archivos que quieras regenerar y ejecuta `lang:add` con los códigos explícitos de las configuraciones regionales que quieras reconstruir. |
| `lang:rm <locale> --force` | Usa `lang:rm <locale>`. Las configuraciones regionales actual y de reserva de Laravel están protegidas. |
| Comprobar solo el código de salida de `lang:rm` | Comprueba cada resultado. `DONE` confirma la eliminación y `FAILED` indica que todavía queda una ruta después del intento. `PROTECTED`, `INVALID` y `SKIP` pueden aparecer con un código de salida de éxito. |

Si llamas a Artisan por clase de comando, sustituye `LaravelLang\Publisher\Console\Add`, `Remove` y `Update` por `AddCommand`, `RemoveCommand` y `UpdateCommand`. Para un decorador de texto personalizado, implementa y vincula `LaravelLang\Publisher\Contracts\Decorator` en lugar de `Contracts\TextDecorator`. Su método `convert(string $locale, string $value): string` recibe la configuración regional canónica.

Ejecuta el primer `lang:update` con los archivos de idiomas existentes bajo control de versiones. Publisher combina los archivos de destino existentes y reemplaza las claves coincidentes, incluidas las numéricas. Siempre que puedas, conserva las traducciones personalizadas en un archivo de traducción separado de la aplicación. Si quieres mantener por tu cuenta un destino generado completo, agrega el patrón de su ruta a `localization_main.skip`. Comprueba los valores que regresen a la frase original. Publisher usa esa frase cuando falta una traducción en los diccionarios.

Si una actualización anterior cambió los índices de los archivos `http-statuses.php`, haz una copia de seguridad de los archivos generados afectados y elimínalos. Quita `'*/http-statuses.php'` de `localization_main.skip` si lo agregaste para evitar el problema. Ejecuta `php artisan lang:update` para regenerar los archivos y restaura las traducciones personalizadas con sus códigos de estado correspondientes. [Gestionar traducciones](./translations.md)

### Sustituir el registro anterior de complementos {#replace-legacy-plugin-registration}

Si usas un paquete de traducción de terceros, actualízalo a la versión basada en manifiesto. Si mantienes un paquete, elimina el proveedor y la entrada de detección de Composer que registran un `LaravelLang\Publisher\Plugins\Provider` anterior. Si ese proveedor también configura otros servicios de Laravel, conserva esos registros en un proveedor de servicios normal de Laravel.

1. Agrega `laravel_lang.json` en la raíz del paquete instalado en `vendor/`. Convierte cada entrada de `Plugin::files()` en una ruta `source` relativa al directorio `source/` del paquete y una ruta `target` relativa al directorio de idiomas de la aplicación. Usa `{locale}` en el destino.
2. Mueve las reglas de coincidencia de Composer al manifiesto. Usa `vendor` y `version` para una dependencia instalada y una entrada separada con `project: true` para un proyecto raíz de Composer. Si tu complemento anterior usaba `$with_project_name = true` y admitía los dos modos, crea ambas entradas.
3. Convierte los diccionarios a `locales/<code>/<style>.json`. Los valores de tus archivos fuente son las claves de búsqueda del diccionario. Por ejemplo, una entrada PHP `'enable' => 'Enable audit trail'` sigue creando la clave `audit.enable` de la aplicación cuando se publica en `{locale}/audit.php`. Guarda su traducción bajo `"Enable audit trail"` en el diccionario.
4. Desde una copia local de Mona, ejecuta el siguiente comando de Toolkit sobre el paquete anterior. El comando crea `default.json` e `inline.json` a partir de los diccionarios JSON y PHP antiguos y conserva las exclusiones por configuración regional. Revisa los resultados y agrégalos al paquete nuevo junto con el manifiesto y los archivos fuente. Cambia los nombres de los directorios de diccionarios basados en alias a códigos canónicos.
5. Declara el Publisher actual como dependencia en el manifiesto de Composer del paquete. Agrega también `laravel-lang/library` si el paquete necesita traducciones del diccionario compartido. Instala el paquete resultante en una aplicación y ejecuta `php artisan lang:update` para comprobar los archivos que publica.

```bash
php src/Toolkit/bin/toolkit migrate-locales path/to/legacy-pack --target=path/to/new-pack/locales
```

En aplicaciones existentes, comprueba qué reglas de los paquetes integrados se aplican después de actualizar.

- Si aún necesitas las traducciones de Laravel-Lang que antes se publicaban para Spark Paddle, Spark Stripe, Nova Dusk Suite o Nova Log Viewer, conserva esas claves en archivos de la aplicación o mueve los archivos fuente y los diccionarios anteriores a un paquete personalizado.
- Las traducciones de los kits de inicio ahora se seleccionan por el valor `name` de Composer del proyecto. Si cambiaste el nombre del proyecto o usas un kit como dependencia, agrega el nombre de Composer del kit, por ejemplo `laravel/livewire-starter-kit`, a `localization_main.force` para publicar sus traducciones. [Complementos de traducción](./plugins.md)

### Actualizar el JSON de reserva y las herramientas de traducción {#update-json-fallback-and-translation-tooling}

Si el código de la aplicación importa o usa `LaravelLang\JsonFallback\Translator` en una declaración de tipo, cambia el tipo a `LaravelLang\JsonFallback\OverridedTranslator` o usa el contrato de traducción de Laravel. Si creaste una subclase del traductor anterior, mueve el comportamiento personalizado a un traductor de la aplicación o a un servicio de traducción propio. Para las claves que no puede resolver, el traductor sustituto llama a la función de Laravel para traducciones ausentes. Revisa esa función si tu aplicación registra una y prueba las claves que existan tanto en el JSON de reserva como en el PHP del idioma actual, porque se comprueba primero el JSON de reserva. [JSON de reserva](./translations.md#json-fallback)

Si usas el paquete separado `laravel-lang/translator` para traducción automática, migra el uso de su fachada `Translate` y las integraciones con Google, DeepL o Yandex al servicio de traducción que elijas antes de eliminar esa dependencia. Conserva en tu aplicación o en los diccionarios del paquete las traducciones que ya generaron esos scripts.

Si mantienes catálogos de paquetes con `vendor/bin/lang` de `laravel-lang/status-generator`, ejecuta los comandos nuevos desde una copia local de Mona después de `composer install`.

| Script anterior | Script de Toolkit que lo sustituye |
| --- | --- |
| `vendor/bin/lang status --path=path/to/pack` | `php src/Toolkit/bin/toolkit status path/to/pack`. Lee los informes generados en `locales/<code>/_status.json`. |
| `vendor/bin/lang sync --path=path/to/pack` | `php src/Toolkit/bin/toolkit sync:package path/to/pack`. Usa `sync:library` para mantener la biblioteca compartida Library. |
| `vendor/bin/lang create` | `php src/Toolkit/bin/toolkit create-locale path/to/pack`. Crea directorios para las configuraciones regionales conocidas. |
| `vendor/bin/lang download` | Pasa la URL del ZIP con `--url`, el destino completo con `--directory` y las rutas del archivo mediante `--paths`, que se puede repetir. |
| `vendor/bin/lang translate` | Mueve la traducción automática a un servicio separado. Ejecuta `status` de Toolkit para encontrar cadenas guardadas que aún coincidan con el original. |

Consulta [Comandos de Toolkit](./toolkit.md) para ver ejemplos completos.

## Verificar la actualización {#verify-the-upgrade}

Ejecuta las pruebas de tu aplicación. Comprueba un mensaje publicado en cada configuración regional importante, una traducción de reserva, una URL localizada con tu alias configurado y una traducción de modelo almacenada con el código canónico. Revisa los archivos generados y los valores de configuración regional guardados junto con los cambios en el código fuente.
