<?php

declare(strict_types=1);

namespace Marwa\Support\Validation\Rules;

use Marwa\Support\Validation\AbstractRule;
use Marwa\Support\Validation\Helpers\TypeValidators;

class DeclinedRule extends AbstractRule
{
    private TypeValidators $validators;

    public function __construct(string|array $params = '')
    {
        parent::__construct($params);
        $this->validators = new TypeValidators();
    }

    public function name(): string
    {
        return 'declined';
    }

    public function validate(mixed $value, array $context): bool
    {
        return $this->validators->isDeclined($value);
    }

    public function message(string $field, array $attributes): string
    {
        return $this->formatMessage('The :attribute must be declined.', $field, $attributes);
    }
}
