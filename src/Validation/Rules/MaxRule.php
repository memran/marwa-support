<?php

declare(strict_types=1);

namespace Marwa\Support\Validation\Rules;

use Marwa\Support\Validation\AbstractRule;
use Marwa\Support\Validation\Helpers\ComparisonValidators;

class MaxRule extends AbstractRule
{
    private ComparisonValidators $validators;

    public function __construct(string|array $params = '')
    {
        parent::__construct($params);
        $this->validators = new ComparisonValidators(new \Marwa\Support\Validation\Helpers\ValueAccessor());
    }

    public function name(): string
    {
        return 'max';
    }

    public function validate(mixed $value, array $context): bool
    {
        return $this->validators->compareMax($value, $this->getParamString('0', ''));
    }

    public function message(string $field, array $attributes): string
    {
        $max = $this->getParam('0', '');
        return $this->formatMessage("The :attribute must not be greater than {$max}.", $field, $attributes);
    }
}
