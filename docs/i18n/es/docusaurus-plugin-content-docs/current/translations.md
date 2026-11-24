---
title: Gestionar traducciones
description: Agrega, actualiza y elimina traducciones de la aplicación y de sus dependencias con Publisher de Mona.
---

Publisher se incluye en los paquetes de traducción oficiales. Sus comandos de Artisan escriben traducciones PHP y JSON en el directorio de idiomas de la aplicación, normalmente `lang/`. También pueden escribir archivos específicos de un paquete en `lang/vendor/`.

## Agregar configuraciones regionales {#add-locales}

Pasa uno o más [códigos de configuración regional](./available-locales.md) separados por espacios. El comando acepta los alias configurados y genera archivos con los códigos canónicos.

```bash
php artisan lang:add fr de_CH
```

El comando crea los directorios de las configuraciones regionales y luego actualiza **todas las configuraciones regionales instaladas**, incluidas la actual y la de reserva de la aplicación. Sin argumentos, pregunta si debe instalar todas las configuraciones regionales compatibles. En las automatizaciones, especifica los códigos.

## Actualizar traducciones {#update-translations}

```bash
php artisan lang:update
```

Publisher detecta las configuraciones regionales a partir de la actual y la de reserva de la aplicación, los archivos y directorios en la raíz del directorio de idiomas y los archivos JSON de `lang/vendor/<package>/`. Un directorio de traducciones PHP de una dependencia por sí solo no marca una configuración regional como instalada. Usa `lang:add` con esa configuración regional para crear su directorio en la aplicación.

Los paquetes de traducción declaran los paquetes, las versiones y los archivos de destino compatibles en los [manifiestos de complementos](./plugins.md). Publisher combina los diccionarios compartidos y los de cada paquete, e incorpora sus traducciones en cada archivo de destino. Conserva las claves ajenas a la actualización y reemplaza las claves coincidentes, incluidas las numéricas. Si una frase no existe en los diccionarios, Publisher escribe la frase original. Mantén las traducciones personalizadas en archivos separados o [excluye](./configure.md#skip-files-and-force-integrations) los destinos generados que mantienes tú mismo.

Ejecuta `lang:update` después de cambiar las dependencias de Composer o la [configuración de publicación](./configure.md). Revisa el diff generado.

## Eliminar configuraciones regionales {#remove-locales}

```bash
php artisan lang:rm de_CH
```

El comando elimina el directorio con el código canónico y el archivo JSON de la configuración regional de la raíz de idiomas y de cada directorio inmediato `lang/vendor/<package>/`, incluidos los archivos personalizados que contengan. La configuración regional actual y la de reserva están protegidas. Si no pasas argumentos, el comando pregunta si debe eliminar todas las configuraciones regionales no protegidas. La respuesta predeterminada es negativa. Si un directorio de la configuración regional es un enlace, el comando elimina el enlace en sí.

El comando indica `DONE`, `PROTECTED`, `INVALID`, `SKIP` o `FAILED` para cada configuración regional. Si queda un archivo o directorio después del intento de eliminación, muestra su ruta y devuelve un código de error. Comprueba el estado de cada configuración regional, porque `PROTECTED`, `INVALID` y `SKIP` pueden aparecer con un código de salida de éxito.

## JSON de reserva {#json-fallback}

Si falta una clave JSON, `laravel-lang/json-fallback` agrega una búsqueda en el JSON de la configuración regional de reserva antes de pasar a las traducciones PHP. Common incluye este paquete. Para instalarlo por separado, ejecuta `composer require laravel-lang/json-fallback`.

Agrega `LaravelLang\JsonFallback\LangJsonFallbackServiceProvider::class` a los proveedores de la aplicación en `bootstrap/providers.php`. Si la aplicación incluye explícitamente `Illuminate\Translation\TranslationServiceProvider`, reemplaza esa entrada por el proveedor de Mona. [Registro de proveedores en Laravel](https://laravel.com/docs/13.x/providers#registering-providers)

Si el francés es la configuración regional actual y el inglés es la de reserva, una clave ausente de `lang/fr.json` puede recuperar ahora su valor de `lang/en.json`.
