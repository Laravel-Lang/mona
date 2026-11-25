---
title: Primeros pasos
description: Instala Laravel-Lang y publica tus primeras traducciones de Laravel.
---

Laravel-Lang publica traducciones para Laravel y su ecosistema. El paquete `laravel-lang/common` también permite [consultar nombres y configuraciones regionales](./locales.md), [localizar rutas](./routes.md) y [traducir modelos](./models.md). Si solo necesitas el paquete de traducciones de Laravel y sus dependencias de publicación, elige `laravel-lang/lang`.

## Inicio rápido {#quick-start}

Ejecuta estos comandos en la raíz de una aplicación Laravel 12 o 13 con Composer, PHP `^8.3` y las extensiones de PHP `intl` y `json`. Tu versión de Laravel puede requerir una versión más reciente de PHP.

```bash
composer require laravel-lang/common
php artisan lang:add fr
```

Usa la función de traducción de Laravel en tu aplicación para consultar un mensaje publicado en francés.

```php
__('auth.failed', locale: 'fr');
// Ces identifiants ne correspondent pas à nos enregistrements.
```

Para que el francés sea el idioma predeterminado, configura `APP_LOCALE=fr` en el archivo `.env` de la aplicación. Configura `APP_FALLBACK_LOCALE=en` para recurrir al inglés cuando una consulta con respaldo no encuentre una traducción en la configuración regional activa. Borra la caché de configuración, si existe, con `php artisan config:clear`. Las llamadas como `__('auth.failed')` usarán la configuración regional activa de la aplicación.

- [Versiones anteriores de Laravel-Lang](/legacy)
- [Gestionar traducciones](./translations.md) y [consultar las configuraciones regionales disponibles](./available-locales.md)
- [Configurar la localización](./configure.md)
- [Comprobar la compatibilidad](./release-notes.md#versioning-and-compatibility) o [actualizar una instalación existente](./upgrade.md)

## Probar una versión candidata {#try-a-release-candidate}

Usa una aplicación de prueba de Laravel independiente para probar una versión candidata de 20.x publicada. Define la estabilidad mínima de esa aplicación en `RC` para que Composer también pueda instalar las dependencias correspondientes de Laravel-Lang. Elige una versión de [Packagist](https://packagist.org/packages/laravel-lang/common). Sustituye `<release-candidate>` por la versión elegida.

```bash
composer config minimum-stability RC
composer config prefer-stable true
composer require "laravel-lang/common:<release-candidate>"
php artisan lang:add fr
```
