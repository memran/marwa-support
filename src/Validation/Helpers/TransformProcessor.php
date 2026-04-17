<?php

declare(strict_types=1);

namespace Marwa\Support\Validation\Helpers;

class TransformProcessor
{
    /**
     * @param mixed $value
     * @param array<int, mixed> $rules
     * @param bool $exists
     * @return mixed
     */
    public function applyTransforms(mixed $value, array $rules, bool $exists): mixed
    {
        if (!$exists) {
            return $value;
        }

        foreach ($rules as $rule) {
            $value = $this->applyRule($value, $rule);
        }

        return $value;
    }

    private function applyRule(mixed $value, mixed $rule): mixed
    {
        if (!is_string($rule)) {
            return $value;
        }

        return match ($rule) {
            'trim' => is_string($value) ? trim($value) : $value,
            'lowercase' => is_string($value) ? strtolower($value) : $value,
            'uppercase' => is_string($value) ? strtoupper($value) : $value,
            default => $value,
        };
    }
}