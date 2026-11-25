---
title: "Mona’ya yükseltme"
description: "Laravel-Lang bağımlılıklarını, yapılandırmayı, yayımlanmış çevirileri ve uygulama entegrasyonlarını yükseltin."
---

Mona, Laravel-Lang paketlerinin birlikte yayımlandığı depodur. Uygulamanızın gerektirdiği Composer paketlerini ayrı ayrı kullanmaya devam edin.

:::info[Neden 20. sürüm?]

Laravel-Lang, `laravel-lang/lang` projesinden doğan bir ekosistem. Paketler farklı zamanlarda ortaya çıktı ve her biri kendi sürüm numaralarıyla, kendi takvimine göre yayımlandı. Yeni ana sürümlere geçişte paketleri uyumlu tutmak giderek daha çok emek gerektiriyordu. Kullanıcıların da hangi sürümleri bir arada kurabileceklerini belirlemesi gerekiyordu.

Birbiriyle uyumlu bağımlılıkları tanımlayarak kurulumu kolaylaştırmak için `laravel-lang/common` paketini oluşturduk. Yine de bir yükseltme, sürüm numaraları birbirinden bağımsız birden fazla paketi kapsayabiliyordu.

Örneğin o dönemde Laravel 11 ve PHP 8.2 kullanan bir uygulamaya bu paketlerin hangi sürümlerini kurardınız?

<details>
<summary>Mona öncesindeki paket sürümleri</summary>

| Paket | Sürüm |
| --- | --- |
| Common | `6.9.0` |
| Actions | `1.13.2` |
| Attributes | `2.16.6` |
| Lang | `15.39.2` |
| MoonShine | `1.9.3` |
| HTTP Statuses | `3.13.2` |
| Currency Names | `1.11.0` |
| Locale Names | `2.10.0` |
| Country Names | `1.8.0` |
| Starter Kits | `1.17.4` |
| Locales | `2.11.0` |
| Models | `1.8.0` |
| Routes | `1.11.0` |
| JSON Fallback | `2.3.0` |
| Locale List | `1.7.0` |
| Config | `1.17.1` (diğer Laravel-Lang paketlerinin kullanmaya devam ettiği 1.x sürümü) ve `2.3.2` (Mona öncesindeki son kararlı sürüm, bu paketlerde nadiren kullanılıyordu) |
| Publisher | `16.9.0` |

</details>

Mona monoreposunda paketleri aynı sürüm numarasıyla birlikte yayımlıyoruz.

En yüksek sürüm numarası Publisher’a aitti, dolayısıyla bir sonraki ana sürümü `17.0` olacaktı. Ekosistemi bir araya getirmek için çok emek verdik ve bu dönüm noktasını işaretlemek için 20. sürümü seçtik. Bunu **Laravel-Lang 2.0** olarak da okuyabilirsiniz. 🙂

:::

:::info[Yerel ayar kodları ve takma adlar]

Laravel-Lang artık yayımlanan dil dosyalarının yolları ve model çeviri satırları dahil olmak üzere sakladığı değerlerde standart yerel ayar kodları kullanır. Laravel’in etkin ve yedek yerel ayarları da standart kodları kullanır. Çalışma zamanında URL bölümlerinde ve dil seçicilerde takma adları kullanın. Yerel ayar kodu döndüren facade metotları, varsayılan olarak yapılandırılmış takma adı, takma ad yoksa standart kodu kullanır. Örneğin çevirileri `lang/pt_BR.json` dosyasına yazıp veritabanında `pt_BR` saklarken `/pt-br/account` adresini korumak için `localization_main.aliases` ayarına `'pt_BR' => 'pt-br'` ekleyin.

:::

## Bağımlılıkları güncelleme {#updating-dependencies}

Mevcut sürüm Laravel 12 ve 13’ü destekler ve PHP `^8.3` gerektirir. Uygulamanızın `composer.json` dosyasında, aşağıdaki paketlerden `require` veya `require-dev` altında yer alanların her biri için sürüm kısıtını `^20.0` olarak belirleyin. Yalnızca `laravel-lang/common` kurduysanız sadece onun kısıtını değiştirmeniz gerekir.

`20.x` sürümünü kurmadan veya bu sürüme yükseltmeden önce gereken Composer paketi sürümünün Packagist’te bulunduğunu doğrulayın. Mona sürümü ve karşılık gelen Composer paketleri farklı zamanlarda yayımlanabilir.

| Kullanım | Composer paketleri |
| --- | --- |
| Paket grubu | `laravel-lang/common` |
| Çeviri paketleri | `laravel-lang/lang`, `laravel-lang/starter-kits`, `laravel-lang/moonshine`, `laravel-lang/actions`, `laravel-lang/attributes`, `laravel-lang/http-statuses` |
| Yayım ve yapılandırma | `laravel-lang/publisher`, `laravel-lang/config`, `laravel-lang/json-fallback`, `laravel-lang/library` |
| Uygulama entegrasyonları | `laravel-lang/locales`, `laravel-lang/locale-list`, `laravel-lang/routes`, `laravel-lang/models` |
| Ortak yardımcı paketler | `laravel-lang/exceptions`, `laravel-lang/filesystem` |
| Paket şablonu | `laravel-lang/translations-template` |

Kurulumda yalnızca standart çeviri iş akışını kullanıyorsanız bağımlılıkları güncelleyin ve çevirilerinizi yeniden yayımlayın.

```bash
composer update
php artisan lang:update
```

Uygulamanızda sağlayıcı kaydettiyseniz, özel ayarlar yayımladıysanız, takma ad veya üçüncü taraf çeviri paketleri kullanıyorsanız ya da paket API’lerini çağırıyorsanız çevirileri yeniden yayımlamadan önce aşağıdaki ilgili bölümleri izleyin.

## Adı değişen veya artık kullanılmayan bağımlılıkları değiştirme {#replace-renamed-or-retired-dependencies}

`composer update` çalıştırmadan önce `composer.json` dosyasında eski ad paketlerine olan doğrudan bağımlılıkları güncelleyin.

