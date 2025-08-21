<?php
declare(strict_types=1);

namespace Marwa\Support;

use Marwa\Support\Random;

class CSRF
{
    public static function token(string $sessionKey = 'csrf_tokens'): string
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        $token = Random::string(32);
        $_SESSION[$sessionKey][] = $token;

        // Keep only last 10 tokens
        if (count($_SESSION[$sessionKey]) > 10) {
            array_shift($_SESSION[$sessionKey]);
        }

        return $token;
    }

    public static function verify(string $token, string $sessionKey = 'csrf_tokens'): bool
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
}