---
title: Complementos de traducción
description: Instala paquetes de traducción o publica tu propio complemento de Composer para Laravel-Lang.
---

Publisher detecta los paquetes de traducción instalados con Composer. Cada paquete proporciona un manifiesto `laravel_lang.json`, archivos fuente en PHP o JSON y, de forma opcional, sus propios diccionarios por configuración regional.

## Instalar un paquete {#install-a-pack}

Instala el paquete en tu aplicación de Laravel y actualiza las configuraciones regionales que ya usas. Por ejemplo, el paquete Actions agrega etiquetas para botones y otras acciones.

```bash
composer require laravel-lang/actions
php artisan lang:update
```

Consulta [Gestionar traducciones](./translations.md) para agregar una configuración regional y [Complementos de la comunidad](./plugins-community.md) para encontrar paquetes de terceros.

### Paquetes de traducción oficiales {#official-translation-packs}

Common incluye estos paquetes. También puedes instalar cada uno por separado con Composer.

| Paquete | Traducciones |
| --- | --- |
| `laravel-lang/lang` | Framework Laravel y paquetes como Breeze, Cashier, Fortify, Jetstream, Nova, Passkeys y Laravel UI |
| `laravel-lang/starter-kits` | Kits de inicio de Laravel para Livewire, React, Svelte y Vue |
| `laravel-lang/moonshine` | Panel de administración MoonShine |
| `laravel-lang/actions` | Etiquetas de botones y otras acciones |
| `laravel-lang/attributes` | Nombres legibles de atributos de validación |
| `laravel-lang/http-statuses` | Frases de estado HTTP |

Cada paquete incorpora Publisher y Library, la biblioteca compartida. Su manifiesto indica para qué integraciones y versiones puede publicar traducciones.

## Crear un paquete {#create-a-pack}

Empieza por la plantilla de traducción. Declara `laravel-lang/publisher` como dependencia en el archivo `composer.json` de tu paquete e indica qué versiones admites. Agrega `laravel-lang/library` si tu paquete depende de las traducciones compartidas. Publisher busca manifiestos en `vendor/*/*/laravel_lang.json` dentro de la aplicación. [Plantilla de traducción](https://github.com/Laravel-Lang/mona/tree/main/src/TranslationsTemplate)

Coloca el manifiesto en la raíz del paquete. Este ejemplo publica un archivo JSON y un grupo de traducciones PHP para cada configuración regional instalada.

```json
{
  "$schema": "https://raw.githubusercontent.com/Laravel-Lang/mona/main/resources/laravel-lang.schema.json",
  "plugins": [
    {
      "files": [
        {"source": "audit.json", "target": "{locale}.json"},
        {"source": "audit.php", "target": "{locale}/audit.php"}
      ]
    }
  ]
}
```

Cada ruta `source` es relativa al directorio `source/` del paquete. Cada ruta `target` es relativa al directorio de idiomas de la aplicación de Laravel e incluye `{locale}`.

Por ejemplo, agrega el siguiente contenido a `source/audit.json`.

```json
{
  "Enable audit trail": "Enable audit trail"
}
```

Agrega el siguiente contenido a `source/audit.php`.

```php
<?php

return [
    'enable' => 'Enable audit trail',
];
```

Al publicar las traducciones al francés, ambas entradas buscan la frase en inglés en `locales/fr/default.json` del paquete.

```json
{
  "Enable audit trail": "Activer le journal d’audit"
}
```

En la aplicación, `__('Enable audit trail')` lee la traducción JSON y `__('audit.enable')` lee la traducción PHP. Las traducciones del paquete tienen prioridad sobre las entradas compartidas de Library para la misma frase. Si la frase no está en ninguno de los diccionarios, Publisher usa el valor original.

Asigna al paquete su propio nombre de Composer y sustituye `self.version` en la plantilla por restricciones adecuadas. Usa códigos canónicos de configuración regional para los directorios de diccionarios. Guarda las traducciones estándar en `default.json` y las variantes en archivos como `inline.json`.

### Aplicar traducciones de forma condicional {#apply-translations-conditionally}

| Propiedad del manifiesto | Cuándo se aplica una asignación |
| --- | --- |
| Sin condición | Publisher siempre la incluye. |
| `vendor` | El paquete de Composer indicado está instalado. |
| `vendor` y `version` | El paquete instalado cumple la restricción de versión de Composer. |
| `vendor` y `project: true` | El nombre de Composer del proyecto raíz coincide con `vendor`. En este modo, Publisher comprueba el nombre. |

Usa dos entradas de manifiesto si una asignación debe aplicarse tanto a una dependencia instalada como a un proyecto raíz con ese nombre. Consulta la [guía de actualización](./upgrade.md) para migrar un proveedor anterior y la [configuración](./configure.md) para las reglas de exclusión y coincidencia forzada de paquetes.

### Probar un paquete en una aplicación Laravel {#test-a-pack-in-a-laravel-application}

Ejecuta estos comandos desde la raíz de la aplicación de pruebas si el paquete está en un directorio hermano. Revisa `lang/fr.json` y `lang/fr/audit.php`, configura `fr` como idioma de la aplicación y prueba ambas claves. Después de editar el paquete, ejecuta `php artisan lang:update`.

```bash
composer config repositories.audit-translations path ../audit-translations
composer require "acme/audit-translations:*@dev"
php artisan lang:add fr
```
