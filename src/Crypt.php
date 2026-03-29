<?php

declare(strict_types=1);

namespace Marwa\Support;

use RuntimeException;

class Crypt
{
    public static function encrypt(string $data, string $key, string $cipher = 'aes-256-gcm'): string
    {
        self::assertSupportedCipher($cipher);
        self::assertValidKey($key);

        $ivLength = openssl_cipher_iv_length($cipher);
        $iv = random_bytes($ivLength);
        $tag = '';
        $normalizedKey = self::normalizeKey($key, $cipher);

        $encrypted = openssl_encrypt(
            $data,
            $cipher,
            $normalizedKey,
            OPENSSL_RAW_DATA,
            $iv,
            $tag,
            '',
            16
        );

        if ($encrypted === false) {
            throw new RuntimeException('Encryption failed');
        }

        return base64_encode($iv . $tag . $encrypted);
    }

    public static function decrypt(string $data, string $key, string $cipher = 'aes-256-gcm'): string
    {
        self::assertSupportedCipher($cipher);
        self::assertValidKey($key);

        $decoded = base64_decode($data, true);
        if ($decoded === false) {
            throw new RuntimeException('Encrypted payload is not valid base64');
        }

        $ivLength = openssl_cipher_iv_length($cipher);
        $tagLength = 16;

        if (strlen($decoded) <= $ivLength + $tagLength) {
            throw new RuntimeException('Encrypted payload is too short');
        }

        $iv = substr($decoded, 0, $ivLength);
        $tag = substr($decoded, $ivLength, $tagLength);
        $encrypted = substr($decoded, $ivLength + $tagLength);
        $normalizedKey = self::normalizeKey($key, $cipher);

        $decrypted = openssl_decrypt(
            $encrypted,
            $cipher,
            $normalizedKey,
            OPENSSL_RAW_DATA,
            $iv,
            $tag
        );

        if ($decrypted === false) {
            throw new RuntimeException('Decryption failed');
        }

        return $decrypted;
    }

    private static function assertSupportedCipher(string $cipher): void
    {
        $ivLength = openssl_cipher_iv_length($cipher);
        if ($ivLength === false || stripos($cipher, 'gcm') === false) {
            throw new RuntimeException('Only OpenSSL GCM ciphers are supported');
        }
    }

    private static function assertValidKey(string $key): void
    {
        if ($key === '') {
            throw new RuntimeException('Encryption key cannot be empty');
        }
    }

    private static function normalizeKey(string $key, string $cipher): string
    {
        $expectedLength = str_contains($cipher, '128') ? 16 : 32;

        return substr(hash('sha256', $key, true), 0, $expectedLength);
    }
}
