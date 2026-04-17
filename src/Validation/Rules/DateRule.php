<?php

declare(strict_types=1);

namespace Marwa\Support\Validation\Rules;

use Marwa\Support\Validation\AbstractRule;
use Marwa\Support\Validation\Helpers\DateValidators;

class DateRule extends AbstractRule
{
    private DateValidators $validators;

    public function __construct(string|array $params = '')
    {
        parent::__construct($params);
        $this->validators = new DateValidators();
    }

    public function name(): string
    {
        return 'date';
    }

    public function validate(mixed $value, array $context): bool
    {
        return $this->validators->isDate($value);
    }

    public function message(string $field, array $attributes): string
    {
        return $this->formatMessage('The :attribute must be a valid date.', $field, $attributes);
    }
}
