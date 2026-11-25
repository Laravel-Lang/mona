---
title: "Çeviri eklentileri"
description: "Çeviri paketleri kurun veya Laravel-Lang için kendi Composer eklentinizi yayımlayın."
---

Publisher, Composer ile kurulan çeviri paketlerini keşfeder. Paket, bir `laravel_lang.json` manifesti, PHP veya JSON kaynak dosyaları ve isteğe bağlı olarak kendi yerel ayar sözlüklerini sunar.

## Paket kurma {#install-a-pack}

Paketi Laravel uygulamanıza kurun ve kullandığınız yerel ayarları güncelleyin. Örneğin Actions paketi, düğmelere ve diğer eylemlere etiketler ekler.

```bash
composer require laravel-lang/actions
php artisan lang:update
```

Yerel ayar eklemek için [çeviri yönetimine](./translations.md), üçüncü taraf paketleri için [topluluk eklentilerine](./plugins-community.md) bakın.

### Resmî çeviri paketleri {#official-translation-packs}

Common bu paketleri içerir. Her birini Composer ile ayrı olarak da kurabilirsiniz.

| Paket | Çeviriler |
| --- | --- |
| `laravel-lang/lang` | Laravel çatısı ve Breeze, Cashier, Fortify, Jetstream, Nova, Passkeys, Laravel UI gibi paketler |
| `laravel-lang/starter-kits` | Laravel’in Livewire, React, Svelte ve Vue başlangıç kitleri |
| `laravel-lang/moonshine` | MoonShine yönetim paneli |
| `laravel-lang/actions` | Düğme ve diğer eylem etiketleri |
| `laravel-lang/attributes` | Okunabilir doğrulama özniteliği adları |
| `laravel-lang/http-statuses` | HTTP durum ifadeleri |

Her paket Publisher’ı ve ortak Library paketini bağımlılık olarak getirir. Manifesti, hangi entegrasyonlar ve sürümler için çeviri yayımlayabileceğini belirtir.

## Paket oluşturma {#create-a-pack}

Çeviri şablonundan başlayın. Paketinizin `composer.json` dosyasına `laravel-lang/publisher` bağımlılığını ekleyin ve desteklediğiniz sürümleri belirtin. Paketiniz ortak çevirilere dayanıyorsa `laravel-lang/library` bağımlılığını da ekleyin. Publisher, uygulamada `vendor/*/*/laravel_lang.json` yolundaki manifestleri arar. [Çeviri şablonu](https://github.com/Laravel-Lang/mona/tree/main/src/TranslationsTemplate)

Manifesti paketinizin kök dizinine yerleştirin. Bu örnek, kurulu her yerel ayar için bir JSON dosyası ve bir PHP çeviri grubu yayımlar.

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

Her `source` yolu, paketin `source/` dizinine göredir. Her `target` yolu ise uygulamanın Laravel dil dizinine göredir ve `{locale}` içerir.

Örneğin `source/audit.json` dosyasında aşağıdaki içeriği kullanın.

```json
{
  "Enable audit trail": "Enable audit trail"
}
```

`source/audit.php` dosyasında ise aşağıdaki içeriği kullanın.

```php
<?php

return [
    'enable' => 'Enable audit trail',
];
```

Fransızca yayımlanırken her ikisi de İngilizce ifadeyi paketinizin `locales/fr/default.json` dosyasında arar.

```json
{
  "Enable audit trail": "Activer le journal d’audit"
}
```

Uygulamada `__('Enable audit trail')` JSON çevirisini, `__('audit.enable')` PHP çevirisini okur. Aynı ifade için paket çevirileri ortak Library girdilerinden önceliklidir. İfade iki sözlükte de yoksa Publisher kaynak değeri kullanır.

Pakete kendi Composer adını verin ve şablondaki `self.version` ifadelerini desteklediğiniz sürüme uygun kısıtlarla değiştirin. Sözlük dizinlerinde standart yerel ayar kodlarını kullanın. Standart çevirileri `default.json`, stil çeşitlerini `inline.json` gibi dosyalara koyun.

### Çevirileri koşullu uygulama {#apply-translations-conditionally}

| Manifest özelliği | Eşlemenin uygulandığı durum |
| --- | --- |
| Koşul yok | Publisher eşlemeyi her zaman dahil eder. |
| `vendor` | Belirtilen Composer paketi kuruludur. |
| `vendor` ve `version` | Kurulu paket Composer sürüm kısıtını karşılar. |
| `vendor` ve `project: true` | Kök projenin Composer adı `vendor` ile eşleşir. Publisher bu kipte adı kontrol eder. |

Bir eşlemenin hem kurulu bir bağımlılığa hem de o adı taşıyan bir kök projeye uygulanması gerekiyorsa iki manifest girdisi kullanın. Eski bir sağlayıcıyı dönüştürmek için [yükseltme kılavuzuna](./upgrade.md), atlama kuralları ve zorunlu paket eşlemesi için [yapılandırmaya](./configure.md) bakın.

### Paketi Laravel uygulamasında test etme {#test-a-pack-in-a-laravel-application}

Paket, test uygulamasıyla aynı üst dizindeyse komutları uygulamanın kök dizininde çalıştırın. `lang/fr.json` ve `lang/fr/audit.php` dosyalarını inceleyin, uygulamanın yerel ayarını `fr` yapın ve iki anahtarı test edin. Paketi değiştirdikten sonra `php artisan lang:update` çalıştırın.

```bash
composer config repositories.audit-translations path ../audit-translations
composer require "acme/audit-translations:*@dev"
php artisan lang:add fr
```
