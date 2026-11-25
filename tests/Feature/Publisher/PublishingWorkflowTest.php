<?php

declare(strict_types=1);

use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\File;
use LaravelLang\Exceptions\InvalidLocalesArgumentException;
use LaravelLang\Exceptions\InvalidTranslationKeyException;
use LaravelLang\Exceptions\InvalidTranslationPluginException;
use LaravelLang\Exceptions\InvalidTranslationSkipPatternException;
use LaravelLang\Exceptions\PathSearchException;
use LaravelLang\Locales\Facades\Locales;
use LaravelLang\Publisher\Collectors\PluginCollector;
use Symfony\Component\Process\Process;

use function Pest\Laravel\artisan;

beforeEach(function (): void {
    $this->publisherRoot = sys_get_temp_dir() . '/mona-publisher-' . bin2hex(random_bytes(8));

    app()->setBasePath($this->publisherRoot);
    app()->bind(PluginCollector::class);

    config()->set([
        'app.locale'                => 'fr',
        'app.fallback_locale'       => 'de',
        'localization_main.aliases' => [],
        'localization_main.styles'  => ['default'],
    ]);
});

afterEach(function (): void {
    File::deleteDirectory($this->publisherRoot);
});

test('rejects malformed locale arguments passed through Artisan', function (string $command, mixed $locales, string $message): void {
    expect(fn () => artisan($command, ['locales' => $locales])->run())
        ->toThrow(InvalidLocalesArgumentException::class, $message);
})->with(['lang:add', 'lang:rm'])->with([
    'single value'      => ['sv', 'The locales argument must be an array.'],
    'non-string member' => [[17], 'Each locale must be a string.'],
]);

