<?php

declare(strict_types=1);

namespace Marwa\Support\Validation\Rules;

use Marwa\Support\Validation\AbstractRule;

class StringRule extends AbstractRule
{
    public function name(): string
    {
        return 'string';
    }

    public function validate(mixed $value, array $context): bool
    {
        return is_string($value);
    }

    public function message(string $field, array $attributes): string
    {
        return $this->formatMessage('The :attribute must be a string.', $field, $attributes);
    }
}