| Önceki Composer paketi | `^20.0` kısıtıyla eklenecek paket |
| --- | --- |
| `laravel-lang/native-country-names` | `laravel-lang/country-names` |
| `laravel-lang/native-currency-names` | `laravel-lang/currency-names` |
| `laravel-lang/native-locale-names` | `laravel-lang/locale-names` |

Common üç ad paketine de zaten bağımlıdır. PHP kodunuzu da güncelleyin. [Ad sorgulama API’lerini değiştirme](#replace-the-name-lookup-apis) bölümüne bakın.

Her üçüncü taraf eklentisinde yeni Publisher ile uyumlu sürüm olup olmadığını kontrol edin. `laravel-lang/translator` veya `laravel-lang/status-generator` kullanıyorsanız bu bağımlılıkları kaldırmadan önce ilgili kullanımları taşıyın. [Çeviri araçlarına](#update-json-fallback-and-translation-tooling) bakın.

## Servis sağlayıcıları güncelleme {#update-service-providers}

Laravel, paket manifestlerinden Config, Publisher, Routes ve Models sağlayıcılarını keşfeder. Otomatik keşfi kullanıyorsanız eski sağlayıcı sınıflarına başvuran uygulama kodunu ve testleri güncelleyin. Uygulamanızda paketleri elle kaydediyorsanız `bootstrap/providers.php` veya `config/app.php` gibi aynı sağlayıcı listesinde aşağıdaki değişiklikleri yapın.

- `LaravelLang\Config\ServiceProvider` → `LaravelLang\Config\LangConfigServiceProvider`
- `LaravelLang\Publisher\ServiceProvider` → `LaravelLang\Publisher\LangPublisherServiceProvider`
- `LaravelLang\Routes\ServiceProvider` → `LaravelLang\Routes\LangRoutesServiceProvider`
- `LaravelLang\Models\ServiceProvider` → `LaravelLang\Models\LangModelsServiceProvider`

`LaravelLang\Locales\LaravelLangLocalesServiceProvider` ile `LaravelLang\Actions`, `LaravelLang\Attributes`, `LaravelLang\HttpStatuses`, `LaravelLang\Lang`, `LaravelLang\MoonShine` ve `LaravelLang\StarterKits` altındaki eski `ServiceProvider` sınıflarının elle eklenmiş kayıtlarını kaldırın. Bu çeviri paketleri manifestleri üzerinden keşfedilir.

JSON çevirilerinde yedek dil kullanıyorsanız, kaydettiğiniz yerde `LaravelLang\JsonFallback\TranslationServiceProvider` yerine `LaravelLang\JsonFallback\LangJsonFallbackServiceProvider` kullanın. Böylece Laravel’in çeviri sağlayıcısının geçersiz kılınması etkin kalır.

Paket keşfinin uygulamayı başlatabilmesi için `composer update` çalıştırmadan önce eski sağlayıcı başvurularını güncelleyin. Bir Composer betiği eski bir sınıfı yüklediği için hâlâ başarısız oluyorsa `composer update --no-scripts` çalıştırın, başvuruları güncelleyin ve keşfi yeniden çalıştırmak için `composer dump-autoload` komutunu kullanın.

## Yapılandırmayı taşıma {#migrate-configuration}

`config/localization.php` dosyasını özelleştirdiyseniz değişiklikleri taşırken dosyayı başvuru kaynağı olarak tutun. Paketler yeni varsayılanları otomatik yükler. Düzenlemeniz gereken [yapılandırma dosyalarını](./configure.md) yayımlayın. `localization` etiketi beş dosyayı birden yayımlar. Tek dosya için `.php` uzantısı olmadan dosya adını etiket olarak kullanın.

```bash
php artisan vendor:publish --tag=localization
```

### Takma adlar, çeviri stilleri ve dosya biçimlendirme {#aliases-translation-styles-and-file-formatting}

- `aliases` eşlemenizi `config/localization_main.php` dosyasına kopyalayın. `'de_CH' => 'de-CH'` örneğindeki gibi standart kodları anahtar, takma adları değer olarak tutun.
- `config/localization_main.php` dosyasında `inline` yerine `styles` kullanın. Standart doğrulama mesajlarını kullanan önceki varsayılanı korumak için `'styles' => ['default']` olarak ayarlayın. Satır içi mesajları kullandıysanız yeni varsayılan olan `['default', 'inline']` değerini koruyun. Ayarladığınız ortamlardan `LOCALIZATION_INLINE` ve `LANG_PUBLISHER_INLINE` değişkenlerini kaldırın.
- `align` anahtarını ve `LOCALIZATION_ALIGN` ile `LANG_PUBLISHER_ALIGN` ortam değişkenlerini kaldırın. Yayımlanan PHP dizilerindeki `=>` işleçlerini hizalamak için projenizin PHP kod biçimlendiricisini yapılandırın ve yayımdan sonra dil dosyaları üzerinde çalıştırın. Örneğin Laravel Pint’in `binary_operator_spaces` kuralı `=>` için `align_single_space_minimal` uygulayabilir.

### Akıllı noktalama {#smart-punctuation}

Akıllı noktalamayı etkinleştirdiyseniz `LOCALIZATION_SMART_ENABLED` adını `LOCALIZATION_PUNCTUATION_ENABLED` olarak değiştirin ve değeri `true` olarak bırakın. `smart_punctuation.enable` ayarını doğrudan PHP’de belirlediyseniz boolean değeri `config/localization_punctuation.php` dosyasındaki en üst düzey `enabled` anahtarına taşıyın. Akıllı noktalama varsayılan olarak kapalıdır.

Özelleştirdiğiniz tırnak işaretlerini `smart_punctuation.common` ve `smart_punctuation.locales` ayarlarından aynı dosyanın en üst düzeyindeki `common` ve `locales` dizilerine taşıyın. `locales` dizisinde standart kodları anahtar olarak kullanın. Her yerel ayarın tırnak işaretleri `common` içindeki ilgili anahtarları geçersiz kılar. `&nbsp;` kullanan eski bir Fransızca örneği kopyaladıysanız tırnak işareti metinlerinde gerçek bir bölünmez boşluk veya `"\u{00A0}"` kullanın.

### Yerelleştirilmiş rotalar {#localized-routes}

Eski yapılandırmanın `routes` bölümündeki özelleştirmelerinizi `config/localization_routes.php` dosyasına taşıyın.

| Özelleştirdiğiniz ayar | `config/localization_routes.php` dosyasındaki değişiklik | Yeniden adlandırılacak ortam değişkeni |
| --- | --- | --- |
| Yerel ayar parametresi, başlık, çerez, oturum anahtarı veya kullanıcı modeli sütunu | `names` altındaki özelleştirmelerinizi kopyalayın ve diğer varsayılan girdileri koruyun. | |
| Yerelleştirilmiş rota adlarına eklenen önek | `name_prefix` ayarını `prefix` olarak taşıyın. Değer boşsa `localized.` kullanılır. | `LOCALIZATION_NAME_PREFIX` → `LOCALIZATION_PREFIX` |
| Önekli rotadan yönlendirmeler | `redirect_default` ayarını `redirect` olarak taşıyın. | `LOCALIZATION_REDIRECT_DEFAULT` → `LOCALIZATION_REDIRECT` |
| URL yardımcı işlevinin yedek yerel ayarı gizleyip gizlememesi | `hide_default` ayarını `hide` olarak taşıyın. | `LOCALIZATION_HIDE_DEFAULT` → `LOCALIZATION_HIDE` |
| Yerelleştirilmiş rota gruplarına uygulanan ara katmanlar | Öneksiz rotalar için `group.middlewares.default` ayarını `middlewares.default`, önekli rotalar için `group.middlewares.prefix` ayarını `middlewares.prefix` olarak taşıyın. Ara katman sınıfı adlarını [rota geçişinde](#update-localized-routes-and-middleware) gösterildiği gibi güncelleyin. | |

### Model çevirileri {#model-translations}

`models` altındaki özelleştirmelerinizi `config/localization_models.php` dosyasına taşıyın. Özel `suffix` ve `helpers` değerlerini en üst düzeye kopyalayın. Eski `filter.enabled` anahtarı yerine boolean `filter` kullanın. Filtrelemeyi ve IDE yardımcı dosyası yolunu ortam değişkenlerinden denetliyorsanız `LOCALIZATION_FILTER_ENABLED` ile `VENDOR_PATH` değişkenlerini koruyun.

Model komutları artık varsayılan olarak `app/Models` dizinini tarar. Önceki tüm `app/` taramasına dayanıyorsanız veya `localization-private.models.directory` ayarını kendiniz belirlediyseniz taramak istediğiniz her dizini `directories` dizisine ekleyin. Örneğin önceki taramayı korumak için `'directories' => [app_path()]` olarak ayarlayın.

### Uygulama kodunun kullandığı yapılandırma {#configuration-used-by-application-code}

- `LaravelLang\Config\Facades\Config` üzerinden okuma ve yazma işlemlerini, yeni anahtarlarla çalışan Laravel yapılandırma çağrılarıyla değiştirin. Örneğin `Config::shared()->models->filter->enabled` yerine `config('localization_models.filter')` kullanın. Yapılandırmayı tür bilgisiyle okumayı tercih ediyorsanız `LaravelLang\Config\Services\ModelLangConfig` enjekte edip `filter` özelliğini okuyun. Rota ayarları için aynı ad alanındaki `RouteLangConfig` sınıfını enjekte edebilirsiniz.
- Kodunuz yerel ayar meta verilerini `localization-private.map` üzerinden okuyorsa artık `localization_map.locales` ayarını okuyun. Özelleştirmeleri `config/localization_map.php` dosyasındaki `locales` dizisine kopyalayın.
- `localization-private.plugins` ve `localization-private.packages` üzerinden kayıt yerine [eklenti manifesti](./plugins.md) kullanın.
- Eski `translators` bloğunu ve yalnızca `laravel-lang/translator` tarafından kullanılan eski `TRANSLATION_*` ayarlarını kaldırın.

Özelleştirmeleri taşıyıp uygulama başvurularını güncelledikten sonra `config/localization.php` dosyasını kaldırın ve `php artisan config:clear` çalıştırın.

## Dil dosyalarını standart yollara taşıma {#move-language-files-to-canonical-paths}

1. `APP_LOCALE` ve `APP_FALLBACK_LOCALE` değerlerini standart kodlara ayarlayın. URL’lerin ve yerel ayar girdilerinin çalışmaya devam etmesi için takma adları `localization_main.aliases` altına taşıyın.
2. `lang/pt-br.json` ve `lang/pt-br/` gibi takma adlı dosya ve dizinleri `lang/pt_BR.json` ve `lang/pt_BR/` olarak yeniden adlandırın. `lang/vendor/<package>/` altında aynı takma adları kontrol edin. Her iki ad da varsa takma adlı yolu kaldırmadan önce içerikleri birleştirin ve çakışan özel çevirileri çözümleyin.
3. Yalnızca vendor PHP çevirileriyle temsil edilen her yerel ayarı `php artisan lang:add <locale>` ile kurun. Kurulu yerel ayarlar listesi, doğrudan dil dizinindeki girdileri, `lang/vendor/<package>/` altındaki JSON dosyalarını ve etkin geçerli ve yedek yerel ayarları kullanır.
4. Yapılandırmayı ve eklentileri taşıdıktan sonra `php artisan lang:update` çalıştırın, ardından değişiklikleri gözden geçirin. Publisher, artık dosya yayımlamayan entegrasyonlara ait anahtarları koruyabilir. Uygulamanızın kullanmaya devam ettiği anahtarları saklayın. Kullanan kodu kontrol ettikten sonra eski anahtarları kaldırın. Dosyaları yeniden oluşturmak veya korumak için [yayım betikleri ve özel çeviriler](#update-publication-scripts-and-keep-custom-translations) bölümüne bakın.

## Entegrasyonları güncelleme {#update-integrations}

### Yerel ayarlar {#locales}

`LaravelLang\Locales\Facades\Locales` içe aktarımını koruyun ve çağrıları, çağıran kodun kullandığı verilere göre güncelleyin.

| Önceki çağrı | Yeni kullanım |
| --- | --- |
| Nesne döndüren `Locales::available()`, `installed()` veya `protects()` | Aynı metodu `Locales::data()` üzerinde çağırın. |
| `Locales::raw()->available()`, `installed()` veya `protects()` | Yerel ayarları metin koleksiyonu olarak almak için aynı metodu doğrudan `Locales` üzerinde çağırın. Her metin, varsa yapılandırılmış takma addır, yoksa standart koddur. Her durumda standart kod almak için `false` verin. Çağıran kod bir dizi gerektiriyorsa `->all()` çağırın. |
| `Locales::installed(false)` veya `Locales::raw()->installed(false)` | `false` artık standart kodları seçer. Dil dosyaları bulunmasa bile geçerli ve yedek yerel ayarlar dahil edilir. Eski `withProtects: false` adlı argümanını da değiştirin. Bu yerel ayarları hariç tutmak için `Locales::installed(false)->diff(Locales::protects(false))->values()` kullanın. Nesneler için `Locales::data()->installed()->reject(fn ($locale) => Locales::isProtected($locale))->values()` kullanın. |
| `Locales::notInstalled()` veya `Locales::raw()->notInstalled()` | Nesneler için `Locales::data()->uninstalled()`, yerel ayar metinleri için `Locales::uninstalled()` kullanın. Her metin, varsa yapılandırılmış takma addır, yoksa standart koddur. Her durumda standart kod almak için `false` verin. |
| `Locales::getCurrent()` veya `Locales::getDefault()` | DTO için `Locales::data()->current()`, metin için `Locales::current()` kullanın. `Locales::raw()->getDefault()` yerine `Locales::current()` kullanın. Laravel’in tuttuğu değeri almak için `Locales::raw()->getCurrent()` çağırıyorduysanız `app()->getLocale()` kullanın. |
| `Locales::getFallback()` veya `Locales::raw()->getFallback()` | DTO için `Locales::data()->fallback()`, metin için `Locales::fallback()` kullanın. Bilinmeyen bir yedek yerel ayar artık İngilizceye çözümlenir. Bilinmeyen etkin yerel ayar, kullanılan yedek yerel ayara çözümlenir. |
| `Locales::get($locale)` veya `Locales::raw()->get($locale)` | `isInstalled()` kullanın ve yedek yerel ayarı açıkça seçin. Kurulu olmayabilecek, desteklenen bir yerel ayar için önce `isAvailable()`, ardından `info()` kullanın. |
| `Locales::info($locale)` veya `Locales::raw()->info($locale)` | `Locales::info($locale)` kullanın ve metin gerekiyorsa `->alias` veya `->code` okuyun. Geçerli yerel ayar için `null` verin. Bilinmeyen veya boş kod için `LaravelLang\Exceptions\UnknownLocaleException` hatasını işleyin ya da sorgulamadan önce `isAvailable()` ile kontrol edin. |

Kurulu olmayan veya geçersiz girdide geçerli yerel ayara dönen eski `get()` davranışını korumak için bu seçimi çağıran kodda yapın.

```php
use LaravelLang\Locales\Facades\Locales;

$locale = Locales::acceptsInput($requested) && Locales::isInstalled($requested)
    ? Locales::info($requested)
    : Locales::data()->current();

$code = $locale->code;   // Dosya yollarında, veritabanı satırlarında ve kalıcı diğer verilerde kullanın.
$alias = $locale->alias; // URL’lerde ve dil seçicilerde kullanın.
```

Daha önce `raw()->get()` sonucunu `app()->setLocale()` çağrısına veren istek işleyicilerini güncelleyin. İstek değerini kabul ettikten sonra `Locales::set($locale)` kullanın. Bu metot kurulu yerel ayar kodlarını, yapılandırılmış takma adları, enum değerlerini ve yeni DTO’yu kabul eder, ardından Laravel’e standart kodu atar. İstek değeri `pt-br` olduğunda Laravel `pt_BR` alırken `Locales::current()`, `pt-br` döndürür. Standart kod gerektiğinde `Locales::current(false)` veya `Locales::fallback(false)` çağırın.

Uygulamanızda değeri başka bir yerde saklayacaksanız önce `isAvailable()` veya `isInstalled()` ile doğrulayın, ardından standart kodu çözümlemek için `fromAlias()` kullanın. Standart kodu görüntülemek üzere biçimlendirmek için `toAlias()` kullanın. Eski `Services\Resolver` kullanımını, kabul edilen PHP türlerini kontrol etmek için `acceptsInput()` veya belirli bir türdeki değerden metin çıkarmak için `normalize()` ile değiştirin. `normalize()`, `pt-br` gibi takma adlar dahil olmak üzere metinleri verildiği gibi korur.

İçe aktarımlarda, tür bildirimlerinde ve testlerde `LaravelLang\Locales\Data\LocaleData` yerine `LaravelLang\LocaleNames\LocaleData` kullanın. Yeni salt okunur nesnenin `code`, `alias`, `native` ve `localized` adlı metin özellikleri vardır. URL ve arayüz değerlerinde eski `code` özelliği yerine `alias` kullanın. Daha önce `$data->locale` okuduğunuz yerlerde `LaravelLang\LocaleList\Locale` sınıfını içe aktararak `Locale::from($data->code)` kullanın. Şablonlarınız veya biçimlendiricileriniz kullanıyorsa `type`, `regional` ve `direction` değerlerini `config("localization_map.locales.{$data->code}", [])` çağrısından okuyun. Eksik girdiler için kendi varsayılan değerlerinizi verin. Eski varsayılanları korumak için yazı sistemi olarak `Latn`, bölge olarak standart kod ve yön olarak `Direction::LeftToRight` kullanın.

`withCountry`, `withCurrency`, `withCountries` ve `withCurrencies` bayraklarını kaldırın. Yerel ayar satırı için ülkeyi ve para birimini açıkça isteyin. Eski `LaravelLang\Locales\Data\CountryData` ve `CurrencyData` türlerinin içe aktarımlarını ilgili ad paketlerindeki veri sınıflarıyla değiştirin.

```php
use LaravelLang\CountryNames\CountryNames;
use LaravelLang\CurrencyNames\CurrencyNames;
use LaravelLang\Locales\Facades\Locales;

$locale = Locales::info('pt_BR');
$displayLanguage = Locales::current(false);

$country = app(CountryNames::class)->one($displayLanguage, $locale->code);
$currency = app(CurrencyNames::class)->one($displayLanguage, $locale->code);
```

Eski serviste `localizedLocales()`, `localizedCountries()` veya `localizedCurrencies()` çağırıyorduysanız, adları kendi dillerinde almak için ilgili ad servisinde `all(null)`, yerelleştirilmiş adları almak için `all($displayLanguage)` kullanın. Önceki `Services\Locales` veya `Services\RawLocales` sınıflarının doğrudan enjekte edilmesi yerine facade’ı veya ilgili `Services\MainService` ve `Services\DataService` sınıflarını kullanın. Yeni servis API’leri için [Yerel ayarlar ve adlar](./locales.md) sayfasına bakın.

#### Yerel ayar enum yardımcıları {#locale-enum-helpers}

`Locale` ve `Direction` enum değerleri aynı kalır. Özel yerel ayar listelerini veya seçicileri yükseltirken `archtechx/enums` paketinden kaldırılan yardımcıların kullanımlarını değiştirin.

| Önceki yardımcı | Yeni kullanım |
| --- | --- |
| `names()` | `array_column(Locale::cases(), 'name')` |
| `options()` | `array_column(Locale::cases(), 'value', 'name')` ile addan değere eşleme oluşturun. |
| `stringOptions()` | Görünümde `Locale::cases()` üzerinden seçenekler oluşturun. Standart kod için enum öğesinin değerini, görüntülenecek takma ad için `Locales::toAlias($case)` kullanın. |
| `fromName()` veya `tryFromName()` | `Locale::cases()` içinde `name` ile enum öğesini bulun ve eşleşme bulunmaması durumunu işleyin. Enum öğesini `pt_BR` gibi altta yatan bir skaler değerden bulmak için `Locale::from()` veya `Locale::tryFrom()` kullanın. |
| `Locale::onlyValues()` | `array_column(Locale::cases(), 'value', 'value')` ile değerden değere eşleme oluşturun. Değer listesi için `Locale::values()` kullanın. |
| `Direction::values()` | `array_column(Direction::cases(), 'value')` kullanın. |

### Ad sorgulama API’lerini değiştirme {#replace-the-name-lookup-apis}

`LaravelLang\NativeCountryNames`, `LaravelLang\NativeCurrencyNames` ve `LaravelLang\NativeLocaleNames` içe aktarımlarını `LaravelLang\CountryNames`, `LaravelLang\CurrencyNames` ve `LaravelLang\LocaleNames` olarak güncelleyin. `CountryNames`, `CurrencyNames` veya `LocaleNames` servisini Laravel konteynerinden çözümleyin ya da sınıfınıza enjekte edin.

| Önceki kullanım | Yeni kullanım |
| --- | --- |
| `SomeNames::get($displayLocale)` | `$names->all($displayLocale)` |
| Kendi dillerindeki adlar için `SomeNames::get()` | `$names->all(null)` |
| `SortBy::None` | Sıralama argümanını vermeyin. |
| `SortBy::Key` | Dönen koleksiyonda `->sortKeys()` çağırın. |
| `SortBy::Value` | Yerelleştirilmiş ada göre sıralamak için `all()` metoduna `SortDirection::Ascending` verin. |

Bilinen bir girdiyi almak için `one($displayLocale, $entryLocale)` kullanın. Arama anahtarı `fr` gibi bir yerel ayardır. Örneğin `app(CountryNames::class)->one('de', 'fr')`, Fransızca yerel ayarının ülkesini Almanca adıyla ve `code` değeri `FR` olarak döndürür. Ülke ve para birimi DTO’ları ait oldukları paketin kök ad alanındadır ve arama anahtarını `locale` olarak sunar. `code` alanları ülke veya para birimi kodunu tutmaya devam eder.

Üç servis de standart yerel ayar koduyla anahtarlanan koleksiyonlar döndürür. Eski yerel ayar adları paketinden metin dizisini kullanıyorduysanız her DTO’yu `localized` değeriyle eşleyin. Eski ülke veya para birimi DTO’larında `toArray()` çağırdıysanız ya da özelliklerini değiştirdiyseniz uygulamanın kullanacağı diziyi açıkça oluşturun. Yeni DTO’lar salt okunurdur.

```php
use LaravelLang\LocaleNames\LocaleData;
use LaravelLang\LocaleNames\LocaleNames;

$options = app(LocaleNames::class)
    ->all('de', SortDirection::Ascending)
    ->map(fn (LocaleData $locale): string => $locale->localized)
    ->all();
```

`one()` çağrısından önce kullanıcının seçtiği yerel ayarları ilgili kataloğa göre doğrulayın. Bilinmeyen görüntüleme dili, `all()` çağrısının boş liste döndürmesine neden olabilir. Uygulamanın adları kendi dillerinde alması gerekiyorsa `null` verin. Kodunuz size ait bir değer destekli enum kullanıyorsa enum’un metin değerini verin. [Yerel ayarlar ve adlar](./locales.md)

### Yerelleştirilmiş rotaları ve ara katmanları güncelleme {#update-localized-routes-and-middleware}

Yerelleştirilmiş URL oluşturan PHP dosyalarında ve Blade şablonlarında ad alanına sahip yardımcı işlevi içe aktarın. `\LaravelLang\Routes\localizedRoute()` işlevini doğrudan da çağırabilirsiniz.

```php
use LaravelLang\Locales\Facades\Locales;

use function LaravelLang\Routes\localizedRoute;

$url = localizedRoute('products.show', [
    'product' => 42,
    'locale' => Locales::current(),
]);
```

`Route::localizedGroup()` ve `LocalizationRoute::group()` hem öneksiz rotaları hem yerel ayar önekli rotaları kaydeder. Mevcut grubu koruyun ve URL yardımcı işlevine yapılan çağrıları gözden geçirin. Yerel ayar parametresi atlanırsa işlev yedek yerel ayarın takma adını kullanır. Dil seçicisinde istenen takma adı `Locales::toAlias($locale)` veya `$localeData->alias` ile açıkça verin. Verilen yerel ayar metni olduğu gibi kullanılır. Parametrenin adını değiştirdiyseniz dizi anahtarı `localization_routes.names.parameter` ile eşleşmelidir.

Rotalarınız, `bootstrap/app.php` ve özel ara katman listelerinizdeki içe aktarım ve kayıtları değiştirin. Tablodaki tüm sınıflar `LaravelLang\Routes\Middlewares` ad alanındadır.

| Önceki ara katman | Yeni ara katman |
| --- | --- |
| `LocalizationByCookie` | `LocalizationByCookieMiddleware` |
| `LocalizationByHeader` | `LocalizationByHeaderMiddleware` |
| `LocalizationByModel` | `LocalizationByModelMiddleware` |
| `LocalizationBySession` | `LocalizationBySessionMiddleware` |
| `LocalizationByParameter` | `LocalizationByParameterMiddleware` |
| `LocalizationByParameterWithRedirect` | `LocalizationByParameterWithRedirectMiddleware` |
| `LocalizationByParameterPrefix` | `LocalizationByParameterPrefixMiddlewareMiddleware` |

Model ara katmanını bir guard ile oluşturduysanız guard’ı Laravel ara katman parametresi olarak verin. Örneğin `LocalizationByModelMiddleware::class . ':admin'` kullanın. Kullanıcı çözümleyicisi bir Eloquent modeli bekler. Özel ara katman alt sınıflarında `LaravelLang\Config\Services\RouteLangConfig` enjekte edin, kaynak adlarını `$this->routeConfig->name` üzerinden okuyun ve `detect()` metodundan `int|string|null` döndürün. Doğrudan `LaravelLang\Routes\Services\Route` başvurularını `LaravelLang\Routes\Services\RouteService` ile değiştirin.

Uygulamanızın kullandığı her kaynağı test edin. Başlıktan algılama, PHP Intl ile kurulu dilleri `Accept-Language` tercih sırasına göre denetler. Önce tam dil etiketini, ardından temel dili dener. Daha sonra çalışan bir ara katman, önceki kaynağın seçtiği yerel ayarı geçersiz kılabilir. Ön ekli rotalarda URL’nin belirleyici olması için bu rotaların zincirinde yalnızca URL’den yerel ayarı seçen ara katmanı bırakın.

URL oluşturmayı ve istekleri ayrı ayrı test edin. `localization_routes.hide`, URL oluşturulurken yedek yerel ayar bölümünü çıkarır. `localization_routes.redirect`, geçerli yerel ayara yapılan önekli istekleri öneksiz rotaya yönlendirir. Adlandırılmış önekli rota, geçersiz veya kurulu olmayan bir yerel ayarı da öneksiz rotaya yönlendirir.

`LocaleHasBeenSetEvent` dinleyicilerinde standart kod için `$event->locale->code`, görüntüleme için `$event->locale->alias` kullanın. Olayın taşıdığı veri artık `LaravelLang\LocaleNames\LocaleData` türündedir. Ara katman, seçilen yerel ayar değiştiğinde olayı gönderir ve standart kodu oturuma kaydeder. Elle yapılan `LocaleHasBeenSetEvent::dispatch($data)` çağrılarını `event(new LocaleHasBeenSetEvent($data))` ile değiştirin. [Yerelleştirilmiş rotalar](./routes.md)

### Model çevirisi kodunu ve verilerini taşıma {#migrate-model-translation-code-and-data}

Her çeviri tablosunun `locale` sütununda takma adla kaydedilmiş satır olup olmadığını kontrol edin. Her takma ad için ilgili satırları bir veri migration’ıyla standart koda güncelleyin. Güncellemeden önce aynı `item_id` için standart kodlu bir satır olup olmadığını kontrol edin. Benzersiz `(item_id, locale)` kısıtını karşılamak için saklamak istediğiniz değerleri tek bir satırda birleştirin. Eski trait’in bazı sürümleri zaten standart kodları yazıyordu, bu nedenle taşıma işlemini tablolarınızda gerçekten saklanan değerlere göre yapın.

İçe aktarımları ve oluşturulan sınıfları güncelleyin.

| Önceki kullanım | Yeni kullanım |
| --- | --- |
| `LaravelLang\Locales\Data\LocaleData` | `LaravelLang\LocaleNames\LocaleData` |
| `LaravelLang\Models\Casts\TrimCast` | `LaravelLang\Models\Casts\TrimStringCast` |
| `LaravelLang\Models\Exceptions\UnavailableLocaleException` | `LaravelLang\Exceptions\UnknownLocaleException` |
| `LaravelLang\Models\Exceptions\AttributeIsNotTranslatableException` veya `UnknownModelPathException` | `LaravelLang\Exceptions` ad alanında aynı sınıf adı. |
| `translationModelName()` | `translationModel(): string`. Özel bir çeviri sınıfı seçen geçersiz kılmaları güncelleyin. |
| `getTranslationTable()` | Tam nitelikli sütun adı için `translationQualifyColumn($column)`, tablo adı için `translationsRaw()->getRelated()->getTable()` kullanın. |

`getTranslation()`, `setTranslation()`, `hasTranslated()` ve `forgetTranslation()` bir yerel ayar metnini, `Locale` veya yeni `LocaleData` türünü kabul eder. Bu metotları çağırmadan önce açıkça verilen her yerel ayarı kurun. Yerel ayar açıkça verilmeden okuma yapılırsa geçerli değer `null` olduğunda yedek yerel ayar kullanılır. Yerel ayar açıkça verildiğinde okuma işlemi doğrudan o yerel ayarın değerini döndürür. Daha alt düzeydeki `translation()` metodunu çağırıyorsanız yerel ayar metni veya `null` verin. DTO’yu `$locale->code` ile dönüştürün.

```php
$article->setTranslation('title', 'Schweizer Titel', 'de-CH')->save();
$article->translation('de_CH');
```

`de_CH` → `de-CH` takma adı ve İsviçre Almancası kurulu olduğunda her iki çağrı da `de_CH` olarak saklanan satırı kullanır. Çevrilen alana birden çok değer atarken `['en' => 'Title', 'de-CH' => 'Schweizer Titel']` gibi yerel ayarla anahtarlanan bir eşleme verin. Sayısal indisli listeler artık `InvalidLocaleInputException` hatasına yol açar. Yüklenen `translations` ilişkisini ayarlayan özel kod, çeviri modellerinden oluşan bir Laravel koleksiyonu sağlamalıdır.

`translated()`, `whereTranslation()` ve ilgili kapsamları Eloquent sorgu oluşturucu üzerinden çağırın. Kapsamın yerel ayar metni olarak kodu veya takma adı verin. `orderByTranslation()` için `'asc'`, `'desc'` veya `SortDirection` kullanın. `whereTranslation()` veya `whereTranslationLike()` ile geçerli ya da yedek yerel ayar dışında bir ayarı sorgularken `localization_models.filter` ayarını `false` yapın veya doğrudan `translationsRaw()` ilişkisini sorgulayın.

`$event->locale->value` okuyan veya `$event->locale` ile `Locale` enum’unu karşılaştıran olay tüketicilerini güncelleyin. `TranslationHasBeenSetEvent` ve `TranslationHasBeenForgetEvent` artık yerel ayar açıkça verildiğinde metin türünde bir takma ad taşır. Çağıran kod yerel ayarı atlarsa değer atama olayı `null` taşır. Bir takma adı kalıcı anahtar olarak kullanmadan önce `Locales::fromAlias()` ile dönüştürün. Değer atama olayı `save()` çağrısından önce, özniteliğe değer atanırken tetiklenir. Üç model çevirisi olayından herhangi birini elle göndermek için `event(new …)` kullanın.

Oluşturulan IDE yardımcı dosyalarını saklıyorsanız içe aktarımları ve model keşif yollarını güncelledikten sonra `php artisan lang:models-helper` çalıştırın. [Model çevirileri](./models.md)

### Yayım betiklerini güncelleme ve özel çevirileri koruma {#update-publication-scripts-and-keep-custom-translations}

| Önceki komut veya varsayım | Geçiş |
| --- | --- |
| `lang:add <locale>` yalnızca eklenen yerel ayarları yenilerdi | Artık tüm kurulu yerel ayarları yeniler. Betiklerde kodları açıkça belirtin ve mevcut yerel ayar dosyalarının da güncelleneceğini hesaba katın. |
| `lang:reset` | Birleştirmek için `lang:update` kullanın. Temiz bir yeniden oluşturma için özel girdileri yedekleyin, yeniden oluşturacağınız dosyaları kaldırın ve yeniden oluşturulacak yerel ayarların kodlarını açıkça vererek `lang:add` çalıştırın. |
| `lang:rm <locale> --force` | `lang:rm <locale>` kullanın. Laravel’in geçerli ve yedek yerel ayarları korunur. |
| Yalnızca `lang:rm` çıkış kodunu kontrol etme | Her sonucu kontrol edin. `DONE` kaldırmayı doğrular, `FAILED` ise kaldırma denemesinden sonra bir yolun kaldığını gösterir. `PROTECTED`, `INVALID` ve `SKIP` başarılı çıkış koduyla dönebilir. |

Artisan’ı komut sınıfı üzerinden çağırıyorsanız `LaravelLang\Publisher\Console\Add`, `Remove` ve `Update` yerine `AddCommand`, `RemoveCommand` ve `UpdateCommand` kullanın. Özel metin düzenleyici için `Contracts\TextDecorator` yerine `LaravelLang\Publisher\Contracts\Decorator` arayüzünü uygulayın ve bağlayın. Arayüzün `convert(string $locale, string $value): string` metodu standart yerel ayar kodunu alır.

İlk `lang:update` işlemini mevcut dil dosyalarınız sürüm denetimindeyken çalıştırın. Publisher çevirileri mevcut hedeflerle birleştirir ve sayısal anahtarlar da dâhil olmak üzere eşleşen anahtarların değerlerini değiştirir. Mümkünse özel çevirileri ayrı bir uygulama çeviri dosyasında tutun. Oluşturulan bir hedefin tamamının bakımını kendiniz üstlenmek için yol desenini `localization_main.skip` ayarına ekleyin. Kaynak ifadeye dönen her değeri kontrol edin. Sözlüklerde çeviri bulunmadığında Publisher kaynak ifadeyi kullanır.

Önceki bir güncelleme `http-statuses.php` dosyalarındaki anahtarları yeniden numaralandırdıysa etkilenen dosyaları yedekleyip kaldırın. Sorunu aşmak için `localization_main.skip` ayarına `'*/http-statuses.php'` eklediyseniz bu girdiyi kaldırın. Dosyaları yeniden oluşturmak için `php artisan lang:update` komutunu çalıştırın, ardından özel çevirileri ilgili durum kodlarıyla geri yükleyin. [Çevirileri yönetme](./translations.md)

### Eski eklenti kaydını değiştirme {#replace-legacy-plugin-registration}

Üçüncü taraf çeviri paketi kullanıyorsanız manifest tabanlı sürümüne yükseltin. Bir paketin bakımını yapıyorsanız eski `LaravelLang\Publisher\Plugins\Provider` sınıfını kaydeden sağlayıcıyı ve Composer keşif girdisini kaldırın. Bu sağlayıcı ilgisiz Laravel servislerini de yapılandırıyorsa söz konusu kayıtları normal bir Laravel servis sağlayıcısında tutun.

1. `vendor/` altına kurulan paketin köküne `laravel_lang.json` ekleyin. Her `Plugin::files()` girdisini paketin `source/` dizinine göre bir `source` yoluna ve uygulamanın dil dizinine göre bir `target` yoluna dönüştürün. Hedefte `{locale}` kullanın.
2. Composer eşleme kurallarını manifeste taşıyın. Kurulu bir bağımlılık için `vendor` ve `version`, kök Composer projesi için `project: true` içeren ayrı bir girdi kullanın. Eski eklentiniz `$with_project_name = true` kullanıyor ve her iki kipi de destekliyorsa iki girdiyi de oluşturun.
3. Sözlükleri `locales/<code>/<style>.json` biçimine dönüştürün. Kaynak dosyalarınızdaki değerler sözlük arama anahtarlarıdır. Örneğin `'enable' => 'Enable audit trail'` PHP girdisi `{locale}/audit.php` dosyasına yayımlandığında `audit.enable` uygulama anahtarını oluşturmaya devam eder. Çeviriyi sözlükte `"Enable audit trail"` anahtarı altında saklayın.
4. Mona’nın yerel kopyasından aşağıdaki Toolkit komutunu eski paket üzerinde çalıştırın. Komut, eski JSON ve PHP sözlüklerinden `default.json` ve `inline.json` üretir ve yerel ayar hariç tutma listelerini taşır. Çıktıyı inceleyin ve manifestiniz ve kaynak dosyalarınızla birlikte yeni pakete ekleyin. Takma ad kullanan sözlük dizinlerini standart kodlarla yeniden adlandırın.
5. Paketin Composer manifestine güncel Publisher bağımlılığını ekleyin. Paketiniz ortak sözlükten çeviri bekliyorsa `laravel-lang/library` bağımlılığını da ekleyin. Sonuçta oluşan paketi bir uygulamaya kurun ve yayımlanan çıktısını kontrol etmek için `php artisan lang:update` çalıştırın.

```bash
php src/Toolkit/bin/toolkit migrate-locales path/to/legacy-pack --target=path/to/new-pack/locales
```

Mevcut uygulamalarda yükseltmeden sonra hangi yerleşik paket kurallarının geçerli olduğunu kontrol edin.

- Spark Paddle, Spark Stripe, Nova Dusk Suite veya Nova Log Viewer için önceden yayımlanan Laravel-Lang çevirilerine hâlâ ihtiyacınız varsa bu anahtarları uygulamanın kendi dosyalarında tutun ya da eski kaynakları ve sözlükleri özel bir pakete taşıyın.
- Başlangıç kiti çevirileri artık projenin Composer `name` değeriyle eşleştirilir. Projeyi yeniden adlandırdıysanız veya bir kiti bağımlılık olarak kullanıyorsanız çevirilerini yayımlamak için kitin Composer adını, örneğin `laravel/livewire-starter-kit`, `localization_main.force` ayarına ekleyin. [Çeviri eklentileri](./plugins.md)

### JSON çevirilerinde yedek dili ve çeviri araçlarını güncelleme {#update-json-fallback-and-translation-tooling}

Uygulama kodu `LaravelLang\JsonFallback\Translator` sınıfını içe aktarıyor veya tür olarak bildiriyorsa türü `LaravelLang\JsonFallback\OverridedTranslator` ile değiştirin ya da Laravel’in çevirmen arayüzüne bağımlı olun. Eski çevirmen sınıfını genişlettiyseniz özel davranışı uygulamaya ait bir çevirmen sınıfına veya kendi çeviri servisinize taşıyın. Yeni çevirmen, çözümlenemeyen anahtarlar için Laravel’in eksik çeviri geri çağırma işlevini çağırır. Uygulamanız bu işlevi kaydediyorsa işlevi gözden geçirin. Yedek dilin JSON dosyası önce kontrol edildiğinden, hem yedek JSON’da hem geçerli dilin PHP dosyalarında bulunan anahtarları test edin. [JSON çevirilerinde yedek dil](./translations.md#json-fallback)

Makine çevirisi için ayrı `laravel-lang/translator` paketini kullanıyorsanız bu bağımlılığı kaldırmadan önce `Translate` facade’ı kullanımlarını ve Google, DeepL veya Yandex entegrasyonlarını seçtiğiniz çeviri servisine taşıyın. Bu betiklerle daha önce üretilen çevirileri uygulama veya paket sözlüklerinizde saklayın.

Paket kataloglarının bakımında `laravel-lang/status-generator` paketinin `vendor/bin/lang` aracını kullanıyorsanız `composer install` çalıştırdıktan sonra aşağıdaki yeni komutları Mona’nın yerel kopyasından çalıştırın.

| Önceki komut | Kullanılacak Toolkit komutu |
| --- | --- |
| `vendor/bin/lang status --path=path/to/pack` | `php src/Toolkit/bin/toolkit status path/to/pack`. Oluşturulan `locales/<code>/_status.json` raporlarını okuyun. |
| `vendor/bin/lang sync --path=path/to/pack` | `php src/Toolkit/bin/toolkit sync:package path/to/pack`. Ortak Library sözlüğünün bakımı için `sync:library` kullanın. |
| `vendor/bin/lang create` | `php src/Toolkit/bin/toolkit create-locale path/to/pack`. Bilinen yerel ayarlar için dizinler oluşturur. |
| `vendor/bin/lang download` | ZIP adresini `--url`, tam hedefi `--directory` ve arşivden alınacak yolları yinelenebilen `--paths` seçeneğiyle verin. |
| `vendor/bin/lang translate` | Makine çevirisini ayrı bir servise taşıyın. Hâlâ kaynakla eşleşen kayıtlı metinleri bulmak için Toolkit’in `status` komutunu çalıştırın. |

Tam örnekler için [Toolkit komutlarına](./toolkit.md) bakın.

## Yükseltmeyi doğrulama {#verify-the-upgrade}

Uygulama testlerinizi çalıştırın. Önemli her yerel ayarda yayımlanmış bir mesajı, yedek dilden gelen bir çeviriyi, yapılandırılmış takma adla yerelleştirilmiş bir URL’yi ve standart kodla saklanan bir model çevirisini kontrol edin. Oluşturulan dosyaları ve saklanan yerel ayar değerlerini kaynak değişiklikleriyle birlikte gözden geçirin.
