<?php

declare(strict_types=1);

namespace Marwa\Support;

class Sanitizer
{
    public static function clean($input, string $type = 'string'): mixed
    {
        if ($input === null) {
            return null;
        }

        if (is_array($input)) {
            return array_map(fn ($item) => self::clean($item, $type), $input);
        }

        $value = is_scalar($input) ? (string) $input : $input;

        return match($type) {
            'string' => htmlspecialchars(trim((string) $value), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'),
            'email' => filter_var($value, FILTER_SANITIZE_EMAIL),
            'url' => filter_var($value, FILTER_SANITIZE_URL),
            'int' => (int) filter_var($value, FILTER_SANITIZE_NUMBER_INT),
            'float' => (float) filter_var($value, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION),
            'encoded' => filter_var($value, FILTER_SANITIZE_ENCODED),
            'string_clean' => preg_replace('/[^\p{L}\p{N}\s]/u', '', (string) $value),
            default => throw new \InvalidArgumentException("Unknown sanitization type: {$type}")
        };
    }

    public static function filename(string $filename): string
    {
        $extension = pathinfo($filename, PATHINFO_EXTENSION);
        $name = pathinfo($filename, PATHINFO_FILENAME);

        $name = preg_replace('/[^a-zA-Z0-9_-]+/', '_', $name) ?? 'file';
        $name = trim($name, '._-');
        $name = $name === '' ? 'file' : substr($name, 0, 100);

        $extension = preg_replace('/[^a-zA-Z0-9]+/', '', $extension) ?? '';

        return $extension === '' ? $name : $name . '.' . $extension;
    }
}
