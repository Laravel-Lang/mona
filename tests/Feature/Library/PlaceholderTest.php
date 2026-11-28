<?php

declare(strict_types=1);

use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;

it('uses only the placeholders of the source key in :dataset', function (string $path): void {
    $pattern = '/(?<![\w:]):([a-z_][\w]*)/i';
    $unknown = [];

    foreach ((new Filesystem)->json($path, JSON_THROW_ON_ERROR) as $key => $value) {
        preg_match_all($pattern, (string) $key, $matches);

        $allowed = array_map(strtolower(...), $matches[1]);

        preg_match_all($pattern, $value, $matches);

        foreach ($matches[1] as $placeholder) {
            // Laravel replaces a placeholder by prefix, so a suffix such as `:attributeda` is still valid.
            if (! Str::startsWith(strtolower($placeholder), $allowed)) {
                $unknown[] = $key . ' => ' . $value;
            }
        }
    }

    expect($unknown)->toBe([]);
})->with(function () {
    foreach (glob(dirname(__DIR__, 3) . '/src/*/locales/*/[!_]*.json') as $path) {
        yield basename(dirname($path)) . '/' . basename($path) => [$path];
    }
});
