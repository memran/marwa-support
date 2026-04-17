<?php

declare(strict_types=1);

namespace Marwa\Support\Validation\Rules;

use Marwa\Support\Validation\AbstractRule;
use Marwa\Support\Validation\Helpers\DateValidators;

class DateFormatRule extends AbstractRule
{
    private DateValidators $validators;

    public function __construct(string|array $params = '')
    {
        parent::__construct($params);
        $this->validators = new DateValidators();
    }

    public function name(): string
    {
        return 'date_format';
    }

    public function validate(mixed $value, array $context): bool
    {
        return $this->validators->matchesDateFormat($value, $this->getParamString('0', ''));
    }

    public function message(string $field, array $attributes): string
    {
        $format = $this->getParam('0', '');
        return $this->formatMessage("The :attribute must match the format {$format}.", $field, $attributes);
    }
}
