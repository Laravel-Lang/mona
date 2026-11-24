<?php

declare(strict_types=1);

use LaravelLang\Config\Services\RouteNameLangConfig;
use LaravelLang\Exceptions\UnknownLocaleException;
use LaravelLang\Locales\Facades\Locales;
use Workbench\App\Constants\FakeTranslation;

use function Pest\Laravel\getJson;

test('direct locale', function (string $locale, string $translated): void {
    $foo = 'test';

    setCustomLocale();

    getJson(route('via.header', ['key' => $foo]), [
        app(RouteNameLangConfig::class)->header => $locale,
    ])
        ->assertSuccessful()
        ->assertJsonPath($foo, $translated);

    assertEventDispatched();
})->with('direct locales');

test('aliased locale', function (string $locale): void {
    $foo = 'test';

    setCustomLocale();

    getJson(route('via.header', ['key' => $foo]), [
        app(RouteNameLangConfig::class)->header => $locale,
    ])
        ->assertSuccessful()
        ->assertJsonPath($foo, FakeTranslation::French);

    assertEventDispatched();
})->with('aliased locales');

test('empty locale', function (int|string|null $locale): void {
    $foo = 'test';

    getJson(route('via.header', ['key' => $foo]), [
        app(RouteNameLangConfig::class)->header => $locale,
    ])
        ->assertSuccessful()
        ->assertJsonPath($foo, FakeTranslation::French);

    assertEventNotDispatched();
})->with('incorrect locales');

test('uninstalled locale', function (string $locale): void {
    $foo = 'test';

    getJson(route('via.header', ['key' => $foo]), [
        app(RouteNameLangConfig::class)->header => $locale,
    ])
        ->assertSuccessful()
        ->assertJsonPath($foo, FakeTranslation::French);

    assertEventNotDispatched();
})->with('uninstalled locales');

test('unknown locale', function (int|string $locale): void {
    $foo = 'test';

    getJson(route('via.header', ['key' => $foo]), [
        app(RouteNameLangConfig::class)->header => $locale,
    ])
        ->assertSuccessful()
        ->assertJsonPath($foo, FakeTranslation::French);

    assertEventNotDispatched();
})->with('unknown locales');

test('ignores a locale header beyond the ICU length limit', function (): void {
    getJson(route('via.header', ['key' => 'test']), ['Accept-Language' => str_repeat('a', 300)])
        ->assertSuccessful()
        ->assertJsonPath('test', FakeTranslation::French);

    assertEventNotDispatched();
});

test('uses the fallback when an installed locale cannot be resolved', function (): void {
    $locales = Mockery::mock(Locales::getFacadeRoot());
    $locales->shouldReceive('info')->once()->with('en')->andThrow(new UnknownLocaleException('en'));
    Locales::swap($locales);

    getJson(route('via.header', ['key' => 'test']), ['Accept-Language' => 'en'])
        ->assertOk()
        ->assertJsonPath('test', FakeTranslation::German);

    expect(app()->getLocale())->toBe('de');
    assertEventNotDispatched();
});

test('multiple accept-language', function (string $locale): void {
    $foo = 'test';

    setCustomLocale();

    getJson(route('via.header', ['key' => $foo]), [
        app(RouteNameLangConfig::class)->header => $locale,
    ])
        ->assertSuccessful()
        ->assertJsonPath($foo, FakeTranslation::French);

    assertEventDispatched();
})->with('locale headers');

test('selects the highest-quality installed language', function (string $header): void {
    getJson(route('via.header', ['key' => 'test']), ['Accept-Language' => $header])
        ->assertSuccessful()
        ->assertJsonPath('test', FakeTranslation::German);

    assertEventDispatched();
})->with([
    'higher quality appears later'        => ['fr;q=0.1,de;q=1'],
    'higher quality is uninstalled'       => ['sv,de;q=0.9'],
    'unacceptable and wildcard languages' => ['fr;q=0,*,de;q=0.9'],
    'unacceptable base language'          => ['fr-CA,fr;q=0,de;q=0.9'],
]);

test('selects an installed regional language before its base language', function (): void {
    file_put_contents(lang_path('zh_CN.json'), json_encode(['Foo' => '已加载所有资源。'], JSON_THROW_ON_ERROR));

    getJson(route('via.header', ['key' => 'test']), ['Accept-Language' => 'zh-cn,zh;q=0.9'])
        ->assertSuccessful()
        ->assertJsonPath('test', '已加载所有资源。');

    expect(app()->getLocale())->toBe('zh_CN');
    assertEventDispatched();
});

test('custom header', function (string $locale): void {
    config(['localization_routes.names.header' => 'X-Custom-Lang']);

    $foo = 'test';

    setCustomLocale();

    getJson(route('via.header', ['key' => $foo]), [
        'X-Custom-Lang' => $locale,
    ])
        ->assertSuccessful()
        ->assertJsonPath($foo, FakeTranslation::French);

    assertEventDispatched();
})->with('locale headers');
