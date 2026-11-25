---
title: Почетак рада
description: Инсталирајте Laravel-Lang и објавите прве преводе за Laravel.
---

Laravel-Lang објављује преводе за Laravel и пакете из његовог екосистема. Пакет `laravel-lang/common` нуди и [претрагу локала и назива](./locales.md), [локализоване руте](./routes.md) и [преводе модела](./models.md). Ако вам је потребан само пакет превода за Laravel са зависностима за објављивање, изаберите `laravel-lang/lang`.

## Брзи почетак {#quick-start}

Покрените ове команде из коренског директоријума апликације на Laravel-у 12 или 13, са Composer-ом, PHP-ом `^8.3` и PHP екстензијама `intl` и `json`. Ваша верзија Laravel-а може захтевати новију верзију PHP-а.

```bash
composer require laravel-lang/common
php artisan lang:add fr
```

Користите Laravel-ову функцију за превођење у апликацији да бисте добили објављену поруку на француском.

```php
__('auth.failed', locale: 'fr');
// Ces identifiants ne correspondent pas à nos enregistrements.
```

Да бисте поставили француски као подразумевани језик, подесите `APP_LOCALE=fr` у датотеци `.env` апликације. Подесите `APP_FALLBACK_LOCALE=en` да бисте користили енглески када претрага са резервним локалом не пронађе превод за тренутни локал. Ако постоји кеш конфигурације, очистите га командом `php artisan config:clear`. Позиви попут `__('auth.failed')` тада користе тренутни локал апликације.

- [Раније верзије Laravel-Lang-а](/legacy)
- [Управљајте преводима](./translations.md) и [прегледајте доступне локале](./available-locales.md)
- [Подесите локализацију](./configure.md)
- [Проверите компатибилност](./release-notes.md#versioning-and-compatibility) или [надоградите постојећу инсталацију](./upgrade.md)

## Испробајте кандидата за издање {#try-a-release-candidate}

За испробавање објављеног кандидата за издање 20.x користите засебну Laravel апликацију за тестирање. У тој апликацији поставите минималну стабилност на `RC` да би Composer могао да инсталира и одговарајуће Laravel-Lang зависности. Изаберите верзију са [Packagist-а](https://packagist.org/packages/laravel-lang/common). Замените `<release-candidate>` изабраном верзијом.

```bash
composer config minimum-stability RC
composer config prefer-stable true
composer require "laravel-lang/common:<release-candidate>"
php artisan lang:add fr
```
