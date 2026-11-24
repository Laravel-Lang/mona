---
title: Локалізовані маршрути
description: Реєструйте локалізовані URL-адреси та визначайте локаль Laravel із запиту.
---

Пакет `laravel-lang/routes` визначає локаль під час обробки запиту й генерує URL-адреси для груп локалізованих маршрутів. Пакет входить до Common.

```bash
composer require laravel-lang/routes
```

Переконайтеся, що [локалі, які використовуються в URL, установлено](./translations.md). Налаштуйте назви параметрів, мідлвар та поведінку типової локалі у [`config/localization_routes.php`](./configure.md).

## Реєстрація групи локалізованих маршрутів {#register-a-localized-route-group}

У `routes/web.php` використовуйте макрос `Route::localizedGroup()`. Фасад `LaravelLang\Routes\Facades\LocalizationRoute::group()` надає такий самий метод групування.

```php
use Illuminate\Support\Facades\Route;

Route::localizedGroup(function (): void {
    Route::get('/products/{product}', fn (string $product) => view('products.show', [
        'product' => $product,
    ]))->name('products.show');
});
```

За типових налаштувань функція зворотного виклику реєструє обидва шаблони URL.

| Шаблон | Назва маршруту |
| --- | --- |
| `/products/{product}` | `products.show` |
| `/{locale}/products/{product}` | `localized.products.show` |

Мідлвар локалізації вилучає параметр `locale` перед викликом обробника маршруту. Запит із невідомою або невстановленою локаллю в URL перенаправляється на маршрут без префікса, якщо маршрут має назву.

## Генерування локалізованого URL {#generate-a-localized-url}

```php
use function LaravelLang\Routes\localizedRoute;

$url = localizedRoute('products.show', [
    'product' => 42,
    'locale' => 'fr',
], absolute: false);
// /fr/products/42
```

Допоміжна функція використовує налаштовану назву параметра локалі й віддає перевагу маршруту з префіксом, якщо такий є. Вона передає вказаний рядок локалі до генератора URL без змін. Щоб згенерувати URL із аліасом, передайте його безпосередньо або спочатку отримайте через [`Locales::toAlias()`](./locales.md). Якщо локаль не вказано, функція використовує `Locales::fallback()`. Щоб використати поточну локаль, явно передайте `Locales::current()`.

Параметр `localization_routes.hide` прибирає резервну локаль з URL, створених допоміжною функцією. Параметр `localization_routes.redirect` перенаправляє запити для поточної локалі з групи з префіксом на маршрут без префікса. Типове значення обох параметрів — `false`.

## Вибір локалі через мідлвари {#select-a-locale-with-middleware}

Кожен мідлвар можна застосувати безпосередньо через його клас із `LaravelLang\Routes\Middlewares`.

| Мідлвар | Джерело |
| --- | --- |
| `LocalizationByParameterMiddleware` | Параметр маршруту, типово `locale` |
| `LocalizationByParameterWithRedirectMiddleware` | Параметр маршруту. На іменованому маршруті з оголошеним параметром підставляє поточну локаль, якщо запитану локаль пропущено або не встановлено |
| `LocalizationByCookieMiddleware` | Типово файл cookie `Accept-Language` |
| `LocalizationByHeaderMiddleware` | Перша встановлена локаль у порядку пріоритету із заголовка `Accept-Language`. Спочатку перевіряє повний мовний тег, потім базову мову. Пропускає шаблон `*` і записи з вагою 0. |
| `LocalizationBySessionMiddleware` | Типово ключ сеансу `Accept-Language` |
| `LocalizationByModelMiddleware` | Типово атрибут `locale` автентифікованого користувача Eloquent. Додайте `:guard_name` до класу мідлвара, щоб вибрати механізм автентифікації |

Для групи з префіксом типовий порядок такий: параметр URL, cookie, заголовок, сеанс, модель. Група без префікса використовує такий самий порядок без кроку URL. Наступне коректне джерело може перевизначити попереднє. Зміна локалі оновлює Laravel і зберігає канонічний код за налаштованим ключем сеансу. Використовуйте групу мідлварів `web`, якщо потрібні сеанси та cookie. Мідлвар для заголовка потребує PHP Intl.

Після зміни локалі мідлвар надсилає подію `LaravelLang\Routes\Events\LocaleHasBeenSetEvent`. Властивість події `locale` — об'єкт `LaravelLang\LocaleNames\LocaleData`. Канонічний код доступний через `$event->locale->code`.
