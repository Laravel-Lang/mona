---
title: "Mahalliylashtirilgan marshrutlar"
description: "Mahalliylashtirilgan URL manzillarini ro‘yxatdan o‘tkazing va so‘rovdan Laravel lokalini tanlang."
---

`laravel-lang/routes` so‘rov vaqtida lokalni tanlaydi va mahalliylashtirilgan marshrut guruhlari uchun URL manzillarini yaratadi. Paket Common tarkibiga kiradi.

```bash
composer require laravel-lang/routes
```

[URL manzillarida ishlatilgan lokallar o‘rnatilganiga](./translations.md) ishonch hosil qiling. Parametr nomlari, middlewarelar va standart lokalning ishlashini [`config/localization_routes.php`](./configure.md) faylida sozlang.

## Mahalliylashtirilgan marshrut guruhini ro‘yxatdan o‘tkazish {#register-a-localized-route-group}

`routes/web.php` faylida `Route::localizedGroup()` makrosidan foydalaning. `LaravelLang\Routes\Facades\LocalizationRoute::group()` fasadi xuddi shu guruhlash metodini taqdim etadi.

```php
use Illuminate\Support\Facades\Route;

Route::localizedGroup(function (): void {
    Route::get('/products/{product}', fn (string $product) => view('products.show', [
        'product' => $product,
    ]))->name('products.show');
});
```

Standart sozlamada qayta chaqiriladigan funksiya ikkala URL andozasini ham ro‘yxatdan o‘tkazadi.

| Andoza | Marshrut nomi |
| --- | --- |
| `/products/{product}` | `products.show` |
| `/{locale}/products/{product}` | `localized.products.show` |

Lokalni aniqlovchi middleware marshrut ishlovchisi chaqirilishidan oldin `locale` parametrini o‘qiydi va marshrut parametrlaridan olib tashlaydi. Marshrut nomlangan bo‘lsa, noma’lum yoki o‘rnatilmagan URL lokali bilan kelgan so‘rov prefikssiz marshrutga yo‘naltiriladi.

Laravel guruh funksiyasini ikki marta ishga tushiradi. Tashqi guruhda URL prefiksi `admin`, nom prefiksi `admin.` bo‘lsa, lokal marshrut nomi `admin.localized.products.show` bo‘ladi.

## Mahalliylashtirilgan URL yaratish {#generate-a-localized-url}

```php
use function LaravelLang\Routes\localizedRoute;

$url = localizedRoute('products.show', [
    'product' => 42,
    'locale' => 'fr',
], absolute: false);
// /fr/products/42
```

Yordamchi funksiya sozlangan lokal parametri nomidan foydalanadi va prefiksli marshrut mavjud bo‘lsa, uni tanlaydi. Uzatilgan lokal satri URL yaratishda o‘zgarishsiz ishlatiladi. Muqobil nomli URL yaratish uchun shu nomni uzating yoki avval uni [`Locales::toAlias()`](./locales.md) bilan aniqlang. Lokal berilmasa, yordamchi funksiya `Locales::fallback()` dan foydalanadi. Joriy lokalni ishlatish uchun `Locales::current()` ni aniq uzating.

`localization_routes.hide` yordamchi funksiya yaratgan URL’larda zaxira lokalni tushirib qoldiradi. `localization_routes.redirect` joriy lokalga yuborilgan so‘rovlarni prefiksli guruhdan prefikssiz marshrutga yo‘naltiradi. Ikkala sozlamaning standart qiymati `false`.

Kanonik kod uchun `Locale::French->value`, muqobil nom uchun `LocaleData` obyektini uzating. URL yaratishdan oldin foydalanuvchi tanlovini `Locales::isInstalled()` bilan tekshiring.

## Middleware orqali lokal tanlash {#select-a-locale-with-middleware}

Har bir middlewareni `LaravelLang\Routes\Middlewares` nomlar fazosidagi sinfi orqali bevosita qo‘llash mumkin.

| Middleware | Manba |
| --- | --- |
| `LocalizationByParameterMiddleware` | Marshrut parametri, standart nomi `locale` |
| `LocalizationByParameterWithRedirectMiddleware` | Marshrut parametri. Marshrut nomlangan va parametr e’lon qilingan bo‘lsa, so‘ralgan lokal berilmaganda yoki o‘rnatilmaganda joriy lokal qo‘yiladi |
| `LocalizationByCookieMiddleware` | Standart holatda `Accept-Language` cookie fayli |
| `LocalizationByHeaderMiddleware` | `Accept-Language` sarlavhasidagi afzalliklar tartibiga ko‘ra birinchi o‘rnatilgan lokal. Avval to‘liq til tegini, so‘ng asosiy tilni tekshiradi. O‘rinbosar belgilar va sifat qiymati 0 bo‘lgan yozuvlarni o‘tkazib yuboradi. |
| `LocalizationBySessionMiddleware` | Standart holatda `Accept-Language` sessiya kaliti |
| `LocalizationByModelMiddleware` | Standart holatda autentifikatsiyadan o‘tgan Eloquent foydalanuvchisining `locale` atributi. Autentifikatsiya mexanizmini tanlash uchun middleware sinfiga `:guard_name` qo‘shing |

Prefiksli guruhdagi standart tartib: URL parametri, cookie, sarlavha, sessiya, so‘ng model. Prefikssiz guruhda ham tartib bir xil, faqat URL bosqichi yo‘q. Keyingi yaroqli manba oldingisini almashtirishi mumkin. Lokal o‘zgarishi Laravel sozlamasini yangilaydi va kanonik kodni sozlangan sessiya kalitida saqlaydi. Sessiya va cookie kerak bo‘lganda `web` middlewarelar guruhidan foydalaning. Sarlavha orqali lokalni aniqlovchi middleware PHP Intl’ni talab qiladi.

Middleware lokalni o‘zgartirganda `LaravelLang\Routes\Events\LocaleHasBeenSetEvent` hodisasini yuboradi. Uning `locale` xususiyati `LaravelLang\LocaleNames\LocaleData` obyektidir. Kanonik kod uchun `$event->locale->code` dan foydalaning.

URL lokalni belgilashi uchun `localization_routes.middlewares.prefix` da faqat `LocalizationByParameterPrefixMiddlewareMiddleware::class` ni qoldiring. Prefikssiz marshrutlar zanjirini alohida sozlang.
