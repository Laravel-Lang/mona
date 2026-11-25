<?php

declare(strict_types=1);

use Illuminate\Filesystem\Filesystem as BaseFilesystem;
use LaravelLang\Exceptions\InvalidJsonEncodeException;
use LaravelLang\Exceptions\InvalidTranslationFileContentException;
use LaravelLang\Filesystem\Filesystem;
use LaravelLang\Filesystem\Processors\JsonProcessor;

test('replaces an existing PHP translation when merging is disabled', function (): void {
    $path       = lang_path('fr/replaced.php');
    $filesystem = app(Filesystem::class);

    $filesystem->storeArray($path, ['nested' => ['old' => 'Old translation']]);
    $filesystem->storeArray($path, ['nested' => ['new' => 'New translation']]);

    expect(require $path)->toBe(['nested' => ['new' => 'New translation']]);
});

test('quotes numeric PHP keys and formats each nested entry on its own line', function (bool $trailingKeys): void {
    $path       = lang_path('fr/statuses.php');
    $filesystem = app(Filesystem::class);

    file_put_contents($path, <<<'PHP'
        <?php return ['Unknown Error', 100 => 'Continue', 'Switching Protocols', '04' => 'Zero-padded', -5 => 'Negative'];
        PHP);

    $translations = [
        'nested' => [
            'deep' => ['label' => 'Label'],
            'list' => ['First', 'Second'],
            'text' => "An example:\n    '100' => 'Continue',",
        ],
        'z-last' => 'Last in sort order',
    ];

    if ($trailingKeys) {
        $filesystem->storePhpArrayWithTrailingKeys($path, $translations, ['nested'], merge: true);
    } else {
        $filesystem->storeArray($path, $translations, merge: true);
    }

    $content = $filesystem->fromString($path);
    $written = require $path;

    expect($written)
        ->toHaveCount(7)
        ->and($written[0])->toBe('Unknown Error')
        ->and($written[100])->toBe('Continue')
        ->and($written[101])->toBe('Switching Protocols')
        ->and($written['04'])->toBe('Zero-padded')
        ->and($written[-5])->toBe('Negative')
        ->and($written['nested'])->toEqual($translations['nested'])
        ->and(array_key_last($written))->toBe($trailingKeys ? 'nested' : 'z-last');

    expect($content)
        ->toMatch("/^    '-5'\\h*=> 'Negative',\\R/m")
        ->toMatch("/^    '0'\\h*=> 'Unknown Error',\\R/m")
        ->toMatch("/^    '100'\\h*=> 'Continue',\\R/m")
        ->toMatch("/^    '101'\\h*=> 'Switching Protocols',\\R/m")
        ->toMatch("/^    'nested'\\h*=> \\[\\R/m")
        ->toMatch("/^        'deep'\\h*=> \\[\\R            'label'\\h*=> 'Label',\\R        \\],\\R/m")
        ->toMatch("/^        'list'\\h*=> \\[\\R            '0'\\h*=> 'First',\\R            '1'\\h*=> 'Second',\\R        \\],\\R/m");
})->with([
    'normal publishing' => false,
    'trailing keys'     => true,
]);

test('writes numeric JSON keys as explicit object properties at every level', function (): void {
    $path       = lang_path('fr.json');
    $filesystem = app(Filesystem::class);
    $nested     = ['First', 'Second'];

    $filesystem->storeArray($path, ['Top-level value', $nested]);

    $json = json_decode($filesystem->fromString($path), flags: JSON_THROW_ON_ERROR);

    expect($json)->toBeInstanceOf(stdClass::class)
        ->and($json->{'0'})->toBe('Top-level value')
        ->and($json->{'1'})->toBeInstanceOf(stdClass::class)
        ->and($json->{'1'}->{'0'})->toBe('First')
        ->and($json->{'1'}->{'1'})->toBe('Second')
        ->and($filesystem->read($path))->toBe(['Top-level value', $nested]);

    expect($filesystem->fromString($path))
        ->toMatch('/^    "1": \{\R        "0": "First",\R        "1": "Second"\R    }\R/m');
});

test('rejects invalid PHP translation arrays', function (string $contents, ?string $key, string $message): void {
    $path = lang_path('fr/invalid.php');
    file_put_contents($path, $contents);

    expect(fn () => app(Filesystem::class)->fromPhp($path, $key))
        ->toThrow(InvalidTranslationFileContentException::class, sprintf($message, $path));
})->with([
    'validates the file'          => ['<?php return "invalid";', null, 'The file [%s] must return an array.'],
    'validates the requested key' => [
        '<?php return ["nested" => "invalid"];',
        'nested',
        'The value for key [nested] in [%s] must be an array.',
    ],
]);

test('reports JSON encoding failures through the filesystem', function (): void {
    $path = lang_path('fr/invalid.json');

    expect(fn () => app(Filesystem::class)->storeArray($path, ['invalid' => "\xFF"]))
        ->toThrow(InvalidJsonEncodeException::class, 'Malformed UTF-8');
});

test('preserves JSON translations when an encoder without throw-on-error fails', function (): void {
    $this->instance(JsonProcessor::class, new class (app(BaseFilesystem::class)) extends JsonProcessor {
        protected int $flags = JSON_PRETTY_PRINT;
    });

    $path       = lang_path('fr/encoded.json');
    $filesystem = app(Filesystem::class);
    $original   = ['valid' => 'Bonjour'];

    $filesystem->storeArray($path, $original);

    expect(fn () => $filesystem->storeArray($path, ['invalid' => "\xFF"]))
        ->toThrow(InvalidJsonEncodeException::class, 'Malformed UTF-8');

    expect($filesystem->read($path))->toBe($original);
});
