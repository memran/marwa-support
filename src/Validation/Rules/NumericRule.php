<?php

declare(strict_types=1);

namespace Marwa\Support\Validation\Rules;

use Marwa\Support\Validation\AbstractRule;

class NumericRule extends AbstractRule
{
    public function name(): string
    {
        return 'numeric';
    }

    public function validate(mixed $value, array $context): bool
    {
        return is_numeric($value);
    }

    public function message(string $field, array $attributes): string
    {
        return $this->formatMessage('The :attribute must be numeric.', $field, $attributes);
    }
}