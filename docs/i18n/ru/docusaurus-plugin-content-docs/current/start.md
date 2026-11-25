---
title: Начало работы
description: Установите Laravel-Lang и опубликуйте первые переводы для Laravel.
---

Laravel-Lang публикует переводы Laravel и пакетов его экосистемы. Комплект `laravel-lang/common` также позволяет [получать локали и названия](./locales.md), [локализовать маршруты](./routes.md) и [переводить модели](./models.md). Если вам нужны только переводы Laravel и зависимости для их публикации, выберите `laravel-lang/lang`.

## Быстрый старт {#quick-start}

Выполните команды из корневого каталога приложения на Laravel 12 или 13 с Composer, PHP `^8.3` и расширениями `intl` и `json`. Ваша версия Laravel может требовать более новую версию PHP. Команда установки рассчитана на стабильные Composer-пакеты линейки 20.x после их публикации.

```bash
composer require "laravel-lang/common:20.*"
php artisan lang:add fr
```

Используйте функцию перевода Laravel в своём приложении, чтобы получить опубликованное сообщение на французском.

```php
__('auth.failed', locale: 'fr');
// Ces identifiants ne correspondent pas à nos enregistrements.
```

Чтобы сделать французский языком по умолчанию, задайте `APP_LOCALE=fr` в файле приложения `.env`. Задайте `APP_FALLBACK_LOCALE=en`, чтобы при поиске с резервной локалью использовать английский, если перевода для текущей локали нет. Если конфигурация закэширована, очистите кэш командой `php artisan config:clear`. Теперь вызовы вроде `__('auth.failed')` используют текущую локаль приложения.

- [Предыдущие версии Laravel-Lang](/legacy)
- [Управляйте переводами](./translations.md) и [смотрите список доступных локалей](./available-locales.md)
- [Настройте локализацию](./configure.md)
- [Проверьте совместимость](./release-notes.md#versioning-and-compatibility) или откройте руководство [Апгрейд до Mona](./upgrade.md)

## Попробовать релиз-кандидат {#try-a-release-candidate}

Для проверки опубликованного релиз-кандидата 20.x используйте отдельное тестовое приложение Laravel. Установите в нём минимальный уровень стабильности `RC`, чтобы Composer мог установить зависимости Laravel-Lang той же версии. Выберите версию из списка на [Packagist](https://packagist.org/packages/laravel-lang/common). В примере используется `20.0-rc2`.

```bash
composer config minimum-stability RC
composer config prefer-stable true
composer require "laravel-lang/common:20.0-rc2"
php artisan lang:add fr
```
