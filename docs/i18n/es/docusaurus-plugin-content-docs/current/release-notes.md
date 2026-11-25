---
title: Notas de la versión
description: Versionado de Laravel-Lang y compatibilidad de plataformas.
---

## Versionado y compatibilidad {#versioning-and-compatibility}

Laravel-Lang sigue el versionado semántico. Los cambios incompatibles y la retirada de compatibilidad con una plataforma corresponden a versiones principales. Los paquetes oficiales comparten el mismo número de versión. [Versionado semántico](https://semver.org/)

La versión actual requiere PHP `^8.3` y es compatible con Laravel 12 y 13. Tu versión de Laravel puede requerir una versión de PHP más reciente. Common también requiere las extensiones `intl` y `json` a través de sus dependencias.

Las etiquetas y notas de cada versión están disponibles en las [versiones de Mona](https://github.com/Laravel-Lang/mona/releases). Consulta las fechas de mantenimiento de Laravel en su [política de soporte](https://laravel.com/docs/releases#support-policy).

## Disponibilidad de los paquetes {#package-availability}

El flujo de exportación actualiza las ramas `main` de los repositorios de paquetes tras cada envío a `main` en Mona. Cuando se publica una versión de Mona, exporta su etiqueta a esos repositorios.

Antes de instalar `20.x` o actualizar a esa versión, comprueba que la versión del paquete de Composer que necesitas esté disponible en Packagist. La versión de Mona y sus paquetes de Composer pueden publicarse en momentos distintos. [Ejecuciones de la exportación de paquetes](https://github.com/Laravel-Lang/mona/actions/workflows/export-packages.yml)
