---
title: "Пачатак працы"
description: "Усталюйце Laravel-Lang і апублікуйце першыя пераклады Laravel."
---

Laravel-Lang публікуе пераклады для Laravel і пакетаў яго экасістэмы. Набор `laravel-lang/common` таксама дадае [пошук лакаляў і назваў](./locales.md), [лакалізаваныя маршруты](./routes.md) і [пераклады мадэляў](./models.md). Калі патрэбныя толькі пераклады Laravel і залежнасці для іх публікацыі, выберыце `laravel-lang/lang`.

## Хуткі пачатак {#quick-start}

Выконвайце гэтыя каманды з каранёвай папкі праграмы на Laravel 12 або 13 з Composer, PHP `^8.3` і пашырэннямі PHP `intl` і `json`. Вашай версіі Laravel можа спатрэбіцца навейшая версія PHP.

```bash
composer require laravel-lang/common
php artisan lang:add fr
```

Выкарыстайце функцыю перакладу Laravel у сваёй праграме, каб атрымаць апублікаванае паведамленне па-французску.

```php
__('auth.failed', locale: 'fr');
// Ces identifiants ne correspondent pas à nos enregistrements.
```

Каб задаць французскую мову па змаўчанні, дадайце `APP_LOCALE=fr` у файл `.env` праграмы. Задайце `APP_FALLBACK_LOCALE=en`, каб выкарыстоўваць англійскую мову, калі пошук з падтрымкай запасной лакалі не знаходзіць перакладу ў бягучай лакалі. Ачысціце наяўны кэш канфігурацыі камандай `php artisan config:clear`. Выклікі накшталт `__('auth.failed')` будуць выкарыстоўваць бягучую лакаль праграмы.

- [Ранейшыя версіі Laravel-Lang](/legacy)
- [Кіруйце перакладамі](./translations.md) і [прагледзьце даступныя лакалі](./available-locales.md)
- [Наладзьце лакалізацыю](./configure.md)
- [Праверце сумяшчальнасць](./release-notes.md#versioning-and-compatibility) або [абнавіце наяўнае ўсталяванне](./upgrade.md)

## Паспрабаваць рэліз-кандыдат {#try-a-release-candidate}

Каб паспрабаваць апублікаваны рэліз-кандыдат 20.x, выкарыстоўвайце асобную тэставую праграму Laravel. Задайце для яе мінімальную стабільнасць `RC`, каб Composer мог усталяваць адпаведныя залежнасці Laravel-Lang. Выберыце версію з [Packagist](https://packagist.org/packages/laravel-lang/common). Замяніце `<release-candidate>` выбранай версіяй.

```bash
composer config minimum-stability RC
composer config prefer-stable true
composer require "laravel-lang/common:<release-candidate>"
php artisan lang:add fr
```
