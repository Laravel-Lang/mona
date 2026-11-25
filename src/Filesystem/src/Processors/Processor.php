<?php

declare(strict_types=1);

namespace LaravelLang\Filesystem\Processors;

use Illuminate\Filesystem\Filesystem;
use LaravelLang\Exceptions\InvalidTranslationFileContentException;

use function is_array;
use function trim;

abstract class Processor
{
    /** @return array<array-key, mixed> */
    abstract protected function getContent(string $path): array;

    /** @param  array<array-key, mixed>  $content */
    abstract protected function putContent(string $path, array $content, bool $merge = false): string;

    public function __construct(
        protected Filesystem $filesystem,
    ) {}

    /**
     * @param  array<array-key, mixed>  $default
     *
     * @return array<array-key, mixed>
     */
    public function read(string $path, ?string $key = null, array $default = []): array
    {
        if (! $this->filesystem->exists($path)) {
            return [];
        }

        $content = $this->getContent($path);

        if (! $key) {
            return $content;
        }

        $value = $content[$key] ?? $default;

        if (! is_array($value)) {
            throw InvalidTranslationFileContentException::expectedArrayForKey($path, $key);
        }

        return $value;
    }

    /** @param  array<array-key, mixed>  $content */
    public function store(string $path, array $content, bool $merge = false): void
    {
        $prepared = $this->putContent($path, $content, $merge);

        $this->filesystem->put($path, trim($prepared) . PHP_EOL);
    }
}
