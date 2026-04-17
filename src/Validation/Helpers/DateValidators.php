<?php

declare(strict_types=1);

namespace Marwa\Support\Validation\Helpers;

class DateValidators
{
    public function isDate(mixed $value): bool
    {
        if ($value instanceof \DateTimeInterface) {
            return true;
        }

        if (!is_string($value) && !is_numeric($value)) {
            return false;
        }

        try {
            new \DateTime(is_numeric($value) ? '@' . $value : $value);
            return true;
        } catch (\Throwable) {
            return false;
        }
    }

    public function matchesDateFormat(mixed $value, string $format): bool
    {
        if (!is_string($value) || $format === '') {
            return false;
        }

        try {
            $date = \DateTime::createFromFormat('!' . $format, $value);
            return $date !== false && $date->format($format) === $value;
        } catch (\Throwable) {
            return false;
        }
    }

    public function matchesRegex(mixed $value, string $pattern): bool
    {
        if (!is_string($value) || $pattern === '') {
            return false;
        }

        return @preg_match($pattern, $value) === 1;
    }
}
