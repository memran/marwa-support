<?php

declare(strict_types=1);

namespace Marwa\Support;

use RuntimeException;

class CSRF
{
    private const DEFAULT_MAX_AGE = 3600; // 1 hour

    public static function token(string $sessionKey = 'csrf_tokens', int $maxAge = self::DEFAULT_MAX_AGE): string
    {
        self::startSession();

        $token = Random::string(32);
        $tokenData = [
            'token' => $token,
            'created' => time(),
            'expires' => time() + $maxAge,
        ];

        $_SESSION[$sessionKey] ??= [];
        $_SESSION[$sessionKey][] = $tokenData;

        self::cleanupExpiredTokens($sessionKey);

        return $token;
    }

    public static function verify(string $token, string $sessionKey = 'csrf_tokens'): bool
    {
        self::startSession();

        if (empty($_SESSION[$sessionKey])) {
            return false;
        }

        foreach ($_SESSION[$sessionKey] as $index => $tokenData) {
            if (!is_array($tokenData) || !isset($tokenData['token'])) {
                continue;
            }

            if (hash_equals($tokenData['token'], $token)) {
                if ($tokenData['expires'] < time()) {
                    unset($_SESSION[$sessionKey][$index]);
                    $_SESSION[$sessionKey] = array_values($_SESSION[$sessionKey]);
                    return false;
                }

                unset($_SESSION[$sessionKey][$index]);
                $_SESSION[$sessionKey] = array_values($_SESSION[$sessionKey]);
                return true;
            }
        }

        return false;
    }

    private static function cleanupExpiredTokens(string $sessionKey): void
    {
        if (empty($_SESSION[$sessionKey])) {
            return;
        }

        $now = time();
        $_SESSION[$sessionKey] = array_values(array_filter(
            $_SESSION[$sessionKey],
            function ($tokenData) use ($now) {
                if (is_string($tokenData)) {
                    return true;
                }
                return isset($tokenData['expires']) && $tokenData['expires'] >= $now;
            }
        ));

        if (count($_SESSION[$sessionKey]) > 10) {
            $_SESSION[$sessionKey] = array_slice($_SESSION[$sessionKey], -10);
        }
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
