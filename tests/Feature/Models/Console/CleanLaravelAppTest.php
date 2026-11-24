<?php

declare(strict_types=1);

use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Symfony\Component\Process\Process;

function withCleanModelApplication(Closure $scenario): void
{
    $project = dirname(__DIR__, 4);
    $path    = sys_get_temp_dir() . '/mona-models-' . Str::uuid();
    $files   = new Filesystem;

    try {
        $files->copyDirectory($project . '/tests/Fixtures/Models/LaravelApp', $path);
        $files->ensureDirectoryExists($path . '/app/Models');
        $files->ensureDirectoryExists($path . '/bootstrap/cache');
        $files->ensureDirectoryExists($path . '/database');
        $files->ensureDirectoryExists($path . '/vendor/composer');

        // Discover providers from the split package manifests using the installed test dependencies.
        $packages = array_map(
            static fn (string $package) => $files->json($project . '/src/' . $package . '/composer.json'),
            ['Config', 'Models']
        );
        $files->put($path . '/vendor/composer/installed.json', json_encode(['packages' => $packages], JSON_THROW_ON_ERROR));
        $files->put($path . '/vendor/autoload.php', <<<'PHP'
            <?php

            $loader = require getenv('MONA_TEST_AUTOLOAD');
            $loader->addPsr4('App\\', dirname(__DIR__) . '/app');
            PHP);

        $database = $path . '/database/database.sqlite';
        $files->put($database, '');

        // Each child must read the latest generated PHP files from disk.
        $run = static fn (string ...$arguments): string => (new Process([PHP_BINARY, '-d', 'opcache.enable_cli=0', ...$arguments], $path, [
            'APP_ENV'             => 'local',
            'APP_LOCALE'          => 'en',
            'APP_FALLBACK_LOCALE' => 'de',
            'COMPOSER_VENDOR_DIR' => false,
            'DB_CONNECTION'       => 'sqlite',
            'DB_DATABASE'         => $database,
            'DB_FOREIGN_KEYS'     => 'true',
            'DB_URL'              => false,
            'MONA_TEST_AUTOLOAD'  => $project . '/vendor/autoload.php',
            'VENDOR_PATH'         => false,
        ]))->mustRun()->getOutput();

        $scenario($path, $run);
    } finally {
        $files->deleteDirectory($path);
    }
}

test('a clean Laravel application discovers and publishes the localization configuration', function (): void {
    withCleanModelApplication(function (string $path, Closure $run): void {
        $run('artisan', 'package:discover', '--no-interaction');
        $run('artisan', 'vendor:publish', '--tag=localization', '--no-interaction');

        foreach (['main', 'map', 'models', 'punctuation', 'routes'] as $name) {
            expect($path . '/config/localization_' . $name . '.php')->toBeReadableFile();
        }

        $config = $path . '/config/localization_models.php';
        $custom = '<?php return [\'filter\' => false];';
        file_put_contents($config, $custom);

        $run('artisan', 'vendor:publish', '--tag=localization', '--no-interaction');

        expect(file_get_contents($config))->toBe($custom);
    });
});

test('a clean Laravel application can generate, use and roll back a translation model', function (): void {
    withCleanModelApplication(function (string $path, Closure $run): void {
        $run('artisan', 'make:model', 'Article', '--migration', '--no-interaction');
        $run('artisan', 'migrate', '--no-interaction');
        $run('artisan', 'make:model-translation', 'App\\Models\\Article', '--no-interaction');
        $run('artisan', 'migrate', '--no-interaction');

        $helper = $path . '/vendor/_laravel_lang/_ide_helper_models_' . hash('xxh128', 'App\\Models\\Article') . '.php';

        expect($helper)->toBeReadableFile();
        File::delete($helper);

        $run('artisan', 'lang:models-helper', 'App\\Models\\Article', '--no-interaction');

        expect($helper)->toBeReadableFile();
        expect(file_get_contents($helper))->toContain('$title', '$description');

        $output = $run('exercise.php');

        expect($output)->toBeJson($output)->json()->toBe([
            'title'       => 'First article',
            'description' => 'Article description',
            'germanTitle' => 'Erster Artikel',
            'locales'     => ['de', 'en'],
            'uniquePair'  => true,
        ]);

        $run('artisan', 'migrate:rollback', '--no-interaction');

        $database = new PDO('sqlite:' . $path . '/database/database.sqlite');

        expect($database->query('SELECT COUNT(*) FROM articles')->fetchColumn())->toBe(2);
        expect($database->query("SELECT name FROM sqlite_master WHERE name = 'article_translations'")->fetchColumn())
            ->toBeFalse();
    });
});

test('generation edits only the selected class and supports sorting by a custom primary key', function (): void {
    withCleanModelApplication(function (string $path, Closure $run): void {
        $source   = $path . '/app/Models/Article.php';
        $original = <<<'PHP'
            <?php

            namespace App\Models;

            use Illuminate\Database\Eloquent\Model;

            class Sibling extends Model
            {
            }

            #[\AllowDynamicProperties]
            final
            class Article
                extends Model
            {
                protected $primaryKey = 'article_id';
            }
            PHP;

        file_put_contents($source, $original);
        File::ensureDirectoryExists($path . '/database/migrations');
        file_put_contents($path . '/database/migrations/2020_01_01_000000_create_articles_table.php', <<<'PHP'
            <?php

            use Illuminate\Database\Migrations\Migration;
            use Illuminate\Database\Schema\Blueprint;
            use Illuminate\Support\Facades\Schema;

            return new class extends Migration {
                public function up(): void
                {
                    Schema::create('articles', function (Blueprint $table): void {
                        $table->id('article_id');
                        $table->timestamps();
                    });
                }
            };
            PHP);

        $run('artisan', 'make:model-translation', 'App\Models\Article', '--columns=title', '--no-interaction');
        $run('artisan', 'migrate', '--no-interaction');

        file_put_contents($path . '/sort.php', <<<'PHP'
            <?php

            require __DIR__ . '/vendor/autoload.php';
            $app = require __DIR__ . '/bootstrap/app.php';
            $app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

            App\Models\Article::create(['title' => 'Zulu']);
            App\Models\Article::create(['title' => 'Alpha']);

            echo json_encode([
                App\Models\Article::orderByTranslation('title')->pluck('article_id')->all(),
                class_uses(App\Models\Sibling::class),
            ], JSON_THROW_ON_ERROR);
            PHP);

        $newLine  = str_contains($original, "\r\n") ? "\r\n" : "\n";
        $expected = str_replace(
            '    protected $primaryKey',
            '    use \\LaravelLang\\Models\\HasTranslations;' . $newLine . '    protected $primaryKey',
            $original,
        );

        expect($run('sort.php'))->json()->toBe([[2, 1], []])
            ->and(file_get_contents($source))->toBe($expected);
    });
});

test('generating or refreshing a single helper retains helpers for other models', function (): void {
    withCleanModelApplication(function (string $path, Closure $run): void {
        foreach (['Article', 'Product'] as $model) {
            $run('artisan', 'make:model', $model, '--no-interaction');
            $run('artisan', 'make:model-translation', 'App\Models\\' . $model, '--columns=title', '--no-interaction');
        }

        $run('artisan', 'lang:models-helper', 'App\Models\Product', '--no-interaction');

        foreach (['Article', 'Product'] as $model) {
            $hash = hash('xxh128', 'App\Models\\' . $model);

            expect($path . '/vendor/_laravel_lang/_ide_helper_models_' . $hash . '.php')->toBeReadableFile();
        }
    });
});
