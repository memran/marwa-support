<?php

declare(strict_types=1);

namespace Marwa\Support\Validation\Rules;

use Marwa\Support\Validation\AbstractRule;

class IpRule extends AbstractRule
{
    public function name(): string
    {
        return 'ip';
    }

    public function validate(mixed $value, array $context): bool
    {
        return filter_var($value, FILTER_VALIDATE_IP) !== false;
    }

    public function message(string $field, array $attributes): string
    {
        return $this->formatMessage('The :attribute must be a valid IP address.', $field, $attributes);
    }
}