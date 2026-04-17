<?php

declare(strict_types=1);

namespace Marwa\Support\Validation\Helpers;

class TypeCoercer
{
    /**
     * @param mixed $value
     * @param array<int, mixed> $rules
     * @return mixed
     */
    public function coerceValidatedValue(mixed $value, array $rules): mixed
    {
        foreach ($rules as $rule) {
            if (!is_string($rule)) {
                continue;
            }

            $value = match ($rule) {
                'integer' => (int) $value,
                'float', 'numeric' => (float) $value,
                'string' => (string) $value,
                'boolean' => $this->coerceBoolean($value),
                'array' => (array) $value,
                default => $value,
            };
        }

        return $value;
    }

    private function coerceBoolean(mixed $value): bool
    {
        if (is_bool($value)) {
            return $value;
        }

        if (is_string($value)) {
            $lower = strtolower($value);
            return in_array($lower, ['true', '1', 'yes', 'on'], true);
        }

        return (bool) $value;
    }
}
