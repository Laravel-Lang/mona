<?php

declare(strict_types=1);

namespace LaravelLang\Filesystem\Processors;

use Illuminate\Support\Arr;
use LaravelLang\Exceptions\InvalidTranslationFileContentException;
use Symfony\Component\VarExporter\VarExporter;

use function array_replace;
use function is_array;
use function ksort;
use function sprintf;

class PhpProcessor extends Processor
{
    protected string $template = <<<'PHP'
        <?php

        declare(strict_types=1);

        return %s;

        PHP;

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
        $data = $this->merge($path, $content, $merge);

        ksort($data);

        return $this->storeContent($data);
    }

    /** @param array<array-key, mixed> $data */
    protected function storeContent(array $data): string
    {
        return sprintf($this->template, $this->export($data));
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
    protected function export(array $data): string
    {
        return VarExporter::export(
            Arr::undot($data)
        );
    }
}
