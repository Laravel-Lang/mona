---
title: "Yerelleştirilmiş rotalar"
description: "Yerelleştirilmiş URL’leri kaydedin ve istekten Laravel yerel ayarını seçin."
---

`laravel-lang/routes`, istek sırasında yerel ayarı seçer ve yerelleştirilmiş rota grupları için URL’ler oluşturur. Paket Common’a dahildir.

```bash
composer require laravel-lang/routes
```

[URL’lerinizde kullanılan yerel ayarların kurulu olduğundan emin olun](./translations.md). Parametre adlarını, ara katmanları ve varsayılan yerel ayarın davranışını [`config/localization_routes.php`](./configure.md) dosyasında yapılandırın.

## Yerelleştirilmiş rota grubu kaydetme {#register-a-localized-route-group}

`routes/web.php` dosyasında `Route::localizedGroup()` makrosunu kullanın. `LaravelLang\Routes\Facades\LocalizationRoute::group()` facade’ı aynı gruplama metodunu sunar.

```php
use Illuminate\Support\Facades\Route;

Route::localizedGroup(function (): void {
    Route::get('/products/{product}', fn (string $product) => view('products.show', [
        'product' => $product,
    ]))->name('products.show');
});
```

Geri çağırma işlevi, varsayılan yapılandırmada her iki URL desenini de kaydeder.

| Desen | Rota adı |
| --- | --- |
| `/products/{product}` | `products.show` |
| `/{locale}/products/{product}` | `localized.products.show` |

Yerel ayar ara katmanı, rota işleyicisini çağırmadan önce `locale` parametresini işler ve kaldırır. URL’de bilinmeyen veya kurulu olmayan bir yerel ayar istenirse, rota adlandırılmış olduğu sürece istek öneksiz rotaya yönlendirilir.

Laravel grup geri çağrısını iki kez çalıştırır. Dış grubun URL ön eki `admin`, ad ön eki `admin.` ise yerelleştirilmiş rotanın adı `admin.localized.products.show` olur.

## Yerelleştirilmiş URL oluşturma {#generate-a-localized-url}

```php
use function LaravelLang\Routes\localizedRoute;

$url = localizedRoute('products.show', [
    'product' => 42,
    'locale' => 'fr',
], absolute: false);
// /fr/products/42
```

Yardımcı işlev, yapılandırılmış yerel ayar parametresinin adını kullanır ve varsa önekli rotayı tercih eder. Verilen yerel ayar metnini URL oluşturma işlemine iletir. Takma adla URL oluşturmak için takma adı doğrudan verin veya önce [`Locales::toAlias()`](./locales.md) ile çözümleyin. Yerel ayarı vermezseniz yardımcı işlev `Locales::fallback()` kullanır. Geçerli yerel ayarı kullanmak için `Locales::current()` sonucunu açıkça verin.

`localization_routes.hide`, yardımcı işlevin oluşturduğu URL’lerden yedek yerel ayarı çıkarır. `localization_routes.redirect`, geçerli yerel ayara yönelik istekleri önekli gruptan öneksiz rotaya yönlendirir. Her iki ayar da varsayılan olarak `false` değerindedir.

Standart kod için `Locale::French->value`, takma ad için `LocaleData` nesnesini verin. URL’yi oluşturmadan önce kullanıcının seçimini `Locales::isInstalled()` ile doğrulayın.

## Ara katmanla yerel ayar seçme {#select-a-locale-with-middleware}

Her ara katman, `LaravelLang\Routes\Middlewares` ad alanındaki sınıfı üzerinden doğrudan uygulanabilir.

| Ara katman | Kaynak |
| --- | --- |
| `LocalizationByParameterMiddleware` | Varsayılan adı `locale` olan bir rota parametresi |
| `LocalizationByParameterWithRedirectMiddleware` | Bir rota parametresi. Rota adlandırılmış ve parametre tanımlıysa istenen yerel ayar yokken veya kurulu değilken geçerli yerel ayarı kullanır |
| `LocalizationByCookieMiddleware` | Varsayılan olarak `Accept-Language` çerezi |
| `LocalizationByHeaderMiddleware` | `Accept-Language` başlığındaki tercih sırasına göre ilk yüklü yerel ayar. Önce tam dil etiketini, ardından temel dili dener. Joker karakterleri ve kalite değeri 0 olan girdileri atlar. |
| `LocalizationBySessionMiddleware` | Varsayılan olarak `Accept-Language` oturum anahtarı |
| `LocalizationByModelMiddleware` | Varsayılan olarak kimliği doğrulanmış Eloquent kullanıcısının `locale` özniteliği. Bir guard seçmek için ara katman sınıfına `:guard_name` ekleyin |

Önekli grup için varsayılan sıra URL parametresi, çerez, başlık, oturum ve modeldir. Öneksiz grup URL adımı dışında aynı sırayı kullanır. Sırada daha sonra gelen geçerli bir kaynak, önceki kaynağın seçimini geçersiz kılabilir. Yerel ayar değişikliği Laravel’i günceller ve standart kodu yapılandırılmış oturum anahtarına kaydeder. Oturum ve çerez gerektiğinde `web` ara katman grubunu kullanın. Başlık ara katmanı PHP Intl gerektirir.

Ara katman yerel ayarı değiştirdiğinde `LaravelLang\Routes\Events\LocaleHasBeenSetEvent` olayını tetikler. Olayın `locale` özelliği bir `LaravelLang\LocaleNames\LocaleData` nesnesidir. Standart kod için `$event->locale->code` kullanın.

URL’nin belirleyici olması için `localization_routes.middlewares.prefix` içinde yalnızca `LocalizationByParameterPrefixMiddlewareMiddleware::class` bırakın. Ön eksiz rota zincirini ayrı yapılandırın.
