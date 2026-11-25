<?php

declare(strict_types=1);

use Illuminate\Filesystem\Filesystem;
use LaravelLang\Exceptions\InvalidJsonEncodeException;
use LaravelLang\Exceptions\InvalidTranslationStatusException;
use LaravelLang\Exceptions\TranslationFileException;
use LaravelLang\LocaleList\Locale;
use LaravelLang\Toolkit\Commands\CreateLocaleCommand;
use LaravelLang\Toolkit\Commands\DeleteLocaleCommand;
use LaravelLang\Toolkit\Commands\StatusCommand;
use LaravelLang\Toolkit\Commands\SyncLibraryCommand;
use LaravelLang\Toolkit\Commands\SyncPackageCommand;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Console\Tester\ApplicationTester;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\Process\Process;

beforeEach(function (): void {
    $this->toolkitFilesystem = new Filesystem;
    $this->toolkitDirectory  = sys_get_temp_dir() . '/mona-toolkit-' . bin2hex(random_bytes(8));
    $this->toolkitFilesystem->ensureDirectoryExists($this->toolkitDirectory);
});

afterEach(function (): void {
    $this->toolkitFilesystem->deleteDirectory($this->toolkitDirectory);
});

function toolkitFile(string $path, string $contents): void
{
    $filesystem = new Filesystem;
    $filesystem->ensureDirectoryExists(dirname($path));
    $filesystem->put($path, $contents);
}

function runToolkit(string $command, array $arguments): ApplicationTester
{
    $application = new Application;
    $application->setAutoExit(false);
    $application->addCommand(new CreateLocaleCommand);
    $application->addCommand(new DeleteLocaleCommand);
    $application->addCommand(new StatusCommand);
    $application->addCommand(new SyncLibraryCommand);
    $application->addCommand(new SyncPackageCommand);

    $tester = new ApplicationTester($application);
    $tester->run(['command' => $command, ...$arguments], ['capture_stderr_separately' => true]);

    return $tester;
}

it('registers the locale and synchronization commands in the Toolkit executable', function (): void {
    $process = new Process([PHP_BINARY, dirname(__DIR__, 3) . '/src/Toolkit/bin/toolkit', 'list', '--raw']);
    $process->run();

    expect($process->getExitCode())->toBe(Command::SUCCESS, $process->getErrorOutput());

    preg_match_all('/^\S+/m', $process->getOutput(), $commands);

    expect($commands[0])->toContain('create-locale', 'delete-locale', 'migrate-locales', 'status', 'sync:library', 'sync:package');
});

it('prunes obsolete translations and preserves active translations and metadata', function (string $command): void {
    $root      = $this->toolkitDirectory;
    $package   = $root . '/Package';
    $locales   = ($command === 'sync:package' ? $package : $root . '/Library') . '/locales';
    $directory = $command === 'sync:package' ? $package : $root;

    toolkitFile($package . '/laravel_lang.json', '{}');
    toolkitFile($package . '/source/messages.json', '{"Hello":"Hello","0":"0","1":1}');
    toolkitFile($package . '/source/subdirectory/labels.php', '<?php return ["nested" => ["first" => "World"], "attributes" => []];');
    toolkitFile($package . '/source/_settings.json', '{"Obsolete":"Obsolete"}');
    toolkitFile($locales . '/fr/messages.json', '{"Hello":"Bonjour","World":"Monde","Obsolete":"Périmé","0":"Zéro","1":"Un"}');
    toolkitFile($locales . '/fr/numeric.json', '{"0":"Zéro","Obsolete":"Périmé"}');
    toolkitFile($locales . '/fr/_settings.json', '{"excludes":["Hello"]}');
    toolkitFile($locales . '/fr/_status.json', '{"messages":{"Hello":"Hello"}}');
    toolkitFile($locales . '/retired/messages.json', '{"Obsolete":"Périmé"}');

    $tester = runToolkit($command, ['path' => $directory]);

    expect($tester->getStatusCode())->toBe(Command::SUCCESS, $tester->getErrorOutput())
        ->and($tester->getErrorOutput())->toBe('')
        ->and($this->toolkitFilesystem->json($locales . '/fr/messages.json'))->toBe([
            0 => 'Zéro', 1 => 'Un', 'Hello' => 'Bonjour', 'World' => 'Monde',
        ])
        ->and(json_decode($this->toolkitFilesystem->get($locales . '/fr/numeric.json'), flags: JSON_THROW_ON_ERROR))
        ->toEqual(['0' => 'Zéro'])
        ->and($this->toolkitFilesystem->get($locales . '/fr/_settings.json'))->toBe('{"excludes":["Hello"]}')
        ->and($this->toolkitFilesystem->get($locales . '/fr/_status.json'))->toBe('{"messages":{"Hello":"Hello"}}')
        ->and($this->toolkitFilesystem->get($locales . '/retired/messages.json'))->toBe('{"Obsolete":"Périmé"}');
})->with(['sync:package', 'sync:library']);

