---
title: "Çeviri araç seti"
description: "Mona’nın yerel kopyasından çeviri kaynaklarını ve sözlükleri yönetin, taşıyın ve içe aktarın."
---

Toolkit, çeviri paketlerinin bakımı için kullanılan komut satırı aracıdır. Kök Composer bağımlılıklarını kurduktan sonra Mona’nın yerel kopyasından çalıştırın. Laravel uygulamasında çeviri yayımlamak için [Artisan komutlarını](./translations.md) kullanın.

```bash
composer install
php src/Toolkit/bin/toolkit list
php src/Toolkit/bin/toolkit migrate-locales --help
```

## Çeviri durumunu kontrol etme {#check-translation-status}

```bash
php src/Toolkit/bin/toolkit status src/Lang
```

Komut varsayılan olarak proje içinde `locales/<locale>/_status.json` dosyasını yazar. Kaydedilen çevirisi kaynak ifadeyle aynı olan girdileri listeler ve stile göre gruplar. `locales/_settings.json` ile `locales/<locale>/_settings.json` dosyalarındaki hariç tutma listelerini birleştirir ve boş raporları siler. Sözlükte hiç bulunmayan ifadeleri bulmak için sözlüğü paketin kaynak dosyalarıyla karşılaştırın.

Raporları `<locale>.json` dosya adlarıyla ayrı bir dizine yazmak için `--output-dir` seçeneğini kullanın. Komut, göreli yolları geçerli çalışma dizinine göre çözümler ve yazılacak bir rapor olduğunda hedef dizini oluşturur.

```bash
php src/Toolkit/bin/toolkit status src/Library --output-dir=workbench/status
```

Bilerek aynı bıraktığınız ifadeleri ilgili `_settings.json` dosyasındaki `excludes` listesine ekleyin.

```json
{
  "excludes": ["Laravel"]
}
```

## Yerel ayar dizinlerini yönetme {#manage-locale-directories}

```bash
php src/Toolkit/bin/toolkit create-locale src/Library
php src/Toolkit/bin/toolkit delete-locale src/Library
```

`create-locale`, tüm standart [yerel ayar kodları](./available-locales.md) için eksik dizinleri oluşturur. Yeni dizinler boş oluşturulur. `delete-locale`, `locales/` altındaki doğrudan alt dizinlerden adı bu listede bulunmayanları siler. Özellikle yerel ayar takma adları veya özel kodlar varsa komutu çalıştırmadan önce hedefi kontrol edin.

## Kullanılmayan sözlük girdilerini kaldırma {#remove-obsolete-dictionary-entries}

```bash
php src/Toolkit/bin/toolkit sync:library src
php src/Toolkit/bin/toolkit sync:package path/to/translation-pack
```

- `sync:library`, `src/` altında `laravel_lang.json` manifesti olan paketlerden kaynak ifadeleri toplar ve `src/Library/locales/` altındaki sözlükleri yeniden yazar.
- `sync:package`, tek bir paketin `source/` dizinini okur ve `locales/` dizinindeki kullanılmayan girdileri kaldırır. Bu işlemden sonra boş kalan sözlükleri siler.

Her iki komut, etkin ifadelerin mevcut çevirilerini ve `_settings.json` gibi alt çizgiyle başlayan dosyaları korur. Yeni ifadelerin çevirilerini doğrudan sözlüğe ekleyin.

Argüman verilmezse `sync:library` çalışma kopyasındaki `src/` dizinini, `sync:package` mevcut çalışma dizinini kullanır. Her ikisi standart yerel ayar koduyla adlandırılmış dizinleri işler.

## Eski sözlükleri taşıma {#migrate-legacy-dictionaries}

```bash
php src/Toolkit/bin/toolkit migrate-locales path/to/legacy-pack --target=path/to/new-pack/locales
```

`source/` ve `locales/` dizinlerini içeren eski bir paket belirtin. Yerel ayar alt dizinlerinin yazılacağı dizini `--target` ile belirleyin. Toolkit varsayılan olarak eski paketin `locales/` dizinini kullanır.

- `php.json` ve `json.json` dosyalarındaki girdiler `default.json` dosyasında birleştirilir.
- `php-inline.json` ve `json-inline.json` dosyalarındaki girdiler `inline.json` dosyasında birleştirilir.
- `_excludes.json` girdileri, hedef yerel ayarın `_settings.json` dosyasındaki `excludes` listesine taşınır.

Toolkit, kaynak ifadeleri zaten anahtar olarak kullanan JSON girdilerini doğrudan taşır. PHP sözlüklerindeki iç içe anahtarları `source/` altındaki PHP dosyalarında arar ve karşılık gelen kaynak ifadeleri anahtar olarak kullanır. Anahtar çakışırsa taşınan çeviri önceliklidir. Her iki eski biçim de aynı ifade için çeviri sağlıyorsa PHP sözlüğü önceliklidir.

Toolkit `source/` dizinindeki dosyaları korur. Girdi aktardığı eski sözlük dosyalarını ve `_excludes.json` dosyalarını, ayrı bir hedef seçildiğinde de siler. Uygulamanız veya eklenti manifestiniz için gereken değişikliklere [yükseltme kılavuzundan](./upgrade.md) bakın.

## ZIP arşivinden kaynak içe aktarma {#import-sources-from-a-zip-archive}

```bash
php src/Toolkit/bin/toolkit download \
  --url=https://github.com/laravel/framework/archive/refs/heads/12.x.zip \
  --directory=workbench/downloads/framework/12.x \
  --paths=src/Illuminate/Translation/lang/en \
  --only-copy
```

- `--directory` son hedefi belirler. Arşivin köküne göre dizinler için `--paths` birden çok kez kullanılabilir.
- Seçilen PHP ve JSON dosyaları, dosya adlarıyla hedefe kopyalanır. PHP dosyaları bayt düzeyinde kopyalanır. Aynı adlı JSON dosyaları birleştirilir ve gelen değerler önceliklidir.
- `--only-copy` olmadan Toolkit, anahtarları değişmez metinler olan desteklenen çeviri çağrılarını tarar. İfade bulursa, örnekteki `framework.json` gibi hedefin üst dizinine göre adlandırılmış bir JSON kaynak dosyası yazar.
- Komut PHP’nin ZIP eklentisini gerektirir. Değişiklikleri yayımdan önce geçici bir alanda hazırlar. Yayım başarısız olursa önceki hedefi geri yüklemeye çalışır.

`--url` ve `--directory` zorunludur. `--paths` ile verilen dizinler özyinelemeli taranır. Yalnızca çeviri anahtarlarını bulmak için `--paths` seçeneğini atlayın. Anahtarları pakete eklemeden önce inceleyin.
