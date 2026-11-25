<?php

declare(strict_types=1);

use Illuminate\Filesystem\Filesystem;
use LaravelLang\Toolkit\Commands\MigrateLocalesCommand;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\ApplicationTester;

beforeEach(function (): void {
    $this->migrationFilesystem = new Filesystem;
    $this->migrationDirectory  = sys_get_temp_dir() . '/mona-migrate-' . bin2hex(random_bytes(8));
});

afterEach(function (): void {
    $this->migrationFilesystem->deleteDirectory($this->migrationDirectory);
});

it('migrates legacy dictionaries to an explicit or default target', function (bool $explicitTarget): void {
    $filesystem = $this->migrationFilesystem;
    $project    = $this->migrationDirectory . '/Legacy project';
    $target     = $explicitTarget ? $this->migrationDirectory . '/New locales' : $project . '/locales';
    $legacy     = $project . '/locales/fr';
    $locale     = $target . '/fr';
    $jsonSource = $project . '/source/messages.json';
    $phpSource  = $project . '/source/framework/12/messages.php';

    $fixtures = [
        $jsonSource                   => '{"greeting":"Hello","invalid":"Invalid :ATTRIBUTE"}',
        $phpSource                    => '<?php return ["nested" => ["name" => "Name", "status" => 403, "unsupported" => true, "invalid" => "Missing :attribute", "required" => "Required :attribute"]];',
        $legacy . '/json.json'        => '{"Hello":"Bonjour","0":"0","Name":"Nom JSON"}',
        $legacy . '/php.json'         => '{"nested.name":"Nom","nested.status":"Interdit","nested.unsupported":"Invalide","nested.obsolete":"Ancien"}',
        $legacy . '/json-inline.json' => '{"Invalid :ATTRIBUTE":"Attribut invalide"}',
        $legacy . '/php-inline.json'  => '{"nested.invalid":"Attribut manquant","nested.required":"Attribut requis"}',
        $legacy . '/_excludes.json'   => '["Name","Hello"]',
        $locale . '/default.json'     => '{"Hello":"Ancien","Another greeting":"Bonjour"}',
        $locale . '/inline.json'      => '{"Invalid :ATTRIBUTE":"Ancien","Another :attribute":"Autre attribut"}',
        $locale . '/_settings.json'   => '{"excludes":["Already excluded"]}',
    ];

    writeMigrationFixtures($filesystem, $fixtures);

    $tester = runLocaleMigration($project, $explicitTarget ? $target : null);

    expect($tester->getStatusCode())->toBe(Command::SUCCESS, $tester->getErrorOutput())
        ->and($filesystem->json($locale . '/default.json'))->toBe([
            0                  => '0',
            403                => 'Interdit',
            'Another greeting' => 'Bonjour',
            'Hello'            => 'Bonjour',
            'Name'             => 'Nom',
        ])
        ->and($filesystem->json($locale . '/inline.json'))->toBe([
            'Another :attribute'  => 'Autre attribut',
            'Invalid :ATTRIBUTE'  => 'Attribut invalide',
            'Missing :attribute'  => 'Attribut manquant',
            'Required :attribute' => 'Attribut requis',
        ])
        ->and($filesystem->json($locale . '/_settings.json'))->toBe([
            'excludes' => ['Already excluded', 'Name', 'Hello'],
        ])
        ->and($filesystem->get($jsonSource))->toBe($fixtures[$jsonSource])
        ->and($filesystem->get($phpSource))->toBe($fixtures[$phpSource]);

    foreach (['json.json', 'php.json', 'json-inline.json', 'php-inline.json', '_excludes.json'] as $filename) {
        expect($filesystem->exists($legacy . '/' . $filename))->toBeFalse("Legacy file $filename still exists.");
    }
})->with(['explicit target' => [true], 'default target' => [false]]);

it('keeps both dictionaries when either is invalid', function (string $legacyContents, string $targetContents): void {
    $filesystem = $this->migrationFilesystem;
    $project    = $this->migrationDirectory . '/project';
    $target     = $this->migrationDirectory . '/target';
    $legacy     = $project . '/locales/fr/json.json';
    $dictionary = $target . '/fr/default.json';

    writeMigrationFixtures($filesystem, [
        $project . '/source/messages.json' => '{"Hello":"Hello"}',
        $legacy                            => $legacyContents,
        $dictionary                        => $targetContents,
    ]);

    $tester = runLocaleMigration($project, $target);

    expect($tester->getStatusCode())->not->toBe(Command::SUCCESS)
        ->and($filesystem->get($legacy))->toBe($legacyContents)
        ->and($filesystem->get($dictionary))->toBe($targetContents);
})->with([
    'malformed legacy dictionary' => ['{broken', '{"Hello":"Salut"}'],
    'scalar target dictionary'    => ['{"Hello":"Bonjour"}', '42'],
]);

it('reports a missing source directory', function (): void {
    $project = $this->migrationDirectory . '/project';
    $target  = $this->migrationDirectory . '/target';
    $this->migrationFilesystem->ensureDirectoryExists($project . '/locales');

    $tester = runLocaleMigration($project, $target);

    expect($tester->getStatusCode())->toBe(Command::FAILURE)
        ->and(preg_replace('/\s+/', ' ', $tester->getErrorOutput()))->toContain('directory does not exist')
        ->and($this->migrationFilesystem->exists($target))->toBeFalse();
});

it('does not create a target when there are no legacy dictionaries', function (): void {
    $project = $this->migrationDirectory . '/project';
    $target  = $this->migrationDirectory . '/target';
    $this->migrationFilesystem->ensureDirectoryExists($project . '/source');

    $tester = runLocaleMigration($project, $target);

    expect($tester->getStatusCode())->toBe(Command::SUCCESS, $tester->getErrorOutput())
        ->and($this->migrationFilesystem->exists($target))->toBeFalse();
});

function writeMigrationFixtures(Filesystem $filesystem, array $fixtures): void
{
    foreach ($fixtures as $path => $contents) {
        $filesystem->ensureDirectoryExists(dirname($path));
        $filesystem->put($path, $contents);
    }
}

function runLocaleMigration(string $project, ?string $target): ApplicationTester
{
    $application = new Application;
    $application->setAutoExit(false);
    $application->setCatchErrors(true);
    $application->addCommand(new MigrateLocalesCommand);

    $arguments = ['command' => 'migrate-locales', 'project' => $project, '--no-interaction' => true];

    if ($target !== null) {
        $arguments['--target'] = $target;
    }

    $tester = new ApplicationTester($application);
    $tester->run($arguments, ['capture_stderr_separately' => true]);

    return $tester;
}
