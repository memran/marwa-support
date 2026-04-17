<?php

declare(strict_types=1);

namespace Marwa\Support\Validation\Rules;

use Marwa\Support\Validation\AbstractRule;

class EmailRule extends AbstractRule
{
    public function name(): string
    {
        return 'email';
    }

    public function validate(mixed $value, array $context): bool
    {
        if (!is_string($value)) {
            return false;
        }

        return filter_var($value, FILTER_VALIDATE_EMAIL) !== false;
    }

    public function message(string $field, array $attributes): string
    {
        return $this->formatMessage('The :attribute must be a valid email address.', $field, $attributes);
    }
}