it('keeps translations for separate source files that reuse a key', function (string $command): void {
    $root        = $this->toolkitDirectory;
    $package     = $root . '/Package';
    $isPackage   = $command === 'sync:package';
    $locales     = ($isPackage ? $package : $root . '/Library') . '/locales';
    $translation = $locales . '/fr/default.json';

    toolkitFile($package . '/laravel_lang.json', '{}');
    toolkitFile($package . '/source/v1/messages.php', '<?php return ["greeting" => "Hello"];');
    toolkitFile($package . '/source/v2/messages.php', '<?php return ["greeting" => "Welcome"];');
    toolkitFile($translation, '{"Hello":"Bonjour","Welcome":"Bienvenue","Obsolete":"Périmé"}');

    $tester = runToolkit($command, ['path' => $isPackage ? $package : $root]);

    expect($tester->getStatusCode())->toBe(Command::SUCCESS, $tester->getErrorOutput())
        ->and($this->toolkitFilesystem->json($translation))->toBe([
            'Hello'   => 'Bonjour',
            'Welcome' => 'Bienvenue',
        ]);
})->with(['sync:package', 'sync:library']);

it('keeps synchronized PHP translations readable on repeated runs', function (): void {
    $root = $this->toolkitDirectory;
    $path = $root . '/locales/fr/messages.php';
    toolkitFile($root . '/source/messages.php', '<?php return ["greeting" => "Hello"];');
    toolkitFile($path, '<?php return ["Hello" => "Bonjour", "Obsolete" => "Périmé"];');
    $obsolete = $root . '/locales/fr/obsolete.php';
    toolkitFile($obsolete, '<?php return ["Obsolete" => "Périmé"];');

    for ($run = 0; $run < 2; $run++) {
        expect(runToolkit('sync:package', ['path' => $root])->getStatusCode())->toBe(Command::SUCCESS)
            ->and($this->toolkitFilesystem->get($path))->toStartWith('<?php')
            ->and($this->toolkitFilesystem->exists($obsolete))->toBeFalse();
        expect(require $path)->toBe(['Hello' => 'Bonjour']);
    }
});

it('preserves translation files when synchronization sources are incomplete', function (string $command, ?string $source, string $extension = 'json'): void {
    $root        = $this->toolkitDirectory;
    $package     = $root . '/Package';
    $locales     = ($command === 'sync:package' ? $package : $root . '/Library') . '/locales';
    $directory   = $command === 'sync:package' ? $package : $root;
    $translation = '{"Hello":"Bonjour","World":"Monde"}';

    toolkitFile($package . '/laravel_lang.json', '{}');
    toolkitFile($locales . '/fr/messages.json', $translation);
    $this->toolkitFilesystem->ensureDirectoryExists($package . '/source');

    if ($source !== null) {
        toolkitFile($package . '/source/first.json', '{"Hello":"Hello"}');
        toolkitFile($package . '/source/messages.' . $extension, $source);
    }

    expect(runToolkit($command, ['path' => $directory])->getStatusCode())->toBe(Command::FAILURE)
        ->and($this->toolkitFilesystem->get($locales . '/fr/messages.json'))->toBe($translation);
})->with(['sync:package', 'sync:library'])->with([
    'no source files'               => [null],
    'empty source file'             => ['{}'],
    'invalid JSON'                  => ['{broken'],
    'non-object JSON'               => ['"World"'],
    'invalid PHP'                   => ['<?php return [', 'php'],
    'non-array PHP'                 => ['<?php return "World";', 'php'],
    'invalid PHP translation value' => ['<?php return ["label" => false];', 'php'],
]);

it('reports missing synchronization inputs', function (string $command, string $suffix, string $error): void {
    $tester = runToolkit($command, ['path' => $this->toolkitDirectory . $suffix]);

    expect($tester->getStatusCode())->toBe(Command::FAILURE)
        ->and($tester->getErrorOutput())->toContain($error);
})->with([
    'missing package directory' => ['sync:package', '/missing', 'Directory does not exist.'],
    'missing package locales'   => ['sync:package', '', 'Locales directory does not exist.'],
    'missing library packages'  => ['sync:library', '', 'No translation packages found'],
]);

