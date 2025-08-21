<?php
declare(strict_types=1);

namespace Marwa\Support;

class Sanitizer
{
    public static function clean($input, string $type = 'string'): mixed
    {
        if (is_array($input)) {
            return array_map(fn($item) => self::clean($item, $type), $input);
        }

        return match($type) {
            'string' => filter_var(trim($input), FILTER_SANITIZE_SPECIAL_CHARS),
            'email' => filter_var($input, FILTER_SANITIZE_EMAIL),
            'url' => filter_var($input, FILTER_SANITIZE_URL),
            'int' => (int) filter_var($input, FILTER_SANITIZE_NUMBER_INT),
            'float' => (float) filter_var($input, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION),
            'encoded' => filter_var($input, FILTER_SANITIZE_ENCODED),
            'string_clean' => preg_replace('/[^\p{L}\p{N}\s]/u', '', $input),
            default => $input
        };
    }

    public static function filename(string $filename): string
    {
        $extension = pathinfo($filename, PATHINFO_EXTENSION);
        $name = pathinfo($filename, PATHINFO_FILENAME);
        
        $name = preg_replace('/[^a-zA-Z0-9_-]/', '_', $name);
        $name = substr($name, 0, 100);
        
        return $name . '.' . $extension;
    }
}