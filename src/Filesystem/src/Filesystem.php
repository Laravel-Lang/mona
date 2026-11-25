<?php

declare(strict_types=1);

namespace LaravelLang\Filesystem;

use Illuminate\Filesystem\Filesystem as BaseFilesystem;
use LaravelLang\Exceptions\PathSearchException;
use LaravelLang\Filesystem\Processors\JsonProcessor;
use LaravelLang\Filesystem\Processors\PhpProcessor;
use Symfony\Component\Finder\SplFileInfo;

use function array_values;
use function base_path;
use function dirname;
use function mb_strtolower;
use function pathinfo;

readonly class Filesystem
{
    public function __construct(
        protected BaseFilesystem $filesystem,
        protected JsonProcessor $jsonProcessor,
        protected PhpProcessor $phpProcessor,
    ) {}

    public function basePath(string $path = ''): string
    {
        return base_path($path);
    }

    public function vendorPath(string $path = ''): string
    {
        return $this->basePath('vendor/' . $path);
    }

    public function libraryPath(): string
    {
        return $this->vendorPath('laravel-lang/library');
    }

    /** @return array<string> */
    public function glob(string $pattern, int $flags = 0): array
    {
        $paths = $this->filesystem->glob($pattern, $flags);

        if ($paths === false) {
            throw PathSearchException::matching($pattern);
        }

        return $paths;
    }

    /** @return list<SplFileInfo> */
    public function files(string $path): array
    {
        return array_values($this->filesystem->allFiles($path));
    }

    /**
     * @param  array<array-key, mixed>  $default
     * @return array<array-key, mixed>
     */
    public function read(string $path, ?string $key = null, array $default = []): array
    {
        return $this->isJson($path)
            ? $this->fromJson($path, $key, $default)
            : $this->fromPhp($path, $key, $default);
    }

    /**
     * @param  array<array-key, mixed>  $default
     * @return array<array-key, mixed>
     */
    public function fromJson(string $path, ?string $key = null, array $default = []): array
    {
        return $this->jsonProcessor->read($path, $key, $default);
    }

    public function fromString(string $path): string
    {
        return $this->filesystem->get($path);
    }

    /** @param  array<array-key, mixed>  $items */
    public function storeArray(string $path, array $items, bool $merge = false): void
    {
        if ($items === []) {
            return;
        }

        $this->filesystem->ensureDirectoryExists(
            dirname($path)
        );

        $this->isJson($path)
            ? $this->storeJson($path, $items, $merge)
            : $this->storePhp($path, $items, $merge);
    }

    /**
     * @param  array<array-key, mixed>  $items
     * @param  list<string>  $trailingKeys
     */
    public function storePhpArrayWithTrailingKeys(
        string $path,
        array $items,
        array $trailingKeys,
        bool $merge = false,
    ): void {
        $this->store($path, $this->phpProcessor->renderWithTrailingKeys($path, $items, $trailingKeys, $merge));
    }

    public function store(string $path, string $content): void
    {
        if (! $content) {
            return;
        }

        $this->filesystem->ensureDirectoryExists(
            dirname($path)
        );

        $this->filesystem->put($path, $content);
    }

    public function isJson(string $filename): bool
    {
        return mb_strtolower(pathinfo($filename, PATHINFO_EXTENSION)) === 'json';
    }

    /**
     * @param  array<array-key, mixed>  $default
     * @return array<array-key, mixed>
     */
    public function fromPhp(string $path, ?string $key = null, array $default = []): array
    {
        return $this->phpProcessor->read($path, $key, $default);
    }

    public function ensureDelete(string $path): void
    {
        $this->filesystem->isFile($path)
            ? $this->filesystem->delete($path)
            : $this->filesystem->deleteDirectory($path);
    }

    /** @param  array<array-key, mixed>  $content */
    protected function storeJson(string $path, array $content, bool $merge = false): void
    {
        $this->jsonProcessor->store($path, $content, $merge);
    }

    /** @param  array<array-key, mixed>  $content */
    protected function storePhp(string $path, array $content, bool $merge = false): void
    {
        $this->phpProcessor->store($path, $content, $merge);
    }
}
