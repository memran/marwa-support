<?php
declare(strict_types=1);

namespace Marwa\Support;

use RuntimeException;

class Hash
{
    public static function make(string $password, array $options = []): string
    {
        $options = array_merge([
            'cost' => 12,
            'algorithm' => PASSWORD_DEFAULT
        ], $options);

        $hash = password_hash($password, $options['algorithm'], $options);

        if ($hash === false) {
            throw new RuntimeException('Password hashing failed');
        }

        return $hash;
    }

    public static function verify(string $password, string $hash): bool
    {
        return password_verify($password, $hash);
    }

    public static function needsRehash(string $hash, array $options = []): bool
    {
        $options = array_merge(['cost' => 12], $options);
        return password_needs_rehash($hash, PASSWORD_DEFAULT, $options);
    }
}