describe('removing locales', function (): void {
    beforeEach(function (): void {
        config()->set('localization_main.aliases', ['sv' => 'sv_SE', 'fr' => 'fr_FR']);

        writePublisherPackage('tests/translations', [], [
            'messages.php'  => '{locale}/messages.php',
            'messages.json' => '{locale}.json',
            'vendor.php'    => 'vendor/demo/{locale}/messages.php',
            'vendor.json'   => 'vendor/demo/{locale}.json',
        ], [
            'source/messages.php'     => ['greeting' => 'Hello'],
            'source/messages.json'    => ['Hello' => 'Hello'],
            'source/vendor.php'       => ['greeting' => 'Hello'],
            'source/vendor.json'      => ['Hello' => 'Hello'],
            'locales/sv/default.json' => ['Hello' => 'Hej'],
        ]);
    });

    test('removes application and vendor translations by code or alias', function (string $add, string $remove): void {
        artisan('lang:add', ['locales' => [$add, 'en']])->assertSuccessful()->run();
        artisan('lang:update')->assertSuccessful()->run();

        [$php, $json, $vendorPhp, $vendorJson] = publisherTranslationPaths('sv');
        expect(File::getRequire($php))->toBe(['greeting' => 'Hej'])
            ->and(File::json($json))->toBe(['Hello' => 'Hej'])
            ->and(File::getRequire($vendorPhp))->toBe(['greeting' => 'Hej'])
            ->and(File::json($vendorJson))->toBe(['Hello' => 'Hej']);

        foreach (publisherTranslationPaths('sv_SE') as $path) {
            expect($path)->not->toBeFile();
        }

        expect(lang_path('sv_SE'))->not->toBeDirectory()
            ->and(lang_path('vendor/demo/sv_SE'))->not->toBeDirectory();

        Locales::set('sv_SE');
        expect(__('messages.greeting'))->toBe('Hej')
            ->and(__('Hello'))->toBe('Hej');
        Locales::set('fr');

        config()->set('localization_main.aliases.sv', 'sv-SE');

        $unrelated = lang_path('sv-SE/keep.txt');
        File::ensureDirectoryExists(dirname($unrelated));
        File::put($unrelated, 'Keep this file');

        $retained = [
            ...publisherTranslationPaths('fr'),
            ...publisherTranslationPaths('de'),
            ...publisherTranslationPaths('en'),
            $unrelated,
        ];
        $contents = array_map(File::get(...), $retained);

        artisan('lang:rm', ['locales' => [$remove, 'fr', 'de']])
            ->expectsOutputToContain('DONE')
            ->assertSuccessful()
            ->run();

        foreach (publisherTranslationPaths('sv') as $path) {
            expect($path)->not->toBeFile();
        }

        expect(lang_path('sv'))->not->toBeDirectory()
            ->and(lang_path('vendor/demo/sv'))->not->toBeDirectory()
            ->and(array_map(File::get(...), $retained))->toBe($contents);
    })->with([
        'from code to updated alias' => ['sv', 'sv-SE'],
        'from alias to code'         => ['sv_SE', 'sv'],
    ]);

    test('keeps the vendor directory when a locale alias matches its name', function (): void {
        artisan('lang:add', ['locales' => ['sv', 'en']])->assertSuccessful()->run();

        $path     = lang_path('vendor/demo/de/messages.php');
        $contents = File::get($path);

        config()->set('localization_main.aliases.sv', 'vendor');

        artisan('lang:rm', ['locales' => ['sv']])->assertSuccessful()->run();

        expect(lang_path('sv'))->not->toBeDirectory()
            ->and(File::get($path))->toBe($contents);
    });

    test('protects the current locale when its code is also an alias', function (): void {
        artisan('lang:add', ['locales' => ['sv']])->assertSuccessful()->run();
        $paths    = publisherTranslationPaths('fr');
        $contents = array_map(File::get(...), $paths);

        config()->set('localization_main.aliases', ['fr' => 'fr_FR', 'sv' => 'fr']);

        artisan('lang:rm', ['locales' => ['fr_FR']])
            ->expectsOutputToContain('PROTECTED')
            ->assertSuccessful()
            ->run();

        expect(array_map(File::get(...), $paths))->toBe($contents);
    });

    test('leaves translations alone for an unknown code', function (): void {
        artisan('lang:add', ['locales' => ['en']])->assertSuccessful()->run();

        $paths    = publisherTranslationPaths('en');
        $contents = array_map(File::get(...), $paths);

        artisan('lang:rm', ['locales' => ['not-a-locale']])
            ->expectsOutputToContain('INVALID')
            ->assertSuccessful()
            ->run();

        expect(array_map(File::get(...), $paths))->toBe($contents);
    });

    test('skips removing a locale twice', function (): void {
        artisan('lang:add', ['locales' => ['sv']])->assertSuccessful()->run();
        artisan('lang:rm', ['locales' => ['sv']])->assertSuccessful()->run();
        artisan('lang:rm', ['locales' => ['sv_SE']])
            ->expectsOutputToContain('SKIP')
            ->assertSuccessful()
            ->run();

        foreach (publisherTranslationPaths('sv') as $path) {
            expect($path)->not->toBeFile();
        }
    });

    test('reports failed deletion and continues removing other locales', function (string $target): void {
        artisan('lang:add', ['locales' => ['sv']])->assertSuccessful()->run();
        $path        = lang_path($target);
        $otherLocale = lang_path('en.json');

        writePublisherFiles([$otherLocale => ['Hello' => 'Hello']]);

        $this->partialMock(Filesystem::class)
            ->shouldReceive('delete')
            ->once()
            ->with(Mockery::on(static fn (string $actual): bool => str_replace('\\', '/', $actual) === str_replace('\\', '/', $path)))
            ->andReturnFalse();

        artisan('lang:rm', ['locales' => ['sv', 'en']])
            ->expectsOutputToContain('FAILED')
            ->expectsOutputToContain('DONE')
            ->assertFailed()
            ->run();

        expect(file_exists($path))->toBeTrue()
            ->and($otherLocale)->not->toBeFile();
    })->with([
        'PHP directory still contains a file' => 'sv/messages.php',
        'vendor JSON file cannot be deleted'  => 'vendor/demo/sv.json',
    ]);

    test('reports failure when the locale directory changes before it can be opened', function (): void {
        $path     = lang_path('sv');
        $contents = 'A replacement file from another process';

        File::ensureDirectoryExists(dirname($path));
        File::put($path, $contents);

        $this->partialMock(Filesystem::class)
            ->shouldReceive('isDirectory')
            ->once()
            ->with(Mockery::on(static fn (string $actual): bool => str_replace('\\', '/', $actual) === str_replace('\\', '/', $path)))
            ->andReturnTrue();

        artisan('lang:rm', ['locales' => ['sv']])->expectsOutputToContain('FAILED')->assertFailed()->run();

        expect(File::get($path))->toBe($contents);
    });

    test('removes linked directories in a locale and preserves their targets', function (bool $nested): void {
        $shared    = $this->publisherRoot . '/shared';
        $locale    = lang_path('sv');
        $directory = $nested ? $locale . '/linked' : $locale;
        $file      = $shared . '/messages.php';
        $messages  = ['greeting' => 'Hej'];

        File::ensureDirectoryExists(dirname($directory));
        writePublisherFiles([$file => $messages]);
        if (PHP_OS_FAMILY === 'Windows') {
            (new Process([
                'powershell.exe',
                '-NoProfile',
                '-NonInteractive',
                '-Command',
                'New-Item -ItemType Junction -Path $env:MONA_LOCALE_LINK -Target $env:MONA_LOCALE_TARGET -ErrorAction Stop',
            ], env: ['MONA_LOCALE_LINK' => $directory, 'MONA_LOCALE_TARGET' => $shared]))->mustRun();
        } elseif (! @symlink($shared, $directory)) {
            $this->markTestSkipped('Creating directory symlinks is unavailable on this system.');
        }

        try {
            artisan('lang:rm', ['locales' => ['sv']])->expectsOutputToContain('DONE')->assertSuccessful()->run();

            expect(@lstat($locale))->toBeFalse()
                ->and(@lstat($directory))->toBeFalse()
                ->and(File::getRequire($file))->toBe($messages);
        } finally {
            if (@lstat($directory) !== false) {
                PHP_OS_FAMILY === 'Windows' ? rmdir($directory) : File::delete($directory);
            }
        }
    })->with(['locale root' => false, 'nested directory' => true]);

    test('keeps translations when the vendor directories cannot be listed', function (): void {
        artisan('lang:add', ['locales' => ['sv']])->assertSuccessful()->run();

        $paths    = publisherTranslationPaths('sv');
        $contents = array_map(File::get(...), $paths);

        $this->partialMock(Filesystem::class)
            ->shouldReceive('glob')->once()->with(lang_path('vendor/*'), GLOB_ONLYDIR)->andReturnFalse();

        expect(fn () => artisan('lang:rm', ['locales' => ['sv']])->run())
            ->toThrow(PathSearchException::class, 'Unable to find vendor translation directories.');

        expect(array_map(File::get(...), $paths))->toBe($contents);
    });

    test('asks before removing all unprotected locales', function (string $confirmation): void {
        artisan('lang:add', ['locales' => ['en', 'sv']])->assertSuccessful()->run();

        $protected         = [...publisherTranslationPaths('fr'), ...publisherTranslationPaths('de')];
        $protectedContents = array_map(File::get(...), $protected);
        $optional          = [...publisherTranslationPaths('en'), ...publisherTranslationPaths('sv')];
        $optionalContents  = array_map(File::get(...), $optional);

        artisan('lang:rm')
            ->expectsConfirmation('Do you want to remove all localizations?', $confirmation)
            ->assertSuccessful()
            ->run();

        expect(array_map(File::get(...), $protected))->toBe($protectedContents);

        if ($confirmation === 'yes') {
            foreach ($optional as $path) {
                expect($path)->not->toBeFile();
            }
            expect(lang_path('en'))->not->toBeDirectory()
                ->and(lang_path('sv'))->not->toBeDirectory();
        } else {
            expect(array_map(File::get(...), $optional))->toBe($optionalContents);
        }
    })->with(['yes', 'no']);

    test('keeps localizations when run without interactive confirmation', function (): void {
        artisan('lang:add', ['locales' => ['sv']])->assertSuccessful()->run();

        $paths    = publisherTranslationPaths('sv');
        $contents = array_map(File::get(...), $paths);

        $this->withoutMockingConsoleOutput();

        expect(artisan('lang:rm', ['--no-interaction' => true]))->toBe(0)
            ->and(array_map(File::get(...), $paths))->toBe($contents);
    });
});

