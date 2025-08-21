<?php
declare(strict_types=1);

namespace Marwa\Support;

class Validator
{
    public static function check($input, string $type): bool
    {
        return match($type) {
            'email' => filter_var($input, FILTER_VALIDATE_EMAIL) !== false,
            'url' => filter_var($input, FILTER_VALIDATE_URL) !== false,
            'ip' => filter_var($input, FILTER_VALIDATE_IP) !== false,
            'mac' => filter_var($input, FILTER_VALIDATE_MAC) !== false,
            'domain' => filter_var($input, FILTER_VALIDATE_DOMAIN) !== false,
            'int' => filter_var($input, FILTER_VALIDATE_INT) !== false,
            'float' => filter_var($input, FILTER_VALIDATE_FLOAT) !== false,
            'boolean' => filter_var($input, FILTER_VALIDATE_BOOLEAN) !== false,
            default => false
        };
    }

    public static function isMalicious(string $input): bool
    {
        $patterns = [
            '/<script/i', '/javascript:/i', '/vbscript:/i',
            '/onload=/i', '/onerror=/i', '/onclick=/i',
            '/eval\(/i', '/base64_decode/i',
            '/document\.cookie/i', '/window\.location/i',
        ];

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $input)) {
                return true;
            }
        }

        return false;
    }
}