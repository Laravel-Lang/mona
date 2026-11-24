<?php

declare(strict_types=1);

use Illuminate\Filesystem\Filesystem;
use LaravelLang\Toolkit\Services\TranslationKeysService;

beforeEach(function (): void {
    $this->translationDirectory = sys_get_temp_dir() . '/mona-translation-keys-' . bin2hex(random_bytes(8));

    (new Filesystem)->makeDirectory($this->translationDirectory);
});

afterEach(function (): void {
    (new Filesystem)->deleteDirectory($this->translationDirectory);
});

test('finds supported translation calls recursively in supported source files', function (): void {
    $filesystem = new Filesystem;
    $filesystem->makeDirectory($this->translationDirectory . '/nested');

    $sources = [
        'php' => <<<'SOURCE'
            <?php
            __('Alpha');
            trans ( 'Bravo' );
            trans_choice('Charlie', 2);
            Lang :: get ('Delta', []);
            Lang :: choice ('Echo', 2);
            $this->getTranslator () -> get ('Foxtrot');
            $this->getTranslator () -> choice ('Golf', 2);
            SOURCE,
        'json' => <<<'SOURCE'
            {"source": "lang('Hotel')"}
            SOURCE,
        'js' => <<<'SOURCE'
            $t ('India');
            tChoice ('Juliet', 2);
            SOURCE,
        'ts' => <<<'SOURCE'
            wTrans ('Kilo');
            wTransChoice ('Lima', 2);
            SOURCE,
        'vue' => <<<'SOURCE'
            <template>{{ lang ('Mike') }}</template>
            SOURCE,
        'stub' => <<<'SOURCE'
            fail ('November');
            SOURCE,
    ];

    foreach ($sources as $extension => $source) {
        $filesystem->put($this->translationDirectory . '/nested/source.' . $extension, $source);
    }

    $filesystem->put($this->translationDirectory . '/ignored.txt', "__('Ignored')");

    $keys = ['Alpha', 'Bravo', 'Charlie', 'Delta', 'Echo', 'Foxtrot', 'Golf', 'Hotel', 'India', 'Juliet', 'Kilo', 'Lima', 'Mike', 'November'];

    expect((new TranslationKeysService)->find($this->translationDirectory))->toBe(array_combine($keys, $keys));
});

test('reads escaped quotes and multiline literals and returns unique sorted keys', function (): void {
    $source = <<<'SOURCE'
        <?php
        __('Zulu');
        __('It\'s ready');
        trans("Say \"Hello\"");
        __("
        Multiple
        lines
        ");
        __('Alpha');
        __('Zulu');
        SOURCE;

    (new Filesystem)->put($this->translationDirectory . '/source.php', str_replace("\r\n", "\n", $source));

    $keys = ['Alpha', "It's ready", "Multiple\nlines", 'Say "Hello"', 'Zulu'];

    expect((new TranslationKeysService)->find($this->translationDirectory))->toBe(array_combine($keys, $keys));
});

test('excludes dynamic expressions generic methods and non translatable keys', function (): void {
    (new Filesystem)->put($this->translationDirectory . '/source.php', <<<'SOURCE'
        <?php
        __('Keep this');
        __($key);
        __('Dynamic '.$key);
        __($condition ? 'Either' : 'Other');
        __("Hello $name");
        __('Hello ${name}');
        __('auth.failed');
        __('pagination.next');
        __('passwords.reset');
        __('validation.required');
        __('package::Welcome');
        __('lowercase.key');
        __('lowercase-key');
        __('lowercase_key');
        __('lowercase:key');
        __('UPPER_CASE_KEY');
        __('Content-Type');
        __('prototype');
        __('constructor');
        __('object');
        __('???');
        __('');
        __('Is it ? This : That');
        get('Generic get');
        choice('Generic choice');
        $service->get('Service get');
        $service->choice('Service choice');
        SOURCE);

    expect((new TranslationKeysService)->find($this->translationDirectory))->toBe(['Keep this' => 'Keep this']);
});

test('starts fresh when the same finder is used for another directory', function (): void {
    $filesystem = new Filesystem;
    $filesystem->makeDirectory($this->translationDirectory . '/first');
    $filesystem->makeDirectory($this->translationDirectory . '/second');
    $filesystem->put($this->translationDirectory . '/first/source.php', "__('First')");
    $filesystem->put($this->translationDirectory . '/second/source.php', "__('Second')");

    $finder = new TranslationKeysService;

    expect($finder->find($this->translationDirectory . '/first'))->toBe(['First' => 'First'])
        ->and($finder->find($this->translationDirectory . '/second'))->toBe(['Second' => 'Second']);
});
