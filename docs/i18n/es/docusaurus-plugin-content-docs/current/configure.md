---
title: Configuración
description: Configura los estilos de traducción, los alias de configuración regional, los filtros de publicación, la puntuación, las rutas y los modelos de Mona.
---

Mona carga automáticamente la configuración predeterminada. Publica los archivos que necesites personalizar en el directorio `config/` de tu aplicación.

```bash
php artisan vendor:publish --tag=localization_main
```

La etiqueta `localization` publica los cinco archivos. El nombre de cada archivo sin `.php` también es una etiqueta de publicación y una clave de configuración de Laravel.

| Archivo | Configuración |
| --- | --- |
| `localization_main.php` | Estilos de traducción, alias y filtros de publicación |
| `localization_punctuation.php` | Puntuación tipográfica y comillas específicas de cada configuración regional |
| `localization_routes.php` | [Nombres de rutas, fuentes de configuración regional y middleware](./routes.md) |
| `localization_models.php` | [Sufijo de modelo de traducción, filtrado y rutas del generador](./models.md) |
| `localization_map.php` | Metadatos de configuración regional como escritura, región y dirección |

Laravel sustituye cada ajuste de nivel superior por el valor de la aplicación. Si editas el array `locales` en `localization_punctuation.php`, conserva las excepciones predeterminadas que aún quieras usar.

Después de cambiar la configuración, borra su caché existente con `php artisan config:clear`. Ejecuta `php artisan lang:update` para regenerar las traducciones afectadas por los ajustes de publicación.

## Estilos de traducción {#translation-styles}

Un estilo de traducción es un diccionario con nombre que modifica la redacción de determinadas frases. Por ejemplo, un paquete puede incluir `informal.json` para un tono cercano o `john.json` para las expresiones de un producto. Los nombres de los estilos son independientes de los códigos de configuración regional y de los nombres de aplicaciones y paquetes. Cualquier paquete de traducción puede proporcionar un estilo en `locales/<locale>/<style>.json` para los idiomas que admite. Un estilo puede contener una sola frase. El caso de uso original eran las formas de tratamiento informal. [Debate sobre las traducciones informales](https://github.com/orgs/Laravel-Lang/discussions/124)

Después de instalar un complemento que proporcione `john`, ponlo al final de `config/localization_main.php` para que tenga prioridad dentro de cada fuente de diccionario.

```php
'styles' => ['default', 'informal', 'john'],
```

Al ejecutar `php artisan lang:update`, Publisher combina los archivos de estilos de cada configuración regional instalada. Para cada frase, prevalece `john` si la contiene, seguido de `informal` y luego de `default`. Por ejemplo, un paquete en francés puede sustituir una frase en `locales/fr/john.json`, mientras que para esa misma frase el alemán usa `locales/de/informal.json` o `locales/de/default.json`. Publisher aplica el orden por separado a Library, que es compartido, y al paquete que aporta el archivo. Para ese archivo, una frase coincidente del paquete tiene prioridad sobre la entrada de Library.

La configuración predeterminada es `['default', 'inline']`. El estilo `inline` proporciona mensajes de validación genéricos como «Este campo debe aceptarse». Establece `styles` en `['default']` para usar solo la redacción estándar. Si omites `default`, Publisher lo agrega al principio.

## Alias de configuración regional {#locale-aliases}

Define `localization_main.aliases` como un mapa de códigos canónicos a alias. Por ejemplo, agrega la siguiente entrada al arreglo que devuelve `config/localization_main.php`.

```php
'aliases' => [
    'de_CH' => 'de-CH',
],
```

Artisan puede aceptar `de-CH`, mientras que Laravel y los archivos publicados usan `de_CH`. Establece `APP_LOCALE` y `APP_FALLBACK_LOCALE` con códigos canónicos. Consulta [Configuraciones regionales y nombres](./locales.md) para ver ejemplos de conversión y búsqueda.

La comparación distingue mayúsculas y minúsculas. Usa un alias distinto para cada configuración regional que no coincida con el código canónico de otra.

## Excluir archivos y forzar integraciones {#skip-files-and-force-integrations}

- `localization_main.skip` compara patrones con `*` con rutas completas que usan `/` como separador, sin distinguir mayúsculas de minúsculas. Puede excluir archivos fuente, diccionarios y destinos de publicación. Por ejemplo, `['*/lang/fr/validation.php']` impide que Publisher escriba ese destino en una aplicación que usa `lang/`.
- `localization_main.force` acepta nombres de paquetes de Composer. Permite a Publisher usar entradas de manifiesto incluso si la dependencia indicada no está instalada, la versión instalada no coincide o el nombre del proyecto raíz es distinto. Úsalo cuando las traducciones del manifiesto correspondan a tu aplicación.

`skip` tiene prioridad sobre `force`.

## Puntuación tipográfica {#smart-punctuation}

Establece `LOCALIZATION_PUNCTUATION_ENABLED=true` para habilitar las comillas, las rayas y los puntos suspensivos tipográficos en las traducciones publicadas. Edita `localization_punctuation.common` para las reglas generales y `localization_punctuation.locales` para las excepciones por código canónico de configuración regional. Las reglas se aplicarán a los textos publicados la próxima vez que Publisher los escriba.

## Metadatos de configuración regional {#locale-metadata}

`localization_map.locales` asigna a cada código canónico un sistema de escritura (`type`), una configuración regional (`regional`) y una dirección de escritura opcional (`direction`). Si no hay una dirección configurada, usa `Direction::LeftToRight`.

```php
use LaravelLang\LocaleList\Direction;

$metadata = config('localization_map.locales.ar', []);
$script = $metadata['type'];         // 'Arab'
$regional = $metadata['regional'];  // 'ar_AE'
$dir = ($metadata['direction'] ?? Direction::LeftToRight)->value; // 'rtl'
```
