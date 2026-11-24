---
title: Rutas localizadas
description: Registra URL localizadas y selecciona la configuración regional de Laravel a partir de una solicitud.
---

`laravel-lang/routes` selecciona la configuración regional al recibir una solicitud y genera URL para grupos de rutas localizadas. El paquete se incluye en Common.

```bash
composer require laravel-lang/routes
```

Asegúrate de que estén [instaladas las configuraciones regionales que usas en las URL](./translations.md). Configura los nombres de parámetros, el middleware y el comportamiento de la configuración regional predeterminada en [`config/localization_routes.php`](./configure.md).

## Registrar un grupo de rutas localizadas {#register-a-localized-route-group}

En `routes/web.php`, usa la macro `Route::localizedGroup()`. La fachada `LaravelLang\Routes\Facades\LocalizationRoute::group()` proporciona el mismo método para agrupar rutas.

```php
use Illuminate\Support\Facades\Route;

Route::localizedGroup(function (): void {
    Route::get('/products/{product}', fn (string $product) => view('products.show', [
        'product' => $product,
    ]))->name('products.show');
});
```

Con la configuración predeterminada, la función de retorno registra los dos patrones de URL.

| Patrón | Nombre de ruta |
| --- | --- |
| `/products/{product}` | `products.show` |
| `/{locale}/products/{product}` | `localized.products.show` |

El middleware de configuración regional consume el parámetro `locale` antes de invocar el controlador de la ruta. Si la ruta tiene nombre, una solicitud con una configuración regional desconocida o sin instalar en la URL redirige a la ruta sin prefijo.

## Generar una URL localizada {#generate-a-localized-url}

```php
use function LaravelLang\Routes\localizedRoute;

$url = localizedRoute('products.show', [
    'product' => 42,
    'locale' => 'fr',
], absolute: false);
// /fr/products/42
```

La función auxiliar usa el nombre de parámetro de configuración regional configurado y da preferencia a la ruta con prefijo si existe. Pasa a la generación de URL la cadena de configuración regional que se le proporciona. Para generar la URL con un alias, pasa ese alias u obtenlo antes con [`Locales::toAlias()`](./locales.md). Si omites la configuración regional, la función usa `Locales::fallback()`. Para usar la actual, pasa `Locales::current()` explícitamente.

`localization_routes.hide` omite la configuración regional de reserva de las URL que genera la función. `localization_routes.redirect` redirige las solicitudes de la configuración regional actual del grupo con prefijo a la ruta sin prefijo. El valor predeterminado de ambas opciones es `false`.

## Seleccionar una configuración regional con middleware {#select-a-locale-with-middleware}

Cada middleware se puede aplicar directamente mediante su clase de `LaravelLang\Routes\Middlewares`.

| Middleware | Fuente |
| --- | --- |
| `LocalizationByParameterMiddleware` | Un parámetro de ruta, `locale` de forma predeterminada |
| `LocalizationByParameterWithRedirectMiddleware` | Un parámetro de ruta. Si la ruta tiene nombre y declara el parámetro, usa la configuración regional actual cuando la solicitada falta o no está instalada |
| `LocalizationByCookieMiddleware` | Una cookie `Accept-Language` de forma predeterminada |
| `LocalizationByHeaderMiddleware` | La primera configuración regional instalada según el orden de preferencia de `Accept-Language`. Prueba la etiqueta completa antes que el idioma base y omite los comodines y las entradas con valor de calidad 0. |
| `LocalizationBySessionMiddleware` | Una clave de sesión `Accept-Language` de forma predeterminada |
| `LocalizationByModelMiddleware` | El atributo `locale` del usuario autenticado de Eloquent de forma predeterminada. Agrega `:guard_name` a la clase de middleware para seleccionar un guard |

El orden predeterminado para el grupo con prefijo es parámetro de URL, cookie, encabezado, sesión y modelo. El grupo sin prefijo usa el mismo orden sin el paso de la URL. Una fuente válida posterior puede reemplazar una anterior. Un cambio de configuración regional actualiza Laravel y guarda el código canónico en la clave de sesión configurada. Usa el grupo de middleware `web` si necesitas sesiones y cookies. El middleware de encabezado requiere PHP Intl.

Cuando el middleware cambia la configuración regional, emite `LaravelLang\Routes\Events\LocaleHasBeenSetEvent`. Su propiedad `locale` es un objeto `LaravelLang\LocaleNames\LocaleData`. Usa `$event->locale->code` para obtener el código canónico.