describe('updating locales from installed manifests', function (): void {
    beforeEach(function (): void {
        writePublisherPackage('tests/compatible', ['vendor' => 'composer/semver', 'version' => '^3.6'], [
            'messages.php'  => '{locale}/messages.php',
            'messages.json' => '{locale}.json',
        ], [
            'source/messages.php'     => ['nested' => ['greeting' => 'Hello', 'farewell' => 'Goodbye']],
            'source/messages.json'    => ['Hello' => 'Hello', 'Goodbye' => 'Goodbye'],
            'locales/fr/default.json' => ['Hello' => 'From the package'],
        ]);

        writePublisherPackage('tests/incompatible', ['vendor' => 'composer/semver', 'version' => '^999.0'], [
            'incompatible.php' => '{locale}/incompatible.php',
            'skip-source.php'  => '{locale}/skip-source.php',
            'skip-target.php'  => '{locale}/skip-target.php',
        ], [
            'source/incompatible.php' => ['status' => 'Forced incompatible package'],
            'source/skip-source.php'  => ['status' => 'Skipped source'],
            'source/skip-target.php'  => ['status' => 'Skipped target'],
        ]);

        writePublisherPackage('tests/missing', ['vendor' => 'tests/absent-dependency'], [
            'missing.php' => '{locale}/missing.php',
        ], [
            'source/missing.php' => ['status' => 'Forced absent package'],
        ]);

        writePublisherFiles([
            base_path('vendor/laravel-lang/library/locales/fr/default.json') => [
                'Hello'   => 'From the library',
                'Goodbye' => 'Library farewell',
            ],
            lang_path('fr/messages.php') => ['nested' => ['custom' => 'Custom PHP']],
            lang_path('fr.json')         => ['Custom key' => 'Custom JSON'],
        ]);
    });

    test('merges compatible translations and writes the same files on repeat', function (): void {
        artisan('lang:update')->assertSuccessful()->run();

        expect(File::getRequire(lang_path('fr/messages.php')))->toEqual([
            'nested' => [
                'greeting' => 'From the package',
                'farewell' => 'Library farewell',
                'custom'   => 'Custom PHP',
            ],
        ])->and(File::json(lang_path('fr.json')))->toEqual([
            'Hello'      => 'From the package',
            'Goodbye'    => 'Library farewell',
            'Custom key' => 'Custom JSON',
        ])->and(lang_path('fr/incompatible.php'))->not->toBeFile()
            ->and(lang_path('fr/missing.php'))->not->toBeFile();

        $paths    = [lang_path('fr/messages.php'), lang_path('fr.json'), lang_path('de/messages.php'), lang_path('de.json')];
        $contents = array_map(File::get(...), $paths);

        artisan('lang:update')->assertSuccessful()->run();

        expect(array_map(File::get(...), $paths))->toBe($contents);
    });

    test('forces missing and incompatible dependencies while respecting source and target skips', function (): void {
        config()->set([
            'localization_main.force' => ['composer/semver', 'tests/absent-dependency'],
            'localization_main.skip'  => ['*/tests/incompatible/source/skip-source.php', '*/lang/*/skip-target.php'],
        ]);

        $skippedTarget = lang_path('fr/skip-target.php');
        writePublisherFiles([$skippedTarget => ['status' => 'Custom target']]);
        $originalTarget = File::get($skippedTarget);

        artisan('lang:update')->assertSuccessful()->run();

        expect(File::getRequire(lang_path('fr/incompatible.php')))->toBe(['status' => 'Forced incompatible package'])
            ->and(File::getRequire(lang_path('fr/missing.php')))->toBe(['status' => 'Forced absent package'])
            ->and(lang_path('fr/skip-source.php'))->not->toBeFile()
            ->and(File::get($skippedTarget))->toBe($originalTarget);
    });

    test('rejects a stream resource as a skip pattern', function (): void {
        $pattern = fopen('php://memory', 'r+');

        try {
            config()->set('localization_main.skip', [$pattern]);

            expect(fn () => artisan('lang:update')->run())
                ->toThrow(InvalidTranslationSkipPatternException::class, 'Translation skip patterns must be convertible to strings.');

            expect(File::getRequire(lang_path('fr/messages.php')))->toBe(['nested' => ['custom' => 'Custom PHP']]);
        } finally {
            fclose($pattern);
        }
    });
});

