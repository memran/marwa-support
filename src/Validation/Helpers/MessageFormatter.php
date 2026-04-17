<?php

declare(strict_types=1);

namespace Marwa\Support\Validation\Helpers;

class MessageFormatter
{
    /**
     * @param string $field
     * @param string $rule
     * @param string $default
     * @param array<string, string> $messages
     * @param array<string, string> $attributes
     * @param array<string, mixed> $replacements
     */
    public function message(
        string $field,
        string $rule,
        string $default,
        array $messages,
        array $attributes,
        array $replacements = []
    ): string {
        $key = "{$field}.{$rule}";

        if (isset($messages[$key])) {
            $message = $messages[$key];
        } elseif (isset($messages[$rule])) {
            $message = $messages[$rule];
        } else {
            $message = $default;
        }

        $attribute = $attributes[$field] ?? $this->humanizeField($field);

        $replacements[':attribute'] = $attribute;
        $replacements[':field'] = $field;

        return str_replace(
            array_keys($replacements),
            array_map(fn($v) => is_bool($v) ? ($v ? 'true' : 'false') : (string) $v, $replacements),
            $message
        );
    }

    private function humanizeField(string $field): string
    {
        $label = str_replace(['.', '_'], ' ', $field);
        $label = preg_replace('/\s+/', ' ', $label) ?? $label;

        return ucfirst(trim($label));
    }
}