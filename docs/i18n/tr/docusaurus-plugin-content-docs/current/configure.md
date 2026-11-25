---
title: "Yapılandırma"
description: "Mona’nın çeviri stillerini, yerel ayar takma adlarını, yayım filtrelerini, noktalama işaretlerini, rotalarını ve modellerini yapılandırın."
---

Mona, varsayılan ayarları otomatik olarak yükler. Özelleştireceğiniz dosyaları uygulamanızın `config/` dizinine yayımlayın.

```bash
php artisan vendor:publish --tag=localization_main
```

`localization` etiketi beş dosyanın tümünü yayımlar. `.php` uzantısı çıkarılmış her dosya adı hem bir yayım etiketi hem de bir Laravel yapılandırma anahtarıdır.

| Dosya | Ayarlar |
| --- | --- |
| `localization_main.php` | Çeviri stilleri, takma adlar ve yayım filtreleri |
| `localization_punctuation.php` | Akıllı noktalama ve yerel ayara özel tırnak işaretleri |
| `localization_routes.php` | [Rota adları, yerel ayar kaynakları ve ara katmanlar](./routes.md) |
| `localization_models.php` | [Çeviri modeli son eki, filtreleme ve oluşturma yolları](./models.md) |
| `localization_map.php` | Yazı sistemi, bölge ve yazı yönü gibi yerel ayar meta verileri |

Laravel, üst düzey her ayarı uygulamada belirtilen değerle değiştirir. `localization_punctuation.php` dosyasında `locales` dizisini düzenlerken kullanmaya devam edeceğiniz varsayılan yerel ayar istisnalarını koruyun.

Yapılandırmayı değiştirdikten sonra varsa önbelleği `php artisan config:clear` ile temizleyin. Yayım ayarlarından etkilenen çevirileri yeniden oluşturmak için `php artisan lang:update` çalıştırın.

## Çeviri stilleri {#translation-styles}

Çeviri stili, belirli ifadelerin yazımını değiştiren adlandırılmış bir sözlüktür. Örneğin bir paket, samimi bir ton için `informal.json`, ürünün kendi ifadeleri için `john.json` sunabilir. Stil adları yerel ayar kodlarından, uygulama adlarından ve paket adlarından bağımsızdır. Her çeviri paketi, desteklediği diller için `locales/<locale>/<style>.json` altında stil sunabilir. Bir stil yalnızca bir ifade bile içerebilir. Bu özellik ilk olarak samimi hitap biçimleri için geliştirilmiştir. [Samimi çeviriler tartışması](https://github.com/orgs/Laravel-Lang/discussions/124)

`john` stilini sağlayan eklentiyi kurduktan sonra, her sözlük kaynağında bu stile öncelik vermek için `config/localization_main.php` dosyasındaki listenin sonuna ekleyin.

```php
'styles' => ['default', 'informal', 'john'],
```

`php artisan lang:update` çalıştırıldığında Publisher, kurulu her yerel ayar için stil dosyalarını birleştirir. Her ifade için `john` sözlüğünde karşılık varsa onu, yoksa `informal` sözlüğündeki karşılığı, o da yoksa `default` sözlüğündeki karşılığı kullanır. Örneğin bir Fransızca paket `locales/fr/john.json` içindeki bir ifadeyi değiştirebilirken Almanca aynı ifade için `locales/de/informal.json` veya `locales/de/default.json` dosyasını kullanabilir. Publisher, bu sıralamayı ortak Library sözlüğüne ve dosyayı sağlayan pakete ayrı ayrı uygular. Söz konusu dosyada, paketteki eşleşen ifade Library girdisinden önceliklidir.

Varsayılan ayar `['default', 'inline']` değeridir. `inline` stili, “Bu alan kabul edilmelidir.” gibi genel doğrulama mesajları sunar. Yalnızca standart ifadeleri kullanmak için `styles` ayarını `['default']` olarak belirleyin. `default` listede yoksa Publisher onu başa ekler.

## Yerel ayar takma adları {#locale-aliases}

`localization_main.aliases` ayarını standart kodları takma adlarla eşleyecek şekilde belirleyin. Örneğin `config/localization_main.php` dosyasının döndürdüğü diziye aşağıdaki girdiyi ekleyin.

```php
'aliases' => [
    'de_CH' => 'de-CH',
],
```

Artisan `de-CH` kodunu kabul edebilir, ancak Laravel ve yayımlanan dosyalar `de_CH` kodunu kullanır. `APP_LOCALE` ve `APP_FALLBACK_LOCALE` için standart kodları kullanın. Dönüştürme ve arama örnekleri için [Yerel ayarlar ve adlar](./locales.md) sayfasına bakın.

Takma adların eşleştirilmesinde büyük ve küçük harfler ayırt edilir. Her yerel ayar için başka bir yerel ayarın standart koduyla çakışmayan, benzersiz bir takma ad kullanın.

## Dosyaları atlama ve entegrasyonları zorlama {#skip-files-and-force-integrations}

- `localization_main.skip`, büyük/küçük harfe duyarsız `*` desenlerini `/` ayraçlı tam yollarla eşleştirir. Kaynak dosyaları, sözlükleri ve yayım hedeflerini hariç tutabilir. Örneğin `['*/lang/fr/validation.php']`, Publisher’ın `lang/` kullanan bir uygulamada o dosyaya yazmasını engeller.
- `localization_main.force`, Composer paket adlarını kabul eder. Belirtilen bağımlılık yoksa, kurulu sürüm eşleşmiyorsa veya kök projenin adı farklıysa bile Publisher’ın manifest girdilerini kullanmasını sağlar. Manifestteki çeviriler uygulamanıza uygunsa bu ayarı kullanın.

`skip`, `force` ayarından önceliklidir.

## Akıllı noktalama {#smart-punctuation}

Yayımlanan çevirilerde tipografik tırnak işaretlerini, tireleri ve üç noktaları etkinleştirmek için `LOCALIZATION_PUNCTUATION_ENABLED=true` ayarını kullanın. Genel kurallar için `localization_punctuation.common`, standart yerel ayar koduna göre özel kurallar için `localization_punctuation.locales` ayarını düzenleyin. Yeni kurallar, Publisher metinleri bir sonraki kez yazdığında uygulanır.

## Yerel ayar üst verileri {#locale-metadata}

`localization_map.locales` standart kodları yazı sistemi (`type`), bölgesel yerel ayar (`regional`) ve isteğe bağlı yazım yönüyle (`direction`) eşler. Yön ayarlanmadıysa `Direction::LeftToRight` kullanın.

```php
use LaravelLang\LocaleList\Direction;

$metadata = config('localization_map.locales.ar', []);
$script = $metadata['type'];         // 'Arab'
$regional = $metadata['regional'];  // 'ar_AE'
$dir = ($metadata['direction'] ?? Direction::LeftToRight)->value; // 'rtl'
```