test('publishes translations when a versioned dependency is replaced by Laravel', function (): void {
    writePublisherPackage('tests/replaced', ['vendor' => 'illuminate/support', 'version' => '*'], [
        'messages.php' => '{locale}/messages.php',
    ], [
        'source/messages.php'     => ['greeting' => 'Hello'],
        'locales/fr/default.json' => ['Hello' => 'Bonjour'],
    ]);

    artisan('lang:update')->assertSuccessful()->run();

    expect(File::getRequire(lang_path('fr/messages.php')))->toBe(['greeting' => 'Bonjour']);
});

test('publishes HTTP statuses by code and keeps the same codes on repeated updates', function (): void {
    $source = require dirname(__DIR__, 3) . '/src/HttpStatuses/source/http-statuses.php';

    writePublisherPackage('laravel-lang/http-statuses', [], ['http-statuses.php' => '{locale}/http-statuses.php'], [
        'source/http-statuses.php' => $source,
        'locales/fr/default.json'  => ['Not Found' => 'Introuvable'],
    ]);

    $french = lang_path('fr/http-statuses.php');
    $german = lang_path('de/http-statuses.php');
    writePublisherFiles([$german => [404 => 'Stale translation', 777 => 'Custom status']]);

    artisan('lang:update')->assertSuccessful()->run();

    expect(File::getRequire($french))->toEqual(array_replace($source, [404 => 'Introuvable']))
        ->and(File::getRequire($german))->toEqual($source + [777 => 'Custom status'])
        ->and(__('http-statuses.404'))->toBe('Introuvable');

    $contents = array_map(File::get(...), [$french, $german]);

    artisan('lang:update')->assertSuccessful()->run();

    expect(array_map(File::get(...), [$french, $german]))->toBe($contents);
});

