<?php

declare(strict_types=1);

namespace Marwa\Support\Validation\Rules;

use Marwa\Support\Validation\AbstractRule;
use Marwa\Support\Validation\Helpers\ComparisonValidators;
use Marwa\Support\Validation\Helpers\ValueAccessor;

class ConfirmedRule extends AbstractRule
{
    private ComparisonValidators $validators;

    public function __construct(string|array $params = '')
    {
        parent::__construct($params);
        $this->validators = new ComparisonValidators(new ValueAccessor());
    }

    public function name(): string
    {
        return 'confirmed';
    }

    public function validate(mixed $value, array $context): bool
    {
        $field = $context['field'] ?? '';
        $input = $context['input'] ?? [];

        return $this->validators->isConfirmed($field, $value, $input);
    }

    public function message(string $field, array $attributes): string
    {
        return $this->formatMessage('The :attribute confirmation does not match.', $field, $attributes);
    }
}
