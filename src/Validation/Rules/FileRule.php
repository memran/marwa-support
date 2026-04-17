<?php

declare(strict_types=1);

namespace Marwa\Support\Validation\Rules;

use Marwa\Support\Validation\AbstractRule;
use Psr\Http\Message\UploadedFileInterface;

class FileRule extends AbstractRule
{
    public function name(): string
    {
        return 'file';
    }

    public function validate(mixed $value, array $context): bool
    {
        return $value instanceof UploadedFileInterface;
    }

    public function message(string $field, array $attributes): string
    {
        return $this->formatMessage('The :attribute must be a file upload.', $field, $attributes);
    }
}