test('updates numeric JSON keys using package and style precedence', function (): void {
    config()->set('localization_main.styles', ['default', 'formal']);

    writePublisherPackage('tests/translations', [], ['messages.json' => '{locale}.json'], [
        'source/messages.json'    => [20 => '20', 21 => '21', 24 => '24', 27 => '27', 42 => '42'],
        'locales/fr/default.json' => [20 => 'Package default', 21 => 'Package default'],
        'locales/fr/formal.json'  => [21 => 'Package formal'],
    ]);

    $path = lang_path('fr.json');
    writePublisherFiles([
        base_path('vendor/laravel-lang/library/locales/fr/default.json') => [20 => 'Library default', 24 => 'Library default', 27 => 'Library default'],
        base_path('vendor/laravel-lang/library/locales/fr/formal.json')  => [24 => 'Library formal'],
        $path                                                            => [20 => 'Stale translation', 99 => 'Custom translation'],
    ]);

    artisan('lang:update')->assertSuccessful()->run();

    expect(File::json($path))->toBe([
        20 => 'Package default',
        21 => 'Package formal',
        24 => 'Library formal',
        27 => 'Library default',
        42 => '42',
        99 => 'Custom translation',
    ]);

    $content = File::get($path);

    artisan('lang:update')->assertSuccessful()->run();

    expect(File::get($path))->toBe($content);
});

test('publishes the application validation sections after the sorted rules', function (): void {
    $source = [
        'zeta'   => 'Last rule',
        'custom' => ['zeta' => 'Last message', 'alpha' => 'First message'],
        'nested' => ['zeta' => 'Last value', 'custom' => 'Nested message', 'attributes' => 'Nested attributes'],
        'alpha'  => 'First rule',
    ];

    writePublisherPackage('tests/translations', [], [
        'validation.php'        => '{locale}/validation.php',
        'messages.php'          => '{locale}/messages.php',
        'vendor/validation.php' => 'vendor/demo/{locale}/validation.php',
    ], [
        'source/validation.php'        => $source,
        'source/messages.php'          => $source,
        'source/vendor/validation.php' => $source,
    ]);
    writePublisherFiles([
        lang_path('fr/validation.php') => [
            'middle'     => 'Existing rule',
            'attributes' => ['zeta' => 'Last attribute', 'alpha' => 'First attribute'],
            'custom'     => ['middle' => 'Existing message', 'alpha' => 'Old message'],
        ],
    ]);

    artisan('lang:update')->assertSuccessful()->run();

    $nested = ['attributes' => 'Nested attributes', 'custom' => 'Nested message', 'zeta' => 'Last value'];
    $sorted = [
        'alpha'  => 'First rule',
        'custom' => ['alpha' => 'First message', 'zeta' => 'Last message'],
        'nested' => $nested,
        'zeta'   => 'Last rule',
    ];

    expect(File::getRequire(lang_path('fr/validation.php')))->toBe([
        'alpha'      => 'First rule',
        'middle'     => 'Existing rule',
        'nested'     => $nested,
        'zeta'       => 'Last rule',
        'custom'     => ['alpha' => 'First message', 'middle' => 'Existing message', 'zeta' => 'Last message'],
        'attributes' => ['alpha' => 'First attribute', 'zeta' => 'Last attribute'],
    ])->and(File::getRequire(lang_path('de/validation.php')))->toBe([
        'alpha'  => 'First rule',
        'nested' => $nested,
        'zeta'   => 'Last rule',
        'custom' => ['alpha' => 'First message', 'zeta' => 'Last message'],
    ])->and(File::getRequire(lang_path('fr/messages.php')))->toBe($sorted)
        ->and(File::getRequire(lang_path('vendor/demo/fr/validation.php')))->toBe($sorted);
});

