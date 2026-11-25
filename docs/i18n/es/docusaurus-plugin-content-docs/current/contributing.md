---
title: Cómo contribuir
description: "Contribuye a las traducciones, los paquetes PHP y la documentación desde una copia local de Mona."
---

Crea un fork del [repositorio Mona](https://github.com/Laravel-Lang/mona) y abre una solicitud de cambios hacia su rama `main`. Mona reúne las plantillas fuente, las traducciones compartidas, los paquetes PHP y la documentación. Los repositorios individuales de los paquetes reciben su contenido desde Mona.

## Corregir o agregar una traducción {#fix-or-add-a-translation}

1. Busca la frase en inglés en el directorio `src/<Package>/source/` del paquete. Por ejemplo, las fuentes de Laravel Framework y de los paquetes oficiales de Laravel están en `src/Lang/source/`. El archivo `laravel_lang.json` del paquete indica los archivos fuente que se usan para cada versión del paquete de origen.
2. Abre `src/Library/locales/<locale>/default.json`. Edita el valor de la frase en inglés o añade una entrada si no existe. Usa el valor exacto de origen como clave JSON, incluso para las frases en archivos PHP.
3. Conserva marcadores de posición como `:count`, `:other` y `:attribute` en la traducción. Para un mensaje de validación en línea, usa la misma clave de origen en `inline.json` y sustituye `:attribute` por una redacción que prescinda del nombre del campo. Conserva los demás marcadores y las formas plurales.
4. Si una traducción coincide intencionalmente con la frase en inglés, agrega la frase a `excludes` en el archivo `_settings.json` de la configuración regional. Toolkit usa esta lista al generar el estado de las traducciones.
5. Abre una solicitud de cambios con el código de configuración regional en el título, por ejemplo `[fr]: Updated validation messages`.

Si encuentras un error de escritura en una frase de un proyecto del que provienen las traducciones, infórmalo allí para que la frase original y su clave de búsqueda se puedan actualizar juntas.

Por ejemplo, en `src/Library/locales/fr/default.json` se encuentra esta entrada predeterminada en francés.

```json
{
  "The :attribute field is invalid.": "Le champ :attribute est invalide."
}
```

Esta es la entrada en línea correspondiente de `src/Library/locales/fr/inline.json`.

```json
{
  "The :attribute field is invalid.": "Ce champ est invalide."
}
```

Para las traducciones que Mona publica en la aplicación, edita `default.json` e `inline.json` en Library. Algunos paquetes aún contienen los diccionarios anteriores `php.json` y `json.json`, que también se usan en la comprobación automática del estado de los paquetes.

Para actualizar los nombres de países, monedas o configuraciones regionales, edita el archivo JSON de la configuración regional en `src/CountryNames/data/`, `src/CurrencyNames/data/` o `src/LocaleNames/data/`. Consulta [Configuraciones regionales y nombres traducidos](./locales.md).

## Proponer una configuración regional {#propose-a-locale}

Consulta los [códigos canónicos de configuración regional](./available-locales.md). Si el código ya figura en la lista, añade o actualiza su diccionario en `src/Library/locales/`. Toolkit puede [crear los directorios de configuración regional que faltan](./toolkit.md#manage-locale-directories). Para proponer un código nuevo, abre un [debate de Laravel-Lang](https://github.com/orgs/Laravel-Lang/discussions/new?category=ideas&title=I%20want%20to%20add%20a%20new%20localization).

## Desarrollar paquetes {#develop-packages}

Usa Git, Composer 2 y PHP `^8.3` con `intl`, `json` y `zip`. Activa `pdo_sqlite` para las pruebas, que utilizan una base de datos en memoria. Ejecuta los comandos desde la raíz del repositorio.

```bash
composer install
composer check-platform-reqs
```

El proyecto Composer de la raíz carga automáticamente el código de `src/<Package>/src/`. Declara las dependencias de cada paquete publicado en su propio archivo `src/<Package>/composer.json`. Las dependencias internas usan `self.version` para poder instalar juntos los paquetes de la misma versión.

Usa la aplicación Testbench de `workbench/` para probar los paquetes en local. `composer serve` prepara Workbench e inicia el servidor de desarrollo. Para consultar rutas o comandos Artisan, ejecuta la interfaz de línea de comandos de Testbench desde la raíz.

```bash
composer serve
```

```bash
php vendor/bin/testbench route:list
php vendor/bin/testbench help lang:update
```

La integración continua instala cada paquete por separado. Un repositorio Composer de tipo `path` proporciona los demás paquetes de la copia local. Si cambias las dependencias de un paquete, comprueba su instalación individual y ejecuta las pruebas de la raíz. La configuración y la matriz de versiones de Laravel figuran en el [flujo de trabajo Test Dependencies](https://github.com/Laravel-Lang/mona/blob/main/.github/workflows/test-dependencies.yml).

## Ejecutar comprobaciones locales {#run-local-checks}

Para cambios en los diccionarios, instala las dependencias de JavaScript en la raíz y ejecuta las comprobaciones de Biome. El comando `status` de Toolkit escribe informes sobre las entradas cuyo valor coincide con la clave de origen. Comprueba los archivos fuente del paquete para localizar frases sin entrada en el diccionario. Usa `git status --short` para revisar los informes generados. [Comandos de Toolkit](./toolkit.md)

```bash
npm install
npx biome check src/Library/locales/fr/default.json
php src/Toolkit/bin/toolkit status src/Library
```

Para cambios en PHP, ejecuta Pest y PHPStan desde la raíz del repositorio. Ejecuta las pruebas funcionales en serie, como en la integración continua, porque las pruebas de generación de modelos comparten el directorio de la aplicación Testbench. Las pruebas de arquitectura pueden ejecutarse en paralelo. Para probar una función, pasa su archivo o directorio de pruebas a Pest.

```bash
php vendor/bin/pest --exclude-group=arch
php vendor/bin/pest --group=arch --parallel
composer test:types
```

Si las pruebas de rutas generan URL con `http://localhost:8000` por la configuración de desarrollo de Testbench, define `APP_URL` como `http://localhost` en el proceso de pruebas antes de ejecutar Pest.

El script `composer style` normaliza los archivos de Composer y aplica las correcciones de Rector y Pint. Revisa sus cambios antes de enviar la solicitud. Para medir la cobertura de líneas, activa Xdebug y ejecuta `composer run-script --timeout=0 test:coverage`.

Si cambias un manifiesto, ejecuta la misma validación de esquema que se usa en la integración continua.

```bash
npx --yes ajv-cli@5.0.0 validate --spec=draft2020 -s resources/laravel-lang.schema.json -d 'src/*/laravel_lang.json'
```

Consulta [Complementos de traducción](./plugins.md).

## Editar la documentación {#edit-the-documentation}

Las páginas en inglés de la versión actual están en `docs/docs/` y sus traducciones en `docs/i18n/<language>/docusaurus-plugin-content-docs/current/`. Cuando cambien las instrucciones, actualiza las páginas correspondientes y conserva los mismos identificadores de encabezado para los enlaces en todos los idiomas. La versión anterior a Mona está archivada en `docs/versioned_docs/version-legacy/` y sus traducciones se encuentran en los directorios `version-legacy/` correspondientes dentro de `docs/i18n/`.

Usa la versión de Node.js requerida por `docs/package.json`. Ejecuta los comandos de documentación desde `docs/`.

```bash
cd docs
npm ci
npm start
```

Usa `npm start -- --locale fr` para obtener una vista previa de una traducción. Detén el servidor de desarrollo y ejecuta la compilación de producción para comprobar enlaces, anclas e índices de búsqueda en todos los idiomas configurados.

```bash
npm run typecheck
npm run build
npm run serve
```

Abre la dirección que muestra `serve` para comprobar la búsqueda y las exportaciones Markdown generadas. La compilación escribe el sitio estático en `docs/build/`.

## Publicar una versión {#publish-a-release}

Una fusión en `main` exporta el subárbol de cada paquete a la rama `main` de su repositorio y actualiza el borrador de la versión de Mona. Revisa el borrador y publica la versión en Mona con la etiqueta de versión compartida. El evento de publicación activa los flujos de trabajo de la versión.

La publicación exporta la etiqueta y crea la versión correspondiente en el repositorio de cada paquete. También inicia la compilación y el despliegue del sitio de documentación. Comprueba ambos flujos de trabajo después de publicar. Para previsualizar la separación de los paquetes, ejecuta manualmente **Export Packages** desde `main` con `dry-run` activado. [Export Packages](https://github.com/Laravel-Lang/mona/actions/workflows/export-packages.yml) · [Documentation](https://github.com/Laravel-Lang/mona/actions/workflows/docs.yml)
