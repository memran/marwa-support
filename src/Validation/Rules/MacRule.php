<?php

declare(strict_types=1);

namespace Marwa\Support\Validation\Rules;

use Marwa\Support\Validation\AbstractRule;

class MacRule extends AbstractRule
{
    public function name(): string
    {
        return 'mac';
    }

    public function validate(mixed $value, array $context): bool
    {
        return filter_var($value, FILTER_VALIDATE_MAC) !== false;
    }

    public function message(string $field, array $attributes): string
    {
        return $this->formatMessage('The :attribute must be a valid MAC address.', $field, $attributes);
    }
}
