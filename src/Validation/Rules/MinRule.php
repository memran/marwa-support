<?php

declare(strict_types=1);

namespace Marwa\Support\Validation\Rules;

use Marwa\Support\Validation\AbstractRule;
use Marwa\Support\Validation\Helpers\ComparisonValidators;

class MinRule extends AbstractRule
{
    private ComparisonValidators $validators;

    public function __construct(string|array $params = '')
    {
        parent::__construct($params);
        $this->validators = new ComparisonValidators(new \Marwa\Support\Validation\Helpers\ValueAccessor());
    }

    public function name(): string
    {
        return 'min';
    }

    public function validate(mixed $value, array $context): bool
    {
        return $this->validators->compareMin($value, $this->getParamString('0', ''));
    }

    public function message(string $field, array $attributes): string
    {
        $min = $this->getParam('0', '');
        return $this->formatMessage("The :attribute must be at least {$min}.", $field, $attributes);
    }
}