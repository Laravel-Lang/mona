---
title: "Başlangıç"
description: "Laravel-Lang’i kurun ve Laravel için ilk çevirilerinizi yayımlayın."
---

Laravel-Lang, Laravel ve ekosistemindeki paketler için çeviriler yayımlar. `laravel-lang/common` paketi ayrıca [yerel ayar ve ad sorgulama](./locales.md), [yerelleştirilmiş rotalar](./routes.md) ve [model çevirileri](./models.md) sağlar. Yalnızca Laravel çeviri paketini ve yayımlama bağımlılıklarını kurmak için `laravel-lang/lang` paketini seçin.

## Hızlı başlangıç {#quick-start}

Bu komutları Composer, PHP `^8.3` ve `intl` ile `json` PHP uzantılarının bulunduğu Laravel 12 veya 13 uygulamasının kök dizininde çalıştırın. Laravel sürümünüz daha yeni bir PHP sürümü gerektirebilir.

```bash
composer require laravel-lang/common
php artisan lang:add fr
```

Fransızca yayımlanmış bir mesajı okumak için uygulamanızda Laravel’in çeviri yardımcı işlevini kullanın.

```php
__('auth.failed', locale: 'fr');
// Ces identifiants ne correspondent pas à nos enregistrements.
```

Fransızcayı varsayılan dil yapmak için uygulamanızın `.env` dosyasında `APP_LOCALE=fr` değerini ayarlayın. Yedek dilin etkin olduğu bir aramada geçerli dilde çeviri bulunamazsa İngilizce kullanmak için `APP_FALLBACK_LOCALE=en` değerini ayarlayın. Yapılandırma önbelleğe alınmışsa `php artisan config:clear` komutuyla önbelleği temizleyin. Ardından `__('auth.failed')` gibi çağrılar uygulamanın geçerli yerel ayarını kullanır.

- [Önceki Laravel-Lang sürümleri](/legacy)
- [Çevirileri yönetin](./translations.md) ve [desteklenen yerel ayarlara göz atın](./available-locales.md)
- [Yerelleştirmeyi yapılandırın](./configure.md)
- [Uyumluluğu kontrol edin](./release-notes.md#versioning-and-compatibility) veya [mevcut bir kurulumu yükseltin](./upgrade.md)

## Sürüm adayını deneme {#try-a-release-candidate}

Yayımlanmış bir 20.x sürüm adayını denemek için ayrı bir Laravel test uygulaması kullanın. Composer’ın aynı sürümdeki Laravel-Lang bağımlılıklarını da kurabilmesi için bu uygulamanın asgari kararlılık düzeyini `RC` olarak ayarlayın. [Packagist](https://packagist.org/packages/laravel-lang/common) listesinden bir sürüm seçin. `<release-candidate>` yerine seçtiğiniz sürümü yazın.

```bash
composer config minimum-stability RC
composer config prefer-stable true
composer require "laravel-lang/common:<release-candidate>"
php artisan lang:add fr
```
