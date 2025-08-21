<?php
declare(strict_types=1);

namespace Marwa\Support;

use Exception;
use RuntimeException;

class Security
{
    /**
     * Generate cryptographically secure random bytes
     */
    public static function randomBytes(int $length = 32): string
    {
        try {
            return random_bytes($length);
        } catch (Exception $e) {
            throw new RuntimeException('Could not generate random bytes');
        }
    }

    /**
     * Generate cryptographically secure random string
     */
    public static function randomString(int $length = 16, string $charset = 'alnum'): string
    {
        $characters = match($charset) {
            'alpha' => 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ',
            'numeric' => '0123456789',
            'alnum' => '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ',
            'hex' => '0123456789abcdef',
            'distinct' => '2345679ACDEFHJKLMNPRSTUVWXYZ',
            default => $charset
        };

        $charLength = strlen($characters);
        $result = '';

        for ($i = 0; $i < $length; $i++) {
            $result .= $characters[random_int(0, $charLength - 1)];
        }

        return $result;
    }

    /**
     * Generate secure password hash
     */
    public static function passwordHash(string $password, array $options = []): string
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

    /**
     * Verify password against hash
     */
    public static function passwordVerify(string $password, string $hash): bool
    {
        return password_verify($password, $hash);
    }

    /**
     * Check if password needs rehash
     */
    public static function passwordNeedsRehash(string $hash, array $options = []): bool
    {
        $options = array_merge(['cost' => 12], $options);
        return password_needs_rehash($hash, PASSWORD_DEFAULT, $options);
    }

    /**
     * Encrypt data using OpenSSL
     */
    public static function encrypt(string $data, string $key, string $cipher = 'aes-256-gcm'): string
    {
        $ivLength = openssl_cipher_iv_length($cipher);
        if ($ivLength === false) {
            throw new RuntimeException('Invalid cipher algorithm');
        }

        $iv = openssl_random_pseudo_bytes($ivLength);
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

    /**
     * Decrypt data using OpenSSL
     */
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

    /**
     * Generate secure CSRF token
     */
    public static function csrfToken(string $sessionKey = 'csrf_tokens'): string
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        $token = self::randomString(32);
        $_SESSION[$sessionKey][] = $token;

        // Keep only last 10 tokens
        if (count($_SESSION[$sessionKey]) > 10) {
            array_shift($_SESSION[$sessionKey]);
        }

        return $token;
    }

    /**
     * Verify CSRF token
     */
    public static function verifyCsrfToken(string $token, string $sessionKey = 'csrf_tokens'): bool
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        if (empty($_SESSION[$sessionKey])) {
            return false;
        }

        $index = array_search($token, $_SESSION[$sessionKey], true);
        
        if ($index !== false) {
            unset($_SESSION[$sessionKey][$index]);
            return true;
        }

        return false;
    }

    /**
     * Sanitize input data
     */
    public static function sanitize($input, string $type = 'string'): mixed
    {
        if (is_array($input)) {
            return array_map(fn($item) => self::sanitize($item, $type), $input);
        }

        return match($type) {
            'string' => filter_var(trim($input), FILTER_SANITIZE_SPECIAL_CHARS),
            'email' => filter_var($input, FILTER_SANITIZE_EMAIL),
            'url' => filter_var($input, FILTER_SANITIZE_URL),
            'int' => filter_var($input, FILTER_SANITIZE_NUMBER_INT),
            'float' => filter_var($input, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION),
            'encoded' => filter_var($input, FILTER_SANITIZE_ENCODED),
            'string_clean' => preg_replace('/[^\p{L}\p{N}\s]/u', '', $input),
            default => $input
        };
    }

    /**
     * Validate input data
     */
    public static function validate($input, string $type): bool
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
            'regex' => preg_match($type, $input) === 1,
            default => false
        };
    }

    /**
     * Prevent XSS attacks
     */
    public static function xssClean(string $input): string
    {
        // Remove JavaScript event handlers
        $input = preg_replace('/on\w+=\s*(["\']).*?\1/i', '', $input);
        $input = preg_replace('/on\w+=\s*[^>]*/i', '', $input);

        // Remove JavaScript URLs
        $input = preg_replace('/(javascript:|jscript:|vbscript:)/i', '', $input);

        // Remove unwanted tags
        $input = strip_tags($input, '<p><br><b><i><strong><em><ul><ol><li><a>');

        // Convert special characters
        return htmlspecialchars($input, ENT_QUOTES, 'UTF-8');
    }

    /**
     * Generate secure file upload name
     */
    public static function safeFileName(string $filename): string
    {
        $extension = pathinfo($filename, PATHINFO_EXTENSION);
        $name = pathinfo($filename, PATHINFO_FILENAME);
        
        // Sanitize name
        $name = preg_replace('/[^a-zA-Z0-9_-]/', '_', $name);
        $name = substr($name, 0, 100);
        
        return $name . '.' . $extension;
    }

    /**
     * Check if string contains potential malicious content
     */
    public static function isMalicious(string $input): bool
    {
        $patterns = [
            '/<script/i',
            '/javascript:/i',
            '/vbscript:/i',
            '/onload=/i',
            '/onerror=/i',
            '/onclick=/i',
            '/eval\(/i',
            '/base64_decode/i',
            '/document\.cookie/i',
            '/window\.location/i',
        ];

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $input)) {
                return true;
            }
        }

        return false;
    }


    /**
     * Secure compare two strings (timing attack safe)
     */
    public static function hashEquals(string $known, string $user): bool
    {
        return hash_equals($known, $user);
    }
}