it('uses locale settings when reporting missing translations and removes an obsolete status file', function (): void {
    $root   = $this->toolkitDirectory;
    $locale = $root . '/locales/fr';
    toolkitFile($root . '/locales/_settings.json', '{"excludes":["Global"]}');
    toolkitFile($locale . '/_settings.json', '{"excludes":["Local"]}');
    toolkitFile($locale . '/messages.json', '{"Zoo":"Zoo","ant":"ant","Ant":"Ant","Local":"Local","Global":"Global","Hello":"Bonjour"}');
    toolkitFile($locale . '/other.json', '{"World":"Monde"}');
    toolkitFile($locale . '/_status.json', '{"outdated":{}}');

    $tester = runToolkit('status', ['directory' => $root]);

    expect($tester->getStatusCode())->toBe(Command::SUCCESS, $tester->getErrorOutput())
        ->and($this->toolkitFilesystem->json($locale . '/_status.json'))->toBe([
            'messages' => ['ant' => 'ant', 'Ant' => 'Ant', 'Zoo' => 'Zoo'],
        ]);

    toolkitFile($locale . '/messages.json', '{"Zoo":"Parc","ant":"Fourmi","Local":"Local","Global":"Global"}');

    expect(runToolkit('status', ['directory' => $root])->getStatusCode())->toBe(Command::SUCCESS)
        ->and($this->toolkitFilesystem->exists($locale . '/_status.json'))->toBeFalse()
        ->and($this->toolkitFilesystem->get($locale . '/_settings.json'))->toBe('{"excludes":["Local"]}');
});

it('writes and removes per-locale reports in the chosen status directory', function (): void {
    $root    = $this->toolkitDirectory;
    $locales = $root . '/package/locales';
    $reports = $root . '/reports/status';
    $default = '{"messages":{"Previous":"Previous"}}';

    toolkitFile($locales . '/fr/messages.json', '{"Hello":"Hello"}');
    toolkitFile($locales . '/pt_BR/messages.json', '{"Goodbye":"Goodbye"}');
    toolkitFile($locales . '/fr/_status.json', $default);

    $process = new Process([
        PHP_BINARY,
        dirname(__DIR__, 3) . '/src/Toolkit/bin/toolkit',
        'status',
        'package',
        '--output-dir=reports/status',
    ], $root);

    expect($process->run())->toBe(Command::SUCCESS, $process->getErrorOutput())
        ->and($this->toolkitFilesystem->json($reports . '/fr.json'))->toBe(['messages' => ['Hello' => 'Hello']])
        ->and($this->toolkitFilesystem->json($reports . '/pt_BR.json'))->toBe(['messages' => ['Goodbye' => 'Goodbye']])
        ->and($this->toolkitFilesystem->exists($locales . '/pt_BR/_status.json'))->toBeFalse();

    toolkitFile($locales . '/fr/messages.json', '{"Hello":"Bonjour"}');

    expect($process->run())->toBe(Command::SUCCESS, $process->getErrorOutput())
        ->and($this->toolkitFilesystem->exists($reports . '/fr.json'))->toBeFalse()
        ->and($this->toolkitFilesystem->exists($reports . '/pt_BR.json'))->toBeTrue()
        ->and($this->toolkitFilesystem->get($locales . '/fr/_status.json'))->toBe($default);
});

it('rejects an explicitly empty status output directory', function (): void {
    $root = $this->toolkitDirectory;
    toolkitFile($root . '/locales/fr/messages.json', '{"Hello":"Hello"}');

    $tester = runToolkit('status', ['directory' => $root, '--output-dir' => '']);

    expect($tester->getStatusCode())->toBe(Command::INVALID)
        ->and($tester->getDisplay())->toContain('The output directory cannot be empty.')
        ->and($this->toolkitFilesystem->exists($root . '/locales/fr/_status.json'))->toBeFalse();
});

it('reports the destination when a status report cannot be written in full', function (false|int $bytesWritten): void {
    $root        = $this->toolkitDirectory;
    $destination = $root . '/reports';
    $report      = $destination . '/fr.json';
    toolkitFile($root . '/locales/fr/messages.json', '{"Hello":"Hello"}');

    $filesystem = Mockery::mock(Filesystem::class)->makePartial();
    $filesystem->shouldReceive('put')->once()->with($report, Mockery::type('string'))->andReturn($bytesWritten);

    $application = new Application;
    $application->addCommand(new StatusCommand($filesystem));
    $tester = new CommandTester($application->find('status'));

    expect(fn (): int => $tester->execute(['directory' => $root, '--output-dir' => $destination]))
        ->toThrow(TranslationFileException::class, "Unable to write translation file [$report]: The translation file could not be written.");
})->with([
    'failed write' => [false],
    'short write'  => [1],
]);

