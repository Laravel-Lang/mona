---
title: "Çevirileri yönetme"
description: "Mona Publisher ile uygulama ve bağımlılık çevirilerini ekleyin, güncelleyin ve kaldırın."
---

Publisher, resmî çeviri paketlerine dahildir. Artisan komutları, PHP ve JSON çevirilerini uygulamanın genellikle `lang/` olan dil dizinine yazar. Pakete özel dosyalar `lang/vendor/` altına da yazılabilir.

Artisan komutlarını uygulamanın kök dizininde çalıştırın. `lang_path()` çeviri dizinini belirler.

## Yerel ayar ekleme {#add-locales}

Bir veya daha çok [yerel ayar kodunu](./available-locales.md) boşlukla ayırarak verin. Yapılandırılmış takma adlar girdi olarak kabul edilir. Oluşturulan dosyalarda standart kodlar kullanılır.

```bash
php artisan lang:add fr de_CH
```

Komut önce yerel ayar dizinlerini oluşturur, ardından uygulamanın geçerli ve yedek yerel ayarları da dahil olmak üzere **tüm kurulu yerel ayarları** günceller. Argüman olmadan çalıştırıldığında tüm desteklenen yerel ayarların kurulup kurulmayacağını sorar. Otomasyonlarda kodları açıkça belirtin.

Bilinmeyen bir kod verilirse komut dizin oluşturmadan önce hata verir.

## Çevirileri güncelleme {#update-translations}

```bash
php artisan lang:update
```

`lang:update` kurulu tüm yerel ayarları günceller ve argüman olarak yerel ayar kodu almaz.

Publisher, uygulamanın geçerli ve yedek yerel ayarlarına, dil dizininin kökündeki dosya veya dizinlere ve `lang/vendor/<package>/` altındaki JSON dosyalarına bakarak yerel ayarları belirler. Tek başına bir vendor PHP çeviri dizini ilgili yerel ayarı kurulu saymak için yeterli değildir. Uygulama dizinini oluşturmak için o yerel ayarla `lang:add` komutunu kullanın.

Çeviri paketleri, desteklenen paket ve sürümleri ve hedef dosyaları [eklenti manifestlerinde](./plugins.md) bildirir. Publisher, ortak sözlüklerle paket sözlüklerini birleştirir ve ardından çevirileri her hedef dosyaya işler. İlgisiz anahtarları korur ve sayısal anahtarlar da dâhil olmak üzere eşleşen anahtarların değerlerini değiştirir. Bir ifade sözlüklerde yoksa kaynak ifadeyi yazar. Kendi değişikliklerinizi ayrı dosyalarda tutun veya bakımını üstlendiğiniz hedefleri [yayım dışında bırakın](./configure.md#skip-files-and-force-integrations).

Oluşturulan PHP çeviri dosyaları yüklendiğinde `404` gibi sayısal anahtarlar korunur. Boş olmayan iç içe PHP dizilerinde her öğe ayrı bir satıra yazılır. JSON çeviri sözlükleri, sayısal anahtarlar ardışık olduğunda da her düzeyde dize anahtarları olan nesneler olarak yazılır.

Publisher, uygulamanın `validation.php` dosyalarında `custom` ve `attributes` üst düzey anahtarlarını, mevcut olduklarında, bu sırayla dosyanın sonuna yerleştirir. Diğer üst düzey anahtarlar alfabetik olarak sıralanır.

Composer bağımlılıklarını veya [yayım ayarlarını](./configure.md) değiştirdikten sonra `lang:update` komutunu çalıştırın. Oluşan değişiklikleri inceleyin.

Projenin yeni bir kopyasında çevirileri yayımlamadan önce `composer install` çalıştırın. Oluşturulan dosyaları uygulamayla birlikte dağıtın veya çeviri dizini yazılabilirken dağıtım sırasında oluşturun. Dosyalar yoksa gereken kodlarla `php artisan lang:add fr de_CH --no-interaction` çalıştırın. Var olan dosyaları `php artisan lang:update --no-interaction` ile güncelleyin.

## Yerel ayar kaldırma {#remove-locales}

```bash
php artisan lang:rm de_CH
```

Standart kodları veya yapılandırılmış takma adları kullanabilirsiniz.

Bu işlem, dil dizininin kökünden ve `lang/vendor/<package>/` biçimindeki her doğrudan paket dizininden standart koda ait yerel ayar dizinini ve JSON dosyasını siler. Silinen yerel ayar dizinlerindeki özel dosyalar da silinir. Geçerli ve yedek yerel ayarlar korunur. Komut argümansız çalıştırıldığında korumasız yerel ayarların tümünün kaldırılıp kaldırılmayacağını sorar. Varsayılan yanıt hayırdır. Yerel ayar dizini bir bağlantıysa komut bağlantının kendisini kaldırır.

Komut her yerel ayar için `DONE`, `PROTECTED`, `INVALID`, `SKIP` veya `FAILED` bildirir. Kaldırma denemesinden sonra bir dosya veya dizin kalırsa komut yolunu gösterir ve hata çıkış kodu döndürür. `PROTECTED`, `INVALID` ve `SKIP` başarılı çıkış koduyla dönebildiği için her yerel ayarın durumunu kontrol edin.

## Artisan komutlarını bulma {#find-the-artisan-commands}

Komutun kayıtlı olup olmadığını `php artisan help lang:add` ile kontrol edin. Komut bulunamazsa paketin kurulu olduğunu `composer show laravel-lang/publisher` ile doğrulayıp `php artisan package:discover` çalıştırın. Config ve Publisher için otomatik keşfi devre dışı bıraktıysanız `LaravelLang\Config\LangConfigServiceProvider::class` ve `LaravelLang\Publisher\LangPublisherServiceProvider::class` sınıflarını `bootstrap/providers.php` içinde kaydedin.

## JSON çevirilerinde yedek dil {#json-fallback}

`laravel-lang/json-fallback`, bir JSON anahtarı bulunamadığında PHP çevirilerine geçmeden önce yedek yerel ayarın JSON dosyasında arama yapılmasını sağlar. Common bu paketi içerir. Ayrı kurmak için `composer require laravel-lang/json-fallback` çalıştırın.

`LaravelLang\JsonFallback\LangJsonFallbackServiceProvider::class` sınıfını `bootstrap/providers.php` dosyasındaki uygulama sağlayıcılarına ekleyin. Uygulama `Illuminate\Translation\TranslationServiceProvider` sağlayıcısını açıkça listeliyorsa bu girdiyi Mona sağlayıcısıyla değiştirin. [Laravel’de sağlayıcı kaydı](https://laravel.com/docs/13.x/providers#registering-providers)

Geçerli dil Fransızca, yedek dil İngilizce olduğunda `lang/fr.json` dosyasında bulunmayan bir anahtar artık `lang/en.json` dosyasındaki değerine çözümlenebilir.

Aramayı istenen yerel ayarla sınırlamak için `Illuminate\Support\Facades\Lang` üzerinden `Lang::get($key, fallback: false)` çağrısını kullanın.