test('removes the default custom validation message when publishing translations', function (string $directory): void {
    $rules       = ['required' => 'This field is required.'];
    $placeholder = ['attribute-name' => ['rule-name' => 'custom-message']];
    $custom      = ['attribute-name' => ['required' => 'Enter a name.'], 'email' => ['required' => 'Enter an email.']];

    writePublisherPackage('tests/translations', [], ['validation.php' => $directory . '{locale}/validation.php'], [
        'source/validation.php' => $rules + ['custom' => $placeholder],
    ]);

    $french = lang_path($directory . 'fr/validation.php');
    $german = lang_path($directory . 'de/validation.php');

    writePublisherFiles([$french => $rules + ['custom' => array_replace_recursive($custom, $placeholder)]]);

    artisan('lang:update')->assertSuccessful()->run();

    expect(File::getRequire($french))->toEqual($rules + ['custom' => $custom])
        ->and(File::getRequire($german))->toBe($rules);

    $custom['attribute-name']['rule-name'] = 'Use a different name.';

    writePublisherFiles([$french => $rules + ['custom' => $custom]]);

    artisan('lang:update')->assertSuccessful()->run();

    expect(File::getRequire($french))->toEqual($rules + ['custom' => $custom]);
})->with(['application' => '', 'vendor' => 'vendor/demo/']);

test('publishes smart punctuation for each locale when enabled', function (bool $enabled): void {
    config()->set('localization_punctuation.enabled', $enabled);

    $source      = '"It\'s ready, :name" -- \'now\'...';
    $translation = '"C\'est prêt, :name" -- \'maintenant\'...';

    writePublisherPackage('tests/translations', [], [
        'messages.php'  => '{locale}/messages.php',
        'messages.json' => '{locale}.json',
    ], [
        'source/messages.php'     => ['nested' => ['message' => $source]],
        'source/messages.json'    => [$source => $source],
        'locales/fr/default.json' => [$source => $translation],
    ]);
    writePublisherFiles([
        lang_path('fr.json') => [$source => 'Previous message', 'Custom' => '"Custom" -- ...'],
    ]);

    artisan('lang:update')->assertSuccessful()->run();

    $french = $enabled ? "«\u{a0}C’est prêt, :name\u{a0}» – ‘maintenant’…" : $translation;
    $german = $enabled ? '“It’s ready, :name” – ‘now’…' : $source;

    expect(File::getRequire(lang_path('fr/messages.php')))->toBe(['nested' => ['message' => $french]])
        ->and(File::getRequire(lang_path('de/messages.php')))->toBe(['nested' => ['message' => $german]])
        ->and(File::json(lang_path('fr.json')))->toBe([$source => $french, 'Custom' => '"Custom" -- ...'])
        ->and(File::json(lang_path('de.json')))->toBe([$source => $german]);
})->with(['enabled' => true, 'disabled' => false]);

