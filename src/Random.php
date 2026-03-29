<?php

declare(strict_types=1);

namespace Marwa\Support;

use RuntimeException;

class Random
{
    public static function bytes(int $length = 32): string
    {
        if ($length < 1) {
            throw new RuntimeException('Random byte length must be greater than zero');
        }

        try {
            return random_bytes($length);
        } catch (\Exception $e) {
            throw new RuntimeException('Could not generate random bytes', 0, $e);
        }
    }

    public static function string(int $length = 16, string $charset = 'alnum'): string
    {
        if ($length < 1) {
            throw new RuntimeException('Random string length must be greater than zero');
        }

        $alphabet = match ($charset) {
            'alpha' => 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ',
            'hex' => '0123456789abcdef',
            'numeric' => '0123456789',
            'base64url' => 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789-_',
            'alnum' => 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789',
            default => throw new RuntimeException(sprintf('Unsupported random charset "%s"', $charset)),
        };

        $maxIndex = strlen($alphabet) - 1;
        $value = '';

        for ($i = 0; $i < $length; $i++) {
            $value .= $alphabet[random_int(0, $maxIndex)];
        }

        return $value;
    }

    public static function uuid(): string
    {
        $data = self::bytes(16);
        $data[6] = chr(ord($data[6]) & 0x0f | 0x40);
        $data[8] = chr(ord($data[8]) & 0x3f | 0x80);

        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    }
}
