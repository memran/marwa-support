<?php
declare(strict_types=1);

namespace Marwa\Support;

use RuntimeException;

class Crypt
{
    public static function encrypt(string $data, string $key, string $cipher = 'aes-256-gcm'): string
    {
        $ivLength = openssl_cipher_iv_length($cipher);
        if ($ivLength === false) {
            throw new RuntimeException('Invalid cipher algorithm');
        }

        $iv = random_bytes($ivLength);
        $tag = '';

        $encrypted = openssl_encrypt(
            $data,
            $cipher,
            $key,
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
        $data = base64_decode($data);
        $ivLength = openssl_cipher_iv_length($cipher);

        if ($ivLength === false) {
            throw new RuntimeException('Invalid cipher algorithm');
        }

        $iv = substr($data, 0, $ivLength);
        $tag = substr($data, $ivLength, 16);
        $encrypted = substr($data, $ivLength + 16);

        $decrypted = openssl_decrypt(
            $encrypted,
            $cipher,
            $key,
            OPENSSL_RAW_DATA,
            $iv,
            $tag
        );

        if ($decrypted === false) {
            throw new RuntimeException('Decryption failed');
        }

        return $decrypted;
    }
}