test('preserves formatting and markup when publishing smart punctuation', function (): void {
    config()->set([
        'localization_punctuation.enabled'                        => true,
        'localization_punctuation.locales.fr.single_quote_opener' => '‹',
        'localization_punctuation.locales.fr.single_quote_closer' => '›',
    ]);

    writePublisherPackage('tests/translations', [], ['messages.php' => '{locale}/messages.php'], [
        'source/messages.php' => [
            'empty'      => '',
            'markup'     => "Visit <a href=\"/docs--guide\" title=\"Docs...\">'link'</a>...",
            'multiline'  => "  \"First\"...\r\n\r\n  \"Second\"...  \r\n",
            'reference'  => '[source]: https://example.org',
            'whitespace' => " \t",
        ],
    ]);

    artisan('lang:update')->assertSuccessful()->run();

    expect(File::getRequire(lang_path('fr/messages.php')))->toBe([
        'empty'      => '',
        'markup'     => 'Visit <a href="/docs--guide" title="Docs...">‹link›</a>…',
        'multiline'  => "  «\u{a0}First\u{a0}»…\r\n\r\n  «\u{a0}Second\u{a0}»…  \r\n",
        'reference'  => '[source]: https://example.org',
        'whitespace' => " \t",
    ]);
});

test('resolves scalar PHP source values against the translation dictionary', function (): void {
    writePublisherPackage('tests/translations', [], ['values.php' => '{locale}/values.php'], [
        'source/values.php'       => ['boolean' => false, 'float' => 1.5, 'null' => null],
        'locales/fr/default.json' => ['Zero', 'One', '' => 'Empty'],
    ]);

    artisan('lang:update')->assertSuccessful()->run();

    expect(File::getRequire(lang_path('fr/values.php')))->toBe([
        'boolean' => 'Zero',
        'float'   => 'One',
        'null'    => 'Empty',
    ]);
});

test('preserves non-string translation values with or without smart punctuation', function (bool $enabled): void {
    config()->set('localization_punctuation.enabled', $enabled);

    writePublisherPackage('tests/translations', [], ['values.php' => '{locale}/values.php'], [
        'source/values.php' => [
            'boolean'             => false,
            'count'               => 12,
            'fraction'            => 1.5,
            'group'               => 'Group',
            'missing'             => null,
            'translated_boolean'  => 'Boolean',
            'translated_fraction' => 'Fraction',
        ],
        'locales/fr/default.json' => ['Group' => ['item' => 'Item'], 'Boolean' => false, 'Fraction' => 1.5],
    ]);

    artisan('lang:update')->assertSuccessful()->run();

    expect(File::getRequire(lang_path('fr/values.php')))->toBe([
        'boolean'             => false,
        'count'               => 12,
        'fraction'            => 1.5,
        'group'               => ['item' => 'Item'],
        'missing'             => null,
        'translated_boolean'  => false,
        'translated_fraction' => 1.5,
    ]);
})->with(['enabled' => true, 'disabled' => false]);

test('rejects non-scalar PHP source values before writing the corresponding translation', function (): void {
    $target = lang_path('fr/messages.php');

    writePublisherPackage('tests/translations', [], ['messages.php' => '{locale}/messages.php'], [
        'source/messages.php' => ['message' => new stdClass],
    ]);
    writePublisherFiles([$target => ['message' => 'Keep this translation']]);

    expect(fn () => artisan('lang:update')->run())
        ->toThrow(InvalidTranslationKeyException::class, 'Translation keys must be scalar or null.');

    expect(File::getRequire($target))->toBe(['message' => 'Keep this translation']);
});

test('publishes valid entries alongside a non-object plugin entry', function (): void {
    $directory = base_path('vendor/tests/translations');

    writePublisherFiles([
        "$directory/laravel_lang.json" => ['plugins' => [null, [
            'files' => [['source' => 'messages.php', 'target' => '{locale}/messages.php']],
        ]]],
        "$directory/source/messages.php"     => ['greeting' => 'Hello'],
        "$directory/locales/fr/default.json" => ['Hello' => 'Bonjour'],
    ]);

    artisan('lang:update')->assertSuccessful()->run();

    expect(File::getRequire(lang_path('fr/messages.php')))->toBe(['greeting' => 'Bonjour']);
});

