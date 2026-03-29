<?php

declare(strict_types=1);

namespace Marwa\Support;

use RuntimeException;

class CSRF
{
    public static function token(string $sessionKey = 'csrf_tokens'): string
    {
        self::startSession();

        $token = Random::string(32);
        $_SESSION[$sessionKey] ??= [];
        $_SESSION[$sessionKey][] = $token;

        // Keep only last 10 tokens
        if (count($_SESSION[$sessionKey]) > 10) {
            array_shift($_SESSION[$sessionKey]);
        }

        return $token;
    }

    public static function verify(string $token, string $sessionKey = 'csrf_tokens'): bool
    {
        self::startSession();

        if (empty($_SESSION[$sessionKey])) {
            return false;
        }

        foreach ($_SESSION[$sessionKey] as $index => $knownToken) {
            if (is_string($knownToken) && hash_equals($knownToken, $token)) {
                unset($_SESSION[$sessionKey][$index]);
                $_SESSION[$sessionKey] = array_values($_SESSION[$sessionKey]);

                return true;
            }
        }

        return false;
    }

    private static function startSession(): void
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            return;
        }

        if (headers_sent()) {
            throw new RuntimeException('Cannot start session after headers have been sent');
        }

        if (!session_start()) {
            throw new RuntimeException('Unable to start session for CSRF handling');
        }
    }
}
