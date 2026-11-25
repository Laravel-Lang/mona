---
title: Початок роботи
description: Установіть Laravel-Lang і опублікуйте перші переклади Laravel.
---

Laravel-Lang публікує переклади для Laravel і пакетів його екосистеми. Набір `laravel-lang/common` також дає змогу [отримувати локалі й назви](./locales.md), [локалізувати маршрути](./routes.md) і [перекладати моделі](./models.md). Якщо вам потрібні лише переклади Laravel і залежності для їх публікації, виберіть `laravel-lang/lang`.

## Швидкий старт {#quick-start}

Виконайте ці команди з кореневого каталогу застосунку на Laravel 12 або 13 із Composer, PHP `^8.3` та розширеннями PHP `intl` і `json`. Ваша версія Laravel може вимагати новішої версії PHP.

```bash
composer require laravel-lang/common
php artisan lang:add fr
```

Скористайтеся функцією перекладу Laravel у своєму застосунку, щоб отримати опубліковане повідомлення французькою.

```php
__('auth.failed', locale: 'fr');
// Ces identifiants ne correspondent pas à nos enregistrements.
```

Щоб зробити французьку мовою за замовчуванням, задайте `APP_LOCALE=fr` у файлі `.env` застосунку. Задайте `APP_FALLBACK_LOCALE=en`, щоб використовувати англійську, якщо пошук із резервною локаллю не знаходить перекладу поточною мовою. Якщо конфігурацію закешовано, очистьте кеш командою `php artisan config:clear`. Після цього виклики на кшталт `__('auth.failed')` використовують поточну локаль застосунку.

- [Попередні версії Laravel-Lang](/legacy)
- [Керуйте перекладами](./translations.md) та [перегляньте доступні локалі](./available-locales.md)
- [Налаштуйте локалізацію](./configure.md)
- [Перевірте сумісність](./release-notes.md#versioning-and-compatibility) або [оновіть наявну інсталяцію](./upgrade.md)

## Спробувати реліз-кандидат {#try-a-release-candidate}

Щоб спробувати опублікований реліз-кандидат 20.x, використовуйте окремий тестовий застосунок Laravel. Встановіть у ньому мінімальний рівень стабільності `RC`, щоб Composer міг установити відповідні залежності Laravel-Lang. Виберіть версію зі списку на [Packagist](https://packagist.org/packages/laravel-lang/common). Підставте вибрану версію замість `<release-candidate>`.

```bash
composer config minimum-stability RC
composer config prefer-stable true
composer require "laravel-lang/common:<release-candidate>"
php artisan lang:add fr
```
