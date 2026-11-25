---
title: "Katkıda bulunma"
description: "Mona’nın yerel kopyasından çevirilere, PHP paketlerine ve belgelere katkıda bulunun."
---

[Mona deposunu](https://github.com/Laravel-Lang/mona) çatallayın ve deponun `main` dalına bir pull request açın. Mona kaynak şablonlarını, ortak çevirileri, PHP paketlerini ve belgeleri içerir. Paket depolarının içeriği Mona’dan aktarılır.

## Çeviri düzeltme veya ekleme {#fix-or-add-a-translation}

1. Paketin `src/<Package>/source/` dizininde İngilizce ifadeyi bulun. Örneğin Laravel Framework ve Laravel’in resmî paketlerinin kaynakları `src/Lang/source/` altında yer alır. Paketin `laravel_lang.json` dosyası, kaynak paketin her sürümü için kullanılan kaynak dosyalarını listeler.
2. `src/Library/locales/<locale>/default.json` dosyasını açın. İngilizce ifadeyi JSON anahtarı olarak bulun ve değerini düzenleyin. Kaynak ifade pakette bulunuyor ancak sözlükte bulunmuyorsa bir girdi ekleyin.
3. Kaynak ifadeyi değiştirmeyin ve `:count`, `:other`, `:attribute` gibi yer tutucuları koruyun. Satır içi doğrulama mesajlarında, alan adını anmayan bir ifadeyle aynı kaynak anahtarı `inline.json` dosyasında kullanın. Diğer yer tutucuları ve çoğul biçimleri koruyun.
4. Çeviri özellikle İngilizce ifadeyle aynı bırakılmışsa ifadeyi yerel ayarın `_settings.json` dosyasındaki `excludes` listesine ekleyin. Toolkit çeviri durumunu hesaplamak için bu listeyi kullanır.
5. Başlığında yerel ayar kodunu içeren bir pull request açın. Örnek başlık `[fr]: Updated validation messages` olabilir.

Başka bir projenin kaynak ifadesinde yazım hatası bulursanız, kaynak metin ve arama anahtarı birlikte güncellenebilsin diye hatayı o projeye bildirin.

`src/Library/locales/fr/default.json` dosyasındaki varsayılan Fransızca girdiye bakın.

```json
{
  "The :attribute field is invalid.": "Le champ :attribute est invalide."
}
```

Aynı ifadenin satır içi karşılığı `src/Library/locales/fr/inline.json` dosyasında yer alır.

```json
{
  "The :attribute field is invalid.": "Ce champ est invalide."
}
```

Mona ile yayımlanan çeviriler için Library’deki `default.json` ve `inline.json` dosyalarını düzenleyin. Bazı paketlerde eski `php.json` ve `json.json` sözlükleri de vardır. Paket durumunu hesaplayan otomatik iş akışı bu dosyaları kullanır.

Ülke, para birimi veya yerel ayar adlarını güncellemek için ilgili paketin `data/` dizinindeki sözlüğü düzenleyin. [Yerel ayarlar ve çevrilmiş adlar](./locales.md) sayfasına bakın.

## Yerel ayar önerme {#propose-a-locale}

[Standart yerel ayar kodlarına](./available-locales.md) bakın. Kod listedeyse `src/Library/locales/` altında sözlüğü ekleyin veya güncelleyin. Toolkit [eksik dizinleri oluşturabilir](./toolkit.md#manage-locale-directories). Yeni bir kod önermek için [Laravel-Lang’de bir tartışma](https://github.com/orgs/Laravel-Lang/discussions/new?category=ideas&title=I%20want%20to%20add%20a%20new%20localization) açın.

## Paket geliştirme {#develop-packages}

Git, Composer 2 ve `intl`, `json`, `zip` uzantılarıyla PHP `^8.3` kullanın. Testler bellek içi veritabanı kullandığı için `pdo_sqlite` uzantısını etkinleştirin. Komutları deponun kök dizininden çalıştırın.

```bash
composer install
composer check-platform-reqs
```

Kökteki Composer projesi `src/<Package>/src/` altındaki kodu otomatik yükler. Yayımlanan her paketin bağımlılıklarını kendi `src/<Package>/composer.json` dosyasında tutun. Paketlerin iç bağımlılıklarındaki `self.version` kısıtı, aynı sürümdeki paketleri birlikte kurmaya olanak verir.

Paket davranışını yerel olarak denemek için `workbench/` altındaki Testbench uygulamasını kullanın. `composer serve` uygulamayı derler ve geliştirme sunucusunu başlatır. Depo kökünden rotaları ve Artisan komutlarını incelemek için Testbench komut satırını kullanın.

```bash
composer serve
```

```bash
php vendor/bin/testbench route:list
php vendor/bin/testbench help lang:update
```

Bağımlılık iş akışı, diğer yerel paketleri gösteren bir Composer `path` deposuyla paketleri ayrı ayrı kurar. Bir paketin bağımlılıklarını değiştirirseniz tek başına kurulabildiğini doğrulayın ve kökteki test paketini çalıştırın. Kurulum adımları ve Laravel sürümleri iş akışında yer alır. [Bağımlılık testi iş akışı](https://github.com/Laravel-Lang/mona/blob/main/.github/workflows/test-dependencies.yml)

## Yerel kontrolleri çalıştırma {#run-local-checks}

Sözlük değişikliklerinde kök dizine JavaScript bağımlılıklarını kurun ve Biome kontrollerini çalıştırın. Toolkit’in `status` komutu, değeri kaynak anahtarıyla aynı olan girdileri raporlar. Sözlükte henüz bulunmayan ifadeler için paketin kaynak dosyalarını inceleyin. Oluşan raporları `git status --short` ile kontrol edin. [Toolkit komutları](./toolkit.md)

```bash
npm install
npx biome check src/Library/locales/fr/default.json
php src/Toolkit/bin/toolkit status src/Library
```

PHP değişiklikleri için depo kökünden Pest ve PHPStan çalıştırın. Model oluşturma testleri Testbench uygulama dizinini paylaştığı için özellik testlerini CI’daki gibi sıralı çalıştırın. Mimari testler paralel çalışabilir. Tek bir özelliği test etmek için ilgili dosyayı veya dizini Pest’e verin.

```bash
php vendor/bin/pest --exclude-group=arch
php vendor/bin/pest --group=arch --parallel
composer test:types
```

Rota testleri Testbench geliştirme ayarları nedeniyle `http://localhost:8000` içeren URL’ler oluşturuyorsa Pest’i çalıştırmadan önce test sürecinde `APP_URL` değerini `http://localhost` yapın.

`composer style` Composer dosyalarını normalleştirir ve Rector ile Pint düzeltmelerini uygular. Pull request göndermeden önce değişiklikleri inceleyin. Satır kapsamı için Xdebug’u etkinleştirin ve `composer run-script --timeout=0 test:coverage` komutunu çalıştırın.

Manifest değişiklikleri için CI’da kullanılan şema doğrulamasını çalıştırın.

```bash
npx --yes ajv-cli@5.0.0 validate --spec=draft2020 -s resources/laravel-lang.schema.json -d 'src/*/laravel_lang.json'
```

Manifest biçimi için [çeviri eklentilerine](./plugins.md) bakın.

## Belgeleri düzenleme {#edit-the-documentation}

Güncel İngilizce sayfalar `docs/docs/` dizininde, çevirileri ise `docs/i18n/<language>/docusaurus-plugin-content-docs/current/` dizinindedir. Yönergeler değiştiğinde ilgili sayfaları güncelleyin ve diller arası bağlantılar için başlık kimliklerini koruyun. Mona öncesi belgeler `docs/versioned_docs/version-legacy/` altında arşivlenir. Çevirileri `docs/i18n/` altındaki ilgili `version-legacy/` dizinlerindedir.

`docs/package.json` dosyasında belirtilen Node.js sürümünü kullanın. Belge komutlarını `docs/` dizininden çalıştırın.

```bash
cd docs
npm ci
npm start
```

Bir çeviriyi önizlemek için `npm start -- --locale fr` çalıştırın. Geliştirme sunucusunu durdurup tüm dillerin bağlantılarını, başlık bağlantılarını ve arama dizinini doğrulamak için üretim derlemesini çalıştırın.

```bash
npm run typecheck
npm run build
npm run serve
```

Aramayı ve oluşturulan Markdown çıktılarını kontrol etmek için `serve` komutunun yazdırdığı adresi açın. Statik site `docs/build/` dizininde oluşturulur.

## Sürüm yayımlama {#publish-a-release}

`main` dalına birleştirme, her paketin alt ağacını kendi deposunun `main` dalına aktarır ve Mona sürüm taslağını günceller. Taslağı inceleyin ve Mona’da ortak sürüm etiketiyle yayımlayın. Yalnızca etiketi oluşturmak sürüm iş akışlarını tetiklemez.

Sürümü yayımlamak, etiketi aktarır ve her paket deposunda karşılık gelen sürümü oluşturur. Belge sitesinin derlemesini ve dağıtımını da tetikler. Yayımdan sonra iki iş akışını da kontrol edin. Paket ayrımlarını önizlemek için `main` dalından **Export Packages** iş akışını `dry-run` seçeneği açık olarak çalıştırın. [Paket aktarımı](https://github.com/Laravel-Lang/mona/actions/workflows/export-packages.yml) · [Belgeler](https://github.com/Laravel-Lang/mona/actions/workflows/docs.yml)
