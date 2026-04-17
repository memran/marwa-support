<?php

declare(strict_types=1);

namespace Marwa\Support\Validation\Helpers;

class ComparisonValidators
{
    private ValueAccessor $accessor;

    public function __construct(ValueAccessor $accessor)
    {
        $this->accessor = $accessor;
    }

    /**
     * @param array<string, mixed> $input
     */
    public function isConfirmed(string $field, mixed $value, array $input): bool
    {
        $confirmedField = $field . '_confirmation';
        return $this->sameAs($field, $value, $input, $confirmedField);
    }

    /**
     * @param array<string, mixed> $input
     */
    public function sameAs(string $field, mixed $value, array $input, string $otherField): bool
    {
        $otherValue = $this->accessor->getValue($input, $otherField, $this->accessor->hasValue($input, $otherField));
        return $value === $otherValue;
    }

    public function compareMin(mixed $value, ?string $min): bool
    {
        if ($min === null) {
            return true;
        }

        if (is_string($value)) {
            return mb_strlen($value) >= (int) $min;
        }

        if (is_array($value)) {
            return count($value) >= (int) $min;
        }

        if (is_numeric($value)) {
            return (float) $value >= (float) $min;
        }

        return false;
    }

    public function compareMax(mixed $value, ?string $max): bool
    {
        if ($max === null) {
            return true;
        }

        if (is_string($value)) {
            return mb_strlen($value) <= (int) $max;
        }

        if (is_array($value)) {
            return count($value) <= (int) $max;
        }

        if (is_numeric($value)) {
            return (float) $value <= (float) $max;
        }

        return false;
    }

    public function compareBetween(mixed $value, ?string $min, ?string $max): bool
    {
        if ($min === null || $max === null) {
            return true;
        }

        return $this->compareMin($value, $min) && $this->compareMax($value, $max);
    }
}
