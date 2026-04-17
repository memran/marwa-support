<?php

declare(strict_types=1);

namespace Marwa\Support\Validation\Rules;

use Marwa\Support\Validation\AbstractRule;

class ArrayRule extends AbstractRule
{
    public function name(): string
    {
        return 'array';
    }

    public function validate(mixed $value, array $context): bool
    {
        return is_array($value);
    }

    public function message(string $field, array $attributes): string
    {
        return $this->formatMessage('The :attribute must be an array.', $field, $attributes);
    }
}
