<?php

declare(strict_types=1);

namespace Marwa\Support\Validation\Rules;

use Marwa\Support\Validation\AbstractRule;
use Marwa\Support\Validation\Helpers\ComparisonValidators;

class BetweenRule extends AbstractRule
{
    private ComparisonValidators $validators;

    public function __construct(string|array $params = '')
    {
        parent::__construct($params);
        $this->validators = new ComparisonValidators(new \Marwa\Support\Validation\Helpers\ValueAccessor());
    }

    public function name(): string
    {
        return 'between';
    }

    public function validate(mixed $value, array $context): bool
    {
        return $this->validators->compareBetween(
            $value,
            $this->getParamString('0', ''),
            $this->getParamString('1', '')
        );
    }

    public function message(string $field, array $attributes): string
    {
        $min = $this->getParam('0', '');
        $max = $this->getParam('1', '');
        return $this->formatMessage("The :attribute must be between {$min} and {$max}.", $field, $attributes);
    }
}
