<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use LaravelLang\Config\Services\RouteLangConfig;
use LaravelLang\Routes\Middlewares\LocalizationByParameterPrefixMiddlewareMiddleware;
use Workbench\App\Constants\FakeTranslation;

use function Pest\Laravel\getJson;

test('main without prefix', function (): void {
    $foo = 'test';

    getJson(route('via.group.macro', ['key' => $foo]))
        ->assertSuccessful()
        ->assertJsonPath($foo, FakeTranslation::French);
});

test('main locale with enabled redirect', function (string $locale): void {
    config()->set('localization_routes.redirect', true);

    $foo = 'test';

    getJson(route(app(RouteLangConfig::class)->prefix . 'via.group.macro', ['key' => $foo, 'locale' => $locale]))
        ->assertRedirectToRoute('via.group.macro', ['key' => $foo]);

    assertEventNotDispatched();
})->with('aliased locales');

test('main locale with disabled redirect', function (string $locale, string $translation): void {
    config()->set('localization_routes.redirect', false);

    setCustomLocale();

    $foo = 'test';

    getJson(route(app(RouteLangConfig::class)->prefix . 'via.group.macro', ['key' => $foo, 'locale' => $locale]))
        ->assertSuccessful()
        ->assertJsonPath($foo, $translation);

    assertEventDispatched();
})->with('direct locales');

test('aliased locale', function (string $locale): void {
    $foo = 'test';

    setCustomLocale();

    getJson(route(app(RouteLangConfig::class)->prefix . 'via.group.macro', ['key' => $foo, 'locale' => $locale]))
        ->assertSuccessful()
        ->assertJsonPath($foo, FakeTranslation::French);

    assertEventDispatched();
})->with('aliased locales');

test('uninstalled locale', function (string $locale): void {
    $foo = 'test';

    getJson(route(app(RouteLangConfig::class)->prefix . 'via.group.macro', ['key' => $foo, 'locale' => $locale]))
        ->assertRedirectToRoute('via.group.macro', ['key' => $foo]);

    assertEventNotDispatched();
})->with('uninstalled locales');

test('unknown locale', function (int|string $locale): void {
    $foo = 'test';

    getJson(route(app(RouteLangConfig::class)->prefix . 'via.group.macro', ['key' => $foo, 'locale' => $locale]))
        ->assertRedirectToRoute('via.group.macro', ['key' => $foo]);

    assertEventNotDispatched();
})->with('unknown locales');

test('serves an unnamed route with an invalid locale prefix without redirecting', function (): void {
    Route::middleware(LocalizationByParameterPrefixMiddlewareMiddleware::class)
        ->get('{locale}/unnamed-prefix', fn (): array => ['locale' => app()->getLocale()]);

    getJson('/invalid/unnamed-prefix')->assertOk()->assertJsonPath('locale', 'fr');

    assertEventNotDispatched();
});

test('redirects a manually named locale-prefixed route using the configured name prefix', function (): void {
    Route::get('manual-prefix', fn (): string => app()->getLocale())->name('manual-prefix');
    Route::middleware(LocalizationByParameterPrefixMiddlewareMiddleware::class)
        ->get('{locale}/manual-prefix', fn (): string => app()->getLocale())
        ->name('localized.manual-prefix');
    Route::getRoutes()->refreshNameLookups();

    getJson('/unknown/manual-prefix')->assertRedirect('/manual-prefix');
});
