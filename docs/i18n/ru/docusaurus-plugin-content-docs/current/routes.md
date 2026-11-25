---
title: Локализованные маршруты
description: Регистрируйте локализованные URL и выбирайте локаль Laravel из запроса.
---

`laravel-lang/routes` выбирает локаль при обработке запроса и генерирует URL для групп локализованных маршрутов. Пакет входит в Common.

```bash
composer require laravel-lang/routes
```

Убедитесь, что [локали из URL установлены](./translations.md). Настройте имена параметров, мидлвари и поведение локали по умолчанию в [`config/localization_routes.php`](./configure.md).

## Регистрация группы локализованных маршрутов {#register-a-localized-route-group}

Используйте макрос `Route::localizedGroup()` в `routes/web.php`. Фасад `LaravelLang\Routes\Facades\LocalizationRoute::group()` предоставляет такой же метод группировки.

```php
use Illuminate\Support\Facades\Route;

Route::localizedGroup(function (): void {
    Route::get('/products/{product}', fn (string $product) => view('products.show', [
        'product' => $product,
    ]))->name('products.show');
});
```

При настройках по умолчанию переданная функция регистрирует оба шаблона URL.

| Шаблон | Имя маршрута |
| --- | --- |
| `/products/{product}` | `products.show` |
| `/{locale}/products/{product}` | `localized.products.show` |

Мидлварь локализации извлекает параметр `locale` перед вызовом обработчика маршрута. Когда запрос содержит неизвестную или неустановленную локаль, именованный маршрут перенаправляет его на маршрут без префикса.

Laravel выполняет функцию группы дважды. Если у внешней группы задан префикс URL `admin` и префикс имени `admin.`, локализованный маршрут получит имя `admin.localized.products.show`.

## Генерация локализованного URL {#generate-a-localized-url}

```php
use function LaravelLang\Routes\localizedRoute;

$url = localizedRoute('products.show', [
    'product' => 42,
    'locale' => 'fr',
], absolute: false);
// /fr/products/42
```

Вспомогательная функция использует настроенное имя параметра локали и предпочитает маршрут с префиксом, если он существует. Она передаёт указанную строку локали генератору URL. Чтобы сгенерировать URL с алиасом, передайте алиас или сначала получите его через [`Locales::toAlias()`](./locales.md). Если локаль не указана, функция использует `Locales::fallback()`. Чтобы использовать текущую локаль, явно передайте `Locales::current()`.

`localization_routes.hide` исключает резервную локаль из URL, которые генерирует вспомогательная функция. `localization_routes.redirect` перенаправляет запросы с текущей локалью из группы с префиксом на маршрут без префикса. По умолчанию обе настройки имеют значение `false`.

Передайте `Locale::French->value` для канонического кода или объект `LocaleData` для его алиаса. Перед генерацией URL проверьте выбранную пользователем локаль через `Locales::isInstalled()`.

## Выбор локали через мидлвари {#select-a-locale-with-middleware}

Каждую мидлварь можно подключить напрямую по имени класса из `LaravelLang\Routes\Middlewares`.

| Мидлварь | Источник |
| --- | --- |
| `LocalizationByParameterMiddleware` | Параметр маршрута, по умолчанию `locale` |
| `LocalizationByParameterWithRedirectMiddleware` | Параметр маршрута. При наличии имени маршрута и объявленного параметра подставляет текущую локаль, если запрошенная отсутствует или не установлена |
| `LocalizationByCookieMiddleware` | По умолчанию cookie `Accept-Language` |
| `LocalizationByHeaderMiddleware` | Первая установленная локаль в порядке предпочтений из заголовка `Accept-Language`. Middleware сначала проверяет полный языковой тег, затем базовый язык. Шаблон `*` и записи с весом 0 пропускаются. |
| `LocalizationBySessionMiddleware` | По умолчанию ключ сеанса `Accept-Language` |
| `LocalizationByModelMiddleware` | По умолчанию атрибут `locale` аутентифицированного пользователя Eloquent. Чтобы выбрать guard, добавьте `:guard_name` к имени класса |

Для группы с префиксом порядок по умолчанию следующий: параметр URL, cookie, заголовок, сеанс, модель. Для группы без префикса порядок тот же, пропускается только параметр URL. Поздний подходящий источник может переопределить более ранний. При смене локали обновляется локаль Laravel, а канонический код сохраняется в настроенном ключе сеанса. Чтобы использовать сеансы и cookie, подключите группу мидлварей `web`. Мидлварь для заголовка требует PHP Intl.

При смене локали мидлварь отправляет событие `LaravelLang\Routes\Events\LocaleHasBeenSetEvent`. Его свойство `locale` содержит объект `LaravelLang\LocaleNames\LocaleData`. Канонический код доступен в `$event->locale->code`.

Чтобы язык определялся URL, оставьте в `localization_routes.middlewares.prefix` только `LocalizationByParameterPrefixMiddlewareMiddleware::class`. Цепочку для маршрутов без префикса настройте отдельно.
