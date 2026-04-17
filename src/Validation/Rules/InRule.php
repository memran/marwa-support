<?php

declare(strict_types=1);

namespace Marwa\Support\Validation\Rules;

use Marwa\Support\Validation\AbstractRule;

class InRule extends AbstractRule
{
    public function name(): string
    {
        return 'in';
    }

    public function validate(mixed $value, array $context): bool
    {
        $values = array_values($this->params);
        if (empty($values)) {
            return false;
        }

        return in_array($value, $values, true);
    }

    public function message(string $field, array $attributes): string
    {
        $values = implode(', ', array_values($this->params));
        return $this->formatMessage("The :attribute must be one of: {$values}.", $field, $attributes);
    }
}