test('reports when installed plugin manifests cannot be listed', function (): void {
    $pattern = base_path('vendor/*/*/laravel_lang.json');

    $this->partialMock(Filesystem::class)
        ->shouldReceive('glob')->once()->with($pattern, 0)->andReturnFalse();

    expect(fn () => artisan('lang:update')->run())
        ->toThrow(PathSearchException::class, "Unable to find paths matching [$pattern].");
});

test('updates translations from project manifests based on the root project and force configuration', function (
    array $requirements,
    array $forced,
    bool $expected,
): void {
    config()->set('localization_main.force', $forced);

    writePublisherPackage('tests/project', ['project' => true] + $requirements, [
        'project.php' => '{locale}/project.php',
    ], [
        'source/project.php'      => ['message' => 'Hello'],
        'locales/fr/default.json' => ['Hello' => 'Bonjour'],
    ]);

    artisan('lang:update')->assertSuccessful()->run();

    $path = lang_path('fr/project.php');

    if ($expected) {
        expect(File::getRequire($path))->toBe(['message' => 'Bonjour']);
    } else {
        expect($path)->not->toBeFile();
    }
})->with([
    'no project name'         => [[], [], true],
    'root project'            => [['vendor' => 'laravel-lang/mona'], [], true],
    'installed dependency'    => [['vendor' => 'composer/semver'], [], false],
    'forced non-root project' => [['vendor' => 'tests/other-project'], ['tests/other-project'], true],
]);

test('updates nested JSON translations and skips empty sources and excluded styles', function (): void {
    config()->set([
        'localization_main.styles' => ['default', 'formal'],
        'localization_main.skip'   => ['*/tests/translations/locales/fr/formal.json'],
    ]);

    writePublisherPackage('tests/translations', [], [
        'nested.json' => '{locale}/nested.json',
        'empty.php'   => '{locale}/empty.php',
    ], [
        'source/nested.json'      => ['menu' => ['hello' => 'Hello', 'bye' => 'Bye']],
        'source/empty.php'        => [],
        'locales/fr/default.json' => ['Hello' => 'Bonjour', 'Bye' => 'Au revoir'],
        'locales/fr/formal.json'  => ['Hello' => 'Salutations'],
    ]);

    artisan('lang:update')->assertSuccessful()->run();

    expect(File::json(lang_path('fr/nested.json')))->toBe(['menu' => ['hello' => 'Bonjour', 'bye' => 'Au revoir']])
        ->and(lang_path('fr/empty.php'))->not->toBeFile();
});

test('rejects invalid translation plugin manifests', function (array $plugin, string $message): void {
    writePublisherPackage('tests/invalid', $plugin + ['vendor' => 'composer/semver'], [], []);

    expect(fn () => app(PluginCollector::class)->find())
        ->toThrow(InvalidTranslationPluginException::class, $message);
})->with([
    'vendor' => [
        ['vendor' => []],
        'The translation plugin vendor must be a string or null.',
    ],
    'version' => [
        ['version' => []],
        'The translation plugin version must be a string or null.',
    ],
    'source' => [
        ['files' => [['source' => false, 'target' => '{locale}/messages.php']]],
        'A translation plugin file must have a string source path.',
    ],
    'target' => [
        ['files' => [['source' => 'messages.php', 'target' => false]]],
        'A translation plugin file must have a string target path.',
    ],
]);

function publisherTranslationPaths(string $locale): array
{
    return [
        lang_path("$locale/messages.php"),
        lang_path("$locale.json"),
        lang_path("vendor/demo/$locale/messages.php"),
        lang_path("vendor/demo/$locale.json"),
    ];
}

function writePublisherPackage(string $package, array $requirements, array $targets, array $files): void
{
    $entries = [];

    foreach ($targets as $source => $target) {
        $entries[] = ['source' => $source, 'target' => $target];
    }

    $manifest = ['plugins' => [$requirements + ['files' => $entries]]];
    foreach (['laravel_lang.json' => $manifest] + $files as $path => $content) {
        writePublisherFiles([base_path("vendor/$package/$path") => $content]);
    }
}

function writePublisherFiles(array $files): void
{
    foreach ($files as $path => $content) {
        File::ensureDirectoryExists(dirname($path));
        File::put($path, str_ends_with($path, '.json')
            ? json_encode($content, JSON_THROW_ON_ERROR)
            : '<?php return ' . var_export($content, true) . ';');
    }
}
