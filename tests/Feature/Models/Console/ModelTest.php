<?php

declare(strict_types=1);

use App\Models\SomeTranslation;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;
use LaravelLang\Config\Services\ModelLangConfig;
use LaravelLang\Exceptions\InvalidModelCommandInputException;
use LaravelLang\Exceptions\UnknownModelPathException;
use LaravelLang\Models\Console\ModelMakeCommand as PackageMakeModel;
use LaravelLang\Models\HasTranslations;
use Workbench\App\Models\TestModel;
use Workbench\App\Models\User;

use function Pest\Laravel\artisan;

$cleanUp = function (): void {
    $filesystem = new Filesystem;

    $filesystem->delete([
        app_path('Models/Some.php'),
        app_path('Models/SomeTranslation.php'),
        ...$filesystem->glob(database_path('migrations/*_create_some_translations_table.php')),
    ]);
};

beforeEach($cleanUp);
afterEach($cleanUp);

test('from scratch', function (): void {
    $source     = app_path('Models/Some.php');
    $filesystem = new Filesystem;
    $filesystem->ensureDirectoryExists(dirname($source));
    $filesystem->put($source, <<<'PHP'
        <?php

        namespace App\Models;

        use Illuminate\Database\Eloquent\Model;

        class Sibling extends Model {} class Some extends Model {}
        PHP);
    require_once $source;

    artisan(PackageMakeModel::class, [
        'model'     => '\App\Models\Some',
        '--columns' => ['title', 'description'],
    ])->assertSuccessful()->run();

    $model = new SomeTranslation([
        'locale'      => 'en',
        'title'       => ' Title ',
        'description' => ' Description ',
    ]);

    expect($model->locale)->toBe('en')
        ->and($model->title)->toBe('Title')
        ->and($model->description)->toBe('Description');

    $migrations = (new Filesystem)->glob(database_path('migrations/*_create_some_translations_table.php'));
    $helper
        = sprintf('%s/_ide_helper_models_%s.php', app(ModelLangConfig::class)->helpers, hash('xxh128', 'App\Models\Some'));

    expect($migrations)->toHaveCount(1);

    expect(file_get_contents($migrations[0]))->toMatchSnapshot();

    expect($helper)->toBeReadableFile()
        ->and(file_get_contents(app_path('Models/Some.php')))
        ->toContain('class Sibling extends Model {} class Some extends Model {', 'use \\' . HasTranslations::class . ';');
});

test('rejects invalid models before generating translations', function (string $model, string $message): void {
    artisan('make:model-translation', ['model' => $model])
        ->expectsOutputToContain($message)
        ->assertFailed()
        ->run();
})->with([
    'unknown model'              => ['App\\Models\\Missing', 'was not found.'],
    'non-model class'            => [stdClass::class, 'was not found.'],
    'abstract model'             => [Model::class, 'cannot be instantiated.'],
    'existing translation model' => [TestModel::class, 'already has a translation repository.'],
]);

test('requires a model class name in programmatic calls', function (): void {
    artisan('make:model-translation', ['model' => 123])
        ->expectsOutputToContain('A model class name is required.')
        ->assertFailed()
        ->run();
});

test('rejects invalid column options before generating translations', function (array|string $columns, string $message): void {
    expect(fn () => artisan('make:model-translation', ['model' => User::class, '--columns' => $columns])->run())
        ->toThrow(InvalidModelCommandInputException::class, $message);
})->with([
    'non-string column' => [[123], 'Column names must be strings.'],
    'non-array option'  => ['title', 'The columns option must be an array of column names.'],
]);

test('reports when the selected model is outside the configured directories', function (): void {
    config(['localization_models.directories' => []]);

    artisan('make:model-translation', ['model' => User::class, '--columns' => ['title']])->run();
})->throws(UnknownModelPathException::class, 'Could not find path to model file `\\' . User::class . '`.');

test('preserves a model source when it no longer matches the loaded declaration', function (string $model, bool $moved): void {
    $files    = new Filesystem;
    $path     = sys_get_temp_dir() . '/mona-model-source-' . Str::uuid();
    $source   = $path . '/original/' . $model . '.php';
    $target   = $moved ? $path . '/other/' . $model . '.php' : $source;
    $original = "<?php\nnamespace App\\Models;\nclass $model extends \\Illuminate\\Database\\Eloquent\\Model {}";
    $updated  = str_replace('class ', "\nclass ", $original);

    try {
        $files->ensureDirectoryExists(dirname($source));
        $files->put($source, $original);
        require $source;

        $files->ensureDirectoryExists(dirname($target));
        $files->put($target, $updated);
        config(['localization_models.directories' => [dirname($target)]]);

        expect(fn () => artisan('make:model-translation', [
            'model'     => 'App\\Models\\' . $model,
            '--columns' => ['title'],
        ])->run())->toThrow(UnknownModelPathException::class);

        expect($files->get($target))->toBe($updated)
            ->and($files->exists(dirname($target) . '/' . $model . 'Translation.php'))->toBeFalse();
    } finally {
        $files->deleteDirectory($path);
    }
})->with([
    'class moved within the source'            => ['MovedModelSource', false],
    'a different file declares the same class' => ['CopiedModelSource', true],
]);