it('reports an error when the locales directory is missing', function (): void {
    $tester = runToolkit('status', ['directory' => $this->toolkitDirectory]);

    expect($tester->getStatusCode())->toBe(Command::FAILURE)
        ->and($tester->getDisplay())->toContain('The "locales" directory doesn\'t exists!');
});

it('reports invalid translation status input', function (string $path, string $content, string $diagnostic): void {
    $root = $this->toolkitDirectory;
    toolkitFile($root . '/locales/' . $path, $content);

    expect(fn (): ApplicationTester => runToolkit('status', ['directory' => $root]))
        ->toThrow(function (InvalidTranslationStatusException $exception) use ($path, $diagnostic): void {
            expect($exception->getMessage())->toContain(basename($path), $diagnostic);
        });
})->with([
    'invalid excludes'       => ['_settings.json', '{"excludes":"Hello"}', 'must be an array.'],
    'non-scalar translation' => ['fr/messages.json', '{"Hello":["Bonjour"]}', 'must be scalar.'],
]);

it('reports an encoding failure when a missing translation overflows to infinity', function (): void {
    $root = $this->toolkitDirectory;
    toolkitFile($root . '/locales/fr/messages.json', '{"INF":1e999}');

    expect(fn (): int => (new StatusCommand)(new BufferedOutput, $root))
        ->toThrow(function (InvalidJsonEncodeException $exception): void {
            expect($exception->getCode())->toBe(JSON_ERROR_INF_OR_NAN)
                ->and($exception->getPrevious())->toBeInstanceOf(JsonException::class);
        })
        ->and($this->toolkitFilesystem->exists($root . '/locales/fr/_status.json'))->toBeFalse();
});

it('omits the status file when a locale without settings is fully translated', function (): void {
    $root   = $this->toolkitDirectory;
    $locale = $root . '/locales/fr';
    toolkitFile($locale . '/messages.json', '{"Hello":"Bonjour"}');

    expect(runToolkit('status', ['directory' => $root])->getStatusCode())->toBe(Command::SUCCESS)
        ->and($this->toolkitFilesystem->exists($locale . '/_status.json'))->toBeFalse();
});

it('creates and removes only the intended locale directories on repeated runs', function (): void {
    $root     = $this->toolkitDirectory . '/target';
    $locales  = $root . '/locales';
    $neighbor = $this->toolkitDirectory . '/neighbor/locales';
    toolkitFile($root . '/source/messages.json', '{"Hello":"Hello"}');
    toolkitFile($locales . '/_settings.json', '{"excludes":[]}');
    toolkitFile($locales . '/fr/messages.json', '{"Hello":"Bonjour"}');
    toolkitFile($locales . '/retired/messages.json', '{"Hello":"Old"}');
    toolkitFile($neighbor . '/retired/messages.json', '{"Hello":"Neighbor"}');

    for ($run = 0; $run < 2; $run++) {
        expect(runToolkit('create-locale', ['directory' => $root])->getStatusCode())->toBe(Command::SUCCESS)
            ->and(array_map(basename(...), $this->toolkitFilesystem->directories($locales)))
            ->toEqualCanonicalizing([...Locale::values(), 'retired']);
    }

    for ($run = 0; $run < 2; $run++) {
        expect(runToolkit('delete-locale', ['directory' => $root])->getStatusCode())->toBe(Command::SUCCESS)
            ->and(array_map(basename(...), $this->toolkitFilesystem->directories($locales)))
            ->toEqualCanonicalizing(Locale::values());
    }

    expect($this->toolkitFilesystem->get($root . '/source/messages.json'))->toBe('{"Hello":"Hello"}')
        ->and($this->toolkitFilesystem->get($locales . '/_settings.json'))->toBe('{"excludes":[]}')
        ->and($this->toolkitFilesystem->get($locales . '/fr/messages.json'))->toBe('{"Hello":"Bonjour"}')
        ->and($this->toolkitFilesystem->get($neighbor . '/retired/messages.json'))->toBe('{"Hello":"Neighbor"}');
});

it('reports an error when deleting locales from a missing directory', function (): void {
    $tester = runToolkit('delete-locale', ['directory' => $this->toolkitDirectory]);

    expect($tester->getStatusCode())->toBe(Command::FAILURE)
        ->and($tester->getErrorOutput())->toContain('The "locales" directory does not exist.');
});
