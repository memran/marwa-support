<?php

declare(strict_types=1);

namespace Marwa\Support\Validation\Rules;

use Marwa\Support\Validation\AbstractRule;
use Marwa\Support\Validation\Helpers\ComparisonValidators;
use Marwa\Support\Validation\Helpers\ValueAccessor;

class SameRule extends AbstractRule
{
    private ComparisonValidators $validators;

    public function __construct(string|array $params = '')
    {
        parent::__construct($params);
        $this->validators = new ComparisonValidators(new ValueAccessor());
    }

    public function name(): string
    {
        return 'same';
    }

    public function validate(mixed $value, array $context): bool
    {
        $otherField = $this->getParamString('0', '');
        if ($otherField === '') {
            return false;
        }

        $input = $context['input'] ?? [];
        return $this->validators->sameAs('', $value, $input, $otherField);
    }

    public function message(string $field, array $attributes): string
    {
        $other = $this->getParam('0', '');
        return $this->formatMessage("The :attribute must match {$other}.", $field, $attributes);
    }
}
