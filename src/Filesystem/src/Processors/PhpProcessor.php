<?php

declare(strict_types=1);

namespace LaravelLang\Filesystem\Processors;

use Brick\VarExporter\VarExporter;
use Illuminate\Support\Arr;
use LaravelLang\Exceptions\InvalidTranslationFileContentException;

use function array_key_exists;
use function array_replace;
use function is_array;
use function ksort;
use function sprintf;
use function trim;

class PhpProcessor extends Processor
{
    protected string $template = <<<'PHP'
        <?php

        declare(strict_types=1);

        return %s;

        PHP;

    /**
     * @param  array<array-key, mixed>  $content
     * @param  list<string>  $trailingKeys
     */
    public function renderWithTrailingKeys(string $path, array $content, array $trailingKeys, bool $merge = false): string
    {
        $data     = $this->sortedContent($path, $content, $merge);
        $trailing = [];

        foreach ($trailingKeys as $key) {
            if (array_key_exists($key, $data)) {
                $trailing[$key] = $data[$key];

                unset($data[$key]);
            }
        }

        return trim($this->storeContent($data + $trailing)) . PHP_EOL;
    }

    protected function getContent(string $path): array
    {
        $content = require $path;

        if (! is_array($content)) {
            throw InvalidTranslationFileContentException::expectedArray($path);
        }

        return $content;
    }

    protected function putContent(string $path, array $content, bool $merge = false): string
    {
        return $this->storeContent($this->sortedContent($path, $content, $merge));
    }

    /** @param array<array-key, mixed> $data */
    protected function storeContent(array $data): string
    {
        return sprintf($this->template, $this->exportArray($data));
    }

    /**
     * @param  array<array-key, mixed>  $content
     * @return array<array-key, mixed>
     */
    protected function merge(string $path, array $content, bool $merge): array
    {
        if ($merge) {
            $previous = Arr::dot($this->read($path));
            $current  = Arr::dot($content);

            return array_replace($previous, $current);
        }

        return Arr::dot($content);
    }

    /** @param array<array-key, mixed> $data */
    protected function exportArray(array $data): string
    {
        return VarExporter::export($data, VarExporter::TRAILING_COMMA_IN_ARRAY);
    }

    /**
     * @param  array<array-key, mixed>  $content
     * @return array<array-key, mixed>
     */
    protected function sortedContent(string $path, array $content, bool $merge): array
    {
        $data = $this->merge($path, $content, $merge);

        ksort($data);

        return Arr::undot($data);
    }
}
