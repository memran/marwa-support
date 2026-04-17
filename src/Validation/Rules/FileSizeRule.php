<?php

declare(strict_types=1);

namespace Marwa\Support\Validation\Rules;

use Marwa\Support\Validation\AbstractRule;
use Psr\Http\Message\UploadedFileInterface;

class FileSizeRule extends AbstractRule
{
    public function name(): string
    {
        return 'file_size';
    }

    public function validate(mixed $value, array $context): bool
    {
        if (!$value instanceof UploadedFileInterface) {
            return false;
        }

        $maxSize = $this->getParamBytes('size', 0);

        return $value->getSize() <= $maxSize;
    }

    private function getParamBytes(string $key, int $default): int
    {
        $value = $this->params[$key] ?? $default;

        if (is_int($value)) {
            return $value;
        }

        return $this->parseBytes((string) $value);
    }

    private function parseBytes(string $value): int
    {
        $value = trim($value);
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];

        foreach ($units as $i => $unit) {
            if (str_ends_with(strtoupper($value), $unit)) {
                $number = (float) substr($value, 0, -strlen($unit));
                return (int) ($number * pow(1024, $i));
            }
        }

        return (int) $value;
    }

    public function message(string $field, array $attributes): string
    {
        return $this->formatMessage(
            'The :attribute must not be larger than :size.',
            $field,
            $attributes
        );
    }
}
