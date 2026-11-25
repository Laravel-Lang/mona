---
title: Toolkit de traducción
description: Mantén, migra e importa fuentes de traducción y diccionarios desde una copia local de Mona.
---

Toolkit es la herramienta de línea de comandos para mantener paquetes de traducción. Ejecútala desde una copia local de Mona después de instalar las dependencias de Composer de la raíz. Para publicar traducciones en una aplicación de Laravel, usa los [comandos de Artisan](./translations.md).

```bash
composer install
php src/Toolkit/bin/toolkit list
php src/Toolkit/bin/toolkit migrate-locales --help
```

## Comprobar el estado de las traducciones {#check-translation-status}

```bash
php src/Toolkit/bin/toolkit status src/Library
```

Por defecto, el comando escribe `locales/<locale>/_status.json` dentro del proyecto. Enumera las entradas cuya traducción guardada coincide con la frase original y las agrupa por estilo. Combina las exclusiones de `locales/_settings.json` y `locales/<locale>/_settings.json` y elimina los informes vacíos. Para encontrar frases que falten por completo en un diccionario, compáralo con los archivos fuente del paquete.

Usa `--output-dir` para guardar los informes con el nombre `<locale>.json` en un directorio aparte. El comando resuelve las rutas relativas a partir del directorio de trabajo actual y crea el directorio de destino cuando tiene un informe que escribir.

```bash
php src/Toolkit/bin/toolkit status src/Library --output-dir=workbench/status
```

## Gestionar directorios de configuración regional {#manage-locale-directories}

```bash
php src/Toolkit/bin/toolkit create-locale src/Library
php src/Toolkit/bin/toolkit delete-locale src/Library
```

`create-locale` crea los directorios que falten para todos los [códigos de configuración regional](./available-locales.md) canónicos. Los directorios nuevos están vacíos. `delete-locale` elimina cada directorio inmediato de `locales/` cuyo nombre no figura en la lista. Comprueba el destino antes de ejecutarlo, sobre todo si contiene alias o códigos personalizados.

## Eliminar entradas obsoletas de los diccionarios {#remove-obsolete-dictionary-entries}

```bash
php src/Toolkit/bin/toolkit sync:library src
php src/Toolkit/bin/toolkit sync:package path/to/translation-pack
```

- `sync:library` recopila las frases originales de los paquetes con un manifiesto `laravel_lang.json` en `src/` y reescribe los diccionarios en `src/Library/locales/`.
- `sync:package` lee el directorio `source/` de un paquete y elimina las entradas sin usar de su directorio `locales/`. Elimina los diccionarios que queden vacíos.

Ambos conservan las traducciones existentes de las frases activas y los archivos que empiezan por un guion bajo, como `_settings.json`. Agrega directamente al diccionario las traducciones de frases nuevas.

## Migrar diccionarios de versiones anteriores {#migrate-legacy-dictionaries}

```bash
php src/Toolkit/bin/toolkit migrate-locales path/to/legacy-pack --target=path/to/new-pack/locales
```

Pasa un paquete de una versión anterior que contenga `source/` y `locales/`. El destino es el directorio que debe contener los subdirectorios por configuración regional. Pasa `--target` en los scripts. Una ejecución interactiva lo solicita si lo omites.

- `php.json` y `json.json` se convierten en `default.json`. Las nuevas claves son las frases originales.
- `php-inline.json` y `json-inline.json` se convierten en `inline.json` para las frases originales que incluyen `:attribute`.
- Las entradas de `_excludes.json` se trasladan a `excludes` en `_settings.json` de la configuración regional de destino.

Toolkit conserva los archivos anteriores e incorpora los datos a los diccionarios de destino. Si coincide una clave, prevalece la traducción migrada. Si ambos formatos anteriores proporcionan una traducción para la misma frase, prevalece el diccionario PHP. Usa un destino independiente para revisar la migración antes de sustituir un diccionario activo. Consulta la [guía de actualización](./upgrade.md) para ver los cambios en tu aplicación o en el manifiesto del complemento.

## Importar archivos fuente de un ZIP {#import-sources-from-a-zip-archive}

```bash
php src/Toolkit/bin/toolkit download \
  --url=https://github.com/laravel/framework/archive/refs/heads/12.x.zip \
  --directory=workbench/downloads/framework/12.x \
  --paths=src/Illuminate/Translation/lang/en \
  --only-copy
```

- `--directory` establece el destino final. Puedes repetir `--paths` para indicar directorios relativos a la raíz del archivo.
- Los archivos PHP y JSON seleccionados se copian al destino por nombre de archivo. Los archivos PHP se copian byte a byte. Los archivos JSON con el mismo nombre se combinan y prevalecen los valores entrantes.
- Sin `--only-copy`, Toolkit busca las llamadas de traducción compatibles con claves literales. Si encuentra frases, escribe un archivo fuente JSON con el nombre del directorio padre del destino, como `framework.json` en el ejemplo.
- El comando requiere la extensión ZIP de PHP. Prepara los cambios antes de publicarlos e intenta restaurar el destino anterior si falla la publicación.
