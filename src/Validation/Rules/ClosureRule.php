<?php

declare(strict_types=1);

namespace Marwa\Support\Validation\Rules;

use Marwa\Support\Validation\AbstractRule;

final class ClosureRule extends AbstractRule
{
    private \Closure $closure;

    public function __construct(\Closure $closure, string|array $params = '')
    {
        parent::__construct($params);
        $this->closure = $closure;
    }

    public function name(): string
    {
        return 'closure';
    }

    public function validate(mixed $value, array $context): bool
    {
        $field = $context['field'] ?? '';
        $input = $context['input'] ?? [];

        $result = ($this->closure)($value, $input, $field);

        if ($result === true || $result === null) {
            return true;
        }

        return false;
    }

    public function message(string $field, array $attributes): string
    {
        return $this->formatMessage('The :attribute field is invalid.', $field, $attributes);
    }
}
