---
title: "Yerel ayarlar ve adlar"
description: "Desteklenen ve kurulu yerel ayarları bulun, takma adları çözümleyin ve yerelleştirilmiş adları alın."
---

Laravel-Lang, Laravel’de, çeviri dosyası adlarında ve model çeviri satırlarında `en`, `fr` ve `pt_BR` gibi standart kodlar kullanır. Yerel ayar takma adı, URL’deki `pt-br` gibi uygulamaya yönelik bir addır. Takma adları [`localization_main.aliases`](./configure.md) içinde standart koddan takma ada eşleme olarak tanımlayın.

## Desteklenen ve kurulu yerel ayarları bulma {#find-supported-and-installed-locales}

`laravel-lang/locale-list`, değer destekli `LaravelLang\LocaleList\Locale` enum’unu sağlar. Enum değerleri [desteklenen standart kodlara](./available-locales.md) karşılık gelir.

```php
use LaravelLang\LocaleList\Locale;

Locale::French->value;        // 'fr'
Locale::tryFrom('pt_BR');     // Locale::PortugueseBrazil
Locale::values();            // Standart kod dizisi
Locale::collection();        // Standart kod koleksiyonu
Locale::collection(false);   // Enum değerleri koleksiyonu
```

`laravel-lang/locales`, uygulamanın yerel ayarlarını tanıyan `LaravelLang\Locales\Facades\Locales` facade’ını ekler. Bu metotlar metin koleksiyonları döndürür. Standart kodları almak için argüman olarak `false` verin. Varsayılan olarak, yapılandırılmış bir takma ad varsa kod onunla temsil edilir.

| Metot | Dönüş değeri |
| --- | --- |
| `Locales::available()` | Bilinen tüm yerel ayarlar |
| `Locales::installed()` | Uygulamanın dil dosyalarında tanınan yerel ayarlar ve korunan yerel ayarlar |
| `Locales::uninstalled()` | Kurulu olmayan kullanılabilir yerel ayarlar |
| `Locales::protects()` | Yinelenen değerler çıkarıldıktan sonra geçerli ve yedek yerel ayarlar |

Keşif sırasında `lang_path()` yolundaki doğrudan girdiler ve bu yola göre `vendor/*/*.json` desenine uyan dosyalar okunur. Dosya veya dizin adı desteklenen bir koda çözümlenebilmelidir. `lang/vendor/<package>/` altında yalnızca PHP çevirilerinin bulunduğu dizinler bu keşif desenlerinin dışında kalır. Yerel ayar yayımlamak için [çeviri yönetimine](./translations.md) bakın.

## Uygulamanın yerel ayarını okuma veya belirleme {#read-or-set-the-application-locale}

```php
use LaravelLang\Locales\Facades\Locales;

if (Locales::isInstalled('fr')) {
    Locales::set('fr');
}

$canonicalCode = Locales::current(false);
$displayCode = Locales::current();
```

`set()`, kod, takma ad, `Locale` enum değeri veya `LocaleData` nesnesi kabul eder. Yerel ayar kuruluysa Laravel’in yerel ayarını ilgili standart kodla günceller. Kodun desteklenip desteklenmediğini kontrol etmek için `isAvailable()`, dosya veya koruma durumunu kontrol etmek için `isInstalled()` kullanın. `isProtected()` geçerli ve yedek yerel ayarları saptar.

`current()` önce Laravel’in geçerli yerel ayarını, tanınmıyorsa `fallback()` sonucunu kullanır. `fallback()`, Laravel’de yapılandırılan yedek yerel ayarı çözümler ve ayar bilinmiyorsa `en` döndürür. Her iki metot da standart kodlar için aynı `false` argümanını kabul eder.

Bir takma adı standart koda çözümlemek için `fromAlias()`, standart kodun takma adını almak için `toAlias()` kullanın. Bu dönüşümler, tanınmayan ve boş olmayan metinleri olduğu gibi bırakır. Girdiyi doğrulamak için `isAvailable()` kullanın.

## Yerel ayar seçeneklerini gösterme {#display-locale-choices}

`Locales::info('fr')`, `code`, `alias`, `native` ve `localized` özelliklerine sahip bir `LaravelLang\LocaleNames\LocaleData` nesnesi döndürür. Görüntüleme dili, uygulamanın geçerli yerel ayarıdır. Geçerli yerel ayar için argümanı boş bırakın. Bilinen bir yerel ayarın bilgileri kurulumdan önce alınabilir. Bilinmeyen bir kod `LaravelLang\Exceptions\UnknownLocaleException` hatasına yol açar.

Kurulu yerel ayarları `LocaleData` nesneleri olarak almak için `Locales::data()->installed()` kullanın. `data()` başka koleksiyonlar için `available()`, `uninstalled()` ve `protects()`, tekil nesneler için ise `current()` ve `fallback()` sağlar. Dil seçicide değer olarak `alias` gönderilebilir ve etiket olarak `native` adı gösterilebilir.

## Yerel ayar, ülke ve para birimi adlarını sorgulama {#look-up-locale-country-and-currency-names}

`laravel-lang/locale-names`, `laravel-lang/country-names` ve `laravel-lang/currency-names` paketlerinin her biri, Laravel konteynerinden enjekte edebileceğiniz bir servis sunar. Common üçünü de içerir.

```php
use LaravelLang\CountryNames\CountryNames;
use LaravelLang\CurrencyNames\CurrencyNames;
use LaravelLang\LocaleNames\LocaleNames;

$locale = app(LocaleNames::class)->one('de', 'fr');
$locale->localized; // 'Französisch'

$country = app(CountryNames::class)->one('de', 'fr');
$country->code;      // 'FR'
$country->localized; // 'Frankreich'

$currency = app(CurrencyNames::class)->one('de', 'fr');
$currency->code;    // 'EUR'
$currency->numeric; // 978

$nativeLocaleNames = app(LocaleNames::class)->all(null, \SortDirection::Ascending);
```

İlk parametre görüntüleme dilidir. Adları kendi dillerinde almak için `null` kullanın. `one()` metodunun ikinci parametresi, yukarıdaki örneklerde `fr` gibi, satırın yerel ayarıdır. Ülke ve para birimi sorgularında ISO kodu döndürülen nesnededir. Her iki parametre de standart kodları, yapılandırılmış takma adları ve `Locale` enum değerlerini kabul eder.

`all($forLocale)`, standart yerel ayar koduyla anahtarlanan bir koleksiyon döndürür. `localized` özelliğine göre sıralamak için `SortDirection::Ascending` veya `SortDirection::Descending` verin. Her değer salt okunur bir nesnedir. Ülke ve para birimi değerlerinde satırın `locale` özelliği de bulunur.
