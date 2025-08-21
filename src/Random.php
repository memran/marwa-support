<?php
declare(strict_types=1);

namespace Marwa\Support;

use RuntimeException;
use Marwa\Support\Str;

class Random
{
    public static function bytes(int $length = 32): string
    {
        try {
            return random_bytes($length);
        } catch (\Exception $e) {
            throw new RuntimeException('Could not generate random bytes');
        }
    }

    public static function string(int $length = 16, string $charset = 'alnum'): string
    {
        return Str::random($length, $charset);
    }

    public static function uuid(): string
    {
        $data = self::bytes(16);
        $data[6] = chr(ord($data[6]) & 0x0f | 0x40);
        $data[8] = chr(ord($data[8]) & 0x3f | 0x80);
        
        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    }
}