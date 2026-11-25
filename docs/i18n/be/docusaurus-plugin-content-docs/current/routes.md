---
title: "Лакалізаваныя маршруты"
description: "Рэгіструйце лакалізаваныя URL і выбірайце лакаль Laravel паводле запыту."
---

`laravel-lang/routes` выбірае лакаль падчас апрацоўкі запыту і генеруе URL для лакалізаваных груп маршрутаў. Пакет уваходзіць у Common.

```bash
composer require laravel-lang/routes
```

Пераканайцеся, што [лакалі, выкарыстаныя ў URL, усталяваныя](./translations.md). Імёны параметраў, мідлвар і паводзіны для лакалі па змаўчанні наладжваюцца ў [`config/localization_routes.php`](./configure.md).

## Рэгістрацыя лакалізаванай групы маршрутаў {#register-a-localized-route-group}

У `routes/web.php` выкарыстоўвайце макрас `Route::localizedGroup()`. Фасад `LaravelLang\Routes\Facades\LocalizationRoute::group()` дае той жа метад групавання.

```php
use Illuminate\Support\Facades\Route;

Route::localizedGroup(function (): void {
    Route::get('/products/{product}', fn (string $product) => view('products.show', [
        'product' => $product,
    ]))->name('products.show');
});
```

Пры стандартнай канфігурацыі функцыя зваротнага выкліку рэгіструе абодва шаблоны URL.

| Шаблон | Імя маршруту |
| --- | --- |
| `/products/{product}` | `products.show` |
| `/{locale}/products/{product}` | `localized.products.show` |

Мідлвар лакалі апрацоўвае і выдаляе параметр `locale` перад выклікам апрацоўніка маршруту. Калі маршрут мае імя, запыт з невядомай або неўсталяванай лакаллю ў URL перанакіроўваецца на маршрут без прэфікса.

Laravel выклікае функцыю групы двойчы. Калі знешняя група мае прэфікс URL `admin` і прэфікс імя `admin.`, лакалізаваны маршрут атрымае імя `admin.localized.products.show`.

## Генерацыя лакалізаванага URL {#generate-a-localized-url}

```php
use function LaravelLang\Routes\localizedRoute;

$url = localizedRoute('products.show', [
    'product' => 42,
    'locale' => 'fr',
], absolute: false);
// /fr/products/42
```

Дапаможная функцыя выкарыстоўвае наладжанае імя параметра лакалі і выбірае маршрут з прэфіксам, калі ён ёсць. Перададзены радок лакалі ідзе наўпрост у генератар URL. Для URL з аліясам перадайце гэты аліяс або спачатку атрымайце яго праз [`Locales::toAlias()`](./locales.md). Калі лакаль прапушчана, функцыя выкарыстоўвае `Locales::fallback()`. Каб выкарыстаць бягучую лакаль, яўна перадайце `Locales::current()`.

`localization_routes.hide` апускае рэзервовую лакаль з URL, якія стварае дапаможная функцыя. `localization_routes.redirect` перанакіроўвае запыты для бягучай лакалі з групы з прэфіксам на маршрут без прэфікса. Абедзве налады па змаўчанні маюць значэнне `false`.

Перадайце `Locale::French->value` для кананічнага кода або аб’ект `LocaleData` для аліяса. Перад генерацыяй URL праверце выбар карыстальніка праз `Locales::isInstalled()`.

## Выбар лакалі праз мідлвары {#select-a-locale-with-middleware}

Кожны мідлвар можна прымяніць непасрэдна праз яго клас з `LaravelLang\Routes\Middlewares`.

| Мідлвар | Крыніца |
| --- | --- |
| `LocalizationByParameterMiddleware` | Параметр маршруту, па змаўчанні `locale` |
| `LocalizationByParameterWithRedirectMiddleware` | Параметр маршруту. Для найменаванага маршруту з абвешчаным параметрам падстаўляе бягучую лакаль, калі запытаная адсутнічае або не ўсталяваная |
| `LocalizationByCookieMiddleware` | Па змаўчанні файл cookie `Accept-Language` |
| `LocalizationByHeaderMiddleware` | Першая ўсталяваная лакаль у парадку пераваг з загалоўка `Accept-Language`. Спачатку правярае поўны тэг, потым базавую мову. Ігнаруе падстановачныя сімвалы і запісы з вагой 0. |
| `LocalizationBySessionMiddleware` | Па змаўчанні ключ сесіі `Accept-Language` |
| `LocalizationByModelMiddleware` | Па змаўчанні атрыбут `locale` аўтэнтыфікаванага карыстальніка Eloquent. Дадайце `:guard_name` да класа мідлвара, каб выбраць механізм аўтэнтыфікацыі |

Для групы з прэфіксам парадак па змаўчанні такі: параметр URL, cookie, загаловак, сесія, мадэль. Група без прэфікса мае той жа парадак без кроку URL. Пазнейшая крыніца з сапраўдным значэннем можа перавызначыць ранейшую. Змена лакалі абнаўляе Laravel і захоўвае кананічны код па наладжаным ключы ў сесіі. Выкарыстоўвайце групу мідлвараў `web`, калі патрэбныя сесіі і cookie. Мідлвар загалоўка патрабуе PHP Intl.

Калі мідлвар змяняе лакаль, ён адпраўляе падзею `LaravelLang\Routes\Events\LocaleHasBeenSetEvent`. Яе ўласцівасць `locale` — аб’ект `LaravelLang\LocaleNames\LocaleData`. Каб атрымаць кананічны код, выкарыстоўвайце `$event->locale->code`.

Каб лакаль з URL мела прыярытэт, пакіньце ў `localization_routes.middlewares.prefix` толькі `LocalizationByParameterPrefixMiddlewareMiddleware::class`. Наладзьце ланцужок маршрутаў без прэфікса асобна.
