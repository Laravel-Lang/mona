<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use LaravelLang\Config\Services\RouteLangConfig;
use LaravelLang\Exceptions\InvalidLocaleInputException;
use LaravelLang\Locales\Facades\Locales;
use LaravelLang\Routes\Facades\LocalizationRoute;
use LaravelLang\Routes\Services\RouteService;
use Workbench\App\Constants\FakeLocale;

use function LaravelLang\Routes\localizedRoute;
use function Pest\Laravel\get;

test('route groups', function (): void {
    $name     = app(RouteLangConfig::class)->prefix;
    $locale   = FakeLocale::Default;
    $fallback = FakeLocale::Fallback;

    expect(localizedRoute($name . 'via.group.facade', ['key' => 'bar', 'locale' => $locale]))
        ->toEndWith("localhost/$locale/group/facade/bar");

    expect(localizedRoute($name . 'via.group.macro', ['key' => 'bar', 'locale' => $fallback]))
        ->toEndWith("localhost/$fallback/group/macro/bar");

    expect(localizedRoute('via.group.facade', ['key' => 'bar', 'locale' => $locale]))
        ->toEndWith("localhost/$locale/group/facade/bar");

    expect(localizedRoute('via.group.macro', ['key' => 'bar', 'locale' => $fallback]))
        ->toEndWith("localhost/$fallback/group/macro/bar");
});

test('routes without groups', function (): void {
    $locale = FakeLocale::Default;

    expect(localizedRoute('via.model.default', ['key' => 'bar', 'locale' => $locale]))
        ->toEndWith('localhost/model/default/bar?locale=' . $locale);
});

test('rejects an invalid locale parameter when generating a localized URL', function (): void {
    expect(fn (): string => localizedRoute('via.parameter', ['key' => 'bar', 'locale' => ['fr']]))
        ->toThrow(InvalidLocaleInputException::class, 'The locale route parameter must be a Locale, a LocaleData, or a string');
});

test('routes hide fallback', function (bool $set): void {
    $locale   = FakeLocale::Default;
    $fallback = FakeLocale::Default;

    config()->set('localization_routes.hide', true);
    config()->set('app.fallback_locale', $fallback);

    $params = $set
        ? ['key' => 'bar', 'locale' => $locale]
        : ['key' => 'bar'];

    expect(localizedRoute('via.parameter', $params))
        ->toEndWith('localhost/path/bar');
})->with([true, false]);

test('routes hide manual', function (): void {
    $locale   = FakeLocale::Default;
    $fallback = FakeLocale::Fallback;

    config()->set('localization_routes.hide', true);
    config()->set('app.fallback_locale', $fallback);

    expect(localizedRoute('via.parameter', ['key' => 'bar', 'locale' => $locale]))
        ->toEndWith("localhost/path/bar/$locale");

    expect(localizedRoute('via.parameter', ['key' => 'bar', 'locale' => $fallback]))
        ->toEndWith('localhost/path/bar');
});

test('routes does not hide', function (): void {
    $fallback = FakeLocale::Fallback;

    config()->set('localization_routes.hide', true);
    config()->set('app.fallback_locale', $fallback);

    expect(localizedRoute('via.parameter', ['key' => 'bar']))
        ->toEndWith('localhost/path/bar');
});

test('uses the configured parameter and preserves the remaining route parameters on redirect', function (): void {
    config()->set('localization_routes.names.parameter', 'language');
    LocalizationRoute::clearResolvedInstance(RouteService::class);

    Route::localizedGroup(function (): void {
        Route::get('catalog/{category}/{item}', fn () => app()->getLocale())
            ->name('catalog.item');
    });
    Route::getRoutes()->refreshNameLookups();

    $url = localizedRoute('catalog.item', [
        'language' => 'invalid',
        'category' => 'books',
        'item'     => 42,
    ], absolute: false);

    expect($url)->toBe('/invalid/catalog/books/42');

    get($url)->assertRedirect('/catalog/books/42');
    get('/catalog/books/42')->assertOk()->assertSee('fr');
});

test('keeps the localized route helper usable when its file is loaded again', function (): void {
    require dirname(__DIR__, 4) . '/src/Routes/src/helpers.php';

    expect(localizedRoute('via.group.macro', ['key' => 'bar', 'locale' => 'de'], absolute: false))
        ->toBe('/de/group/macro/bar');
});

test('localized routes keep the outer group name when generating URLs and redirecting', function (): void {
    Route::name('admin.')->prefix('admin')->group(function (): void {
        Route::localizedGroup(function (): void {
            Route::get('products/{product}', fn (string $product): array => ['product' => $product])
                ->name('products.show');
            Route::get('stock', fn (): string => app()->getLocale())->name('localized.stock');
        });
    });
    Route::getRoutes()->refreshNameLookups();

    $parameters = ['product' => 42, 'locale' => 'de'];

    expect(localizedRoute('admin.products.show', $parameters, absolute: false))
        ->toBe('/admin/de/products/42')
        ->and(localizedRoute('admin.localized.products.show', $parameters, absolute: false))
        ->toBe('/admin/de/products/42')
        ->and(localizedRoute('admin.localized.stock', ['locale' => 'de'], absolute: false))
        ->toBe('/admin/de/stock');

    get('/admin/unknown/products/42')->assertRedirect('/admin/products/42');
    get('/admin/unknown/stock')->assertRedirect('/admin/stock');
    get('/admin/de/products/42')->assertOk()->assertJsonPath('product', '42');

    config()->set('localization_routes.hide', true);

    expect(localizedRoute('admin.localized.products.show', ['product' => 42], absolute: false))
        ->toBe('/admin/products/42')
        ->and(localizedRoute('localized.via.group.macro', ['key' => 'bar'], absolute: false))
        ->toBe('/group/macro/bar');
});

test('generates the URL from a locale data alias', function (): void {
    $url = localizedRoute('via.group.macro', [
        'key'    => 'bar',
        'locale' => Locales::data()->current(),
    ], absolute: false);

    expect($url)->toBe('/' . FakeLocale::DefaultAlias . '/group/macro/bar');
});

test('an empty or missing route name prefix leaves both routes cacheable', function (?string $prefix): void {
    $settings = config()->array('localization_routes');

    if ($prefix === null) {
        unset($settings['prefix']);
    } else {
        $settings['prefix'] = $prefix;
    }

    config()->set('localization_routes', $settings);
    LocalizationRoute::clearResolvedInstance(RouteService::class);

    Route::localizedGroup(function (): void {
        Route::get('empty-prefix', fn (): string => app()->getLocale())->name('empty-prefix');
    });
    Route::getRoutes()->refreshNameLookups();

    $routes = Route::getRoutes()->compile()['attributes'];

    expect($routes['empty-prefix']['uri'])->toBe('empty-prefix')
        ->and($routes['localized.empty-prefix']['uri'])->toBe('{locale}/empty-prefix')
        ->and(localizedRoute('empty-prefix', ['locale' => 'de'], absolute: false))->toBe('/de/empty-prefix');

    get('/unknown/empty-prefix')->assertRedirect('/empty-prefix');
})->with(['empty' => [''], 'missing' => [null]]);
