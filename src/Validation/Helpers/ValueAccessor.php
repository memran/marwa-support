<?php

declare(strict_types=1);

namespace Marwa\Support\Validation\Helpers;

class ValueAccessor
{
    /**
     * @param array<string, mixed> $data
     */
    public function hasValue(array $data, string $field): bool
    {
        $segments = explode('.', $field);
        $current = $data;

        foreach ($segments as $segment) {
            if (is_array($current) && array_key_exists($segment, $current)) {
                $current = $current[$segment];
            } else {
                return false;
            }
        }

        return true;
    }

    /**
     * @param array<string, mixed> $data
     */
    public function getValue(array $data, string $field, bool $exists): mixed
    {
        if (!$exists) {
            return null;
        }

        $segments = explode('.', $field);
        $current = $data;

        foreach ($segments as $segment) {
            if (is_array($current) && array_key_exists($segment, $current)) {
                $current = $current[$segment];
            } else {
                return null;
            }
        }

        return $current;
    }

    /**
     * @param array<string, mixed> $data
     */
    public function setValue(array &$data, string $field, mixed $value): void
    {
        $segments = explode('.', $field);
        $current = &$data;

        foreach ($segments as $segment) {
            if (!isset($current[$segment]) || !is_array($current[$segment])) {
                $current[$segment] = [];
            }
            $current = &$current[$segment];
        }

        $current = $value;
    }

    /**
     * @param array<string|int, mixed> $rules
     */
    public function fieldHasDefault(array $rules): bool
    {
        foreach ($rules as $rule) {
            if (is_string($rule) && str_starts_with($rule, 'default:')) {
                return true;
            }
        }
        return false;
    }
}