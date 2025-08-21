<?php
declare(strict_types=1);

namespace Marwa\Support;

use Marwa\Support\Crypt;
use Marwa\Support\Hash;
use Marwa\Support\Random;
use Marwa\Support\Sanitizer;
use Marwa\Support\Validator;
use Marwa\Support\CSRF;
use Marwa\Support\XSS;

class Security
{
    public static function encrypt(string $data, string $key): string
    {
        return Crypt::encrypt($data, $key);
    }

    public static function decrypt(string $data, string $key): string
    {
        return Crypt::decrypt($data, $key);
    }

    public static function hash(string $password): string
    {
        return Hash::make($password);
    }

    public static function verifyHash(string $password, string $hash): bool
    {
        return Hash::verify($password, $hash);
    }

    public static function randomBytes(int $length = 32): string
    {
        return Random::bytes($length);
    }

    public static function randomString(int $length = 16): string
    {
        return Random::string($length);
    }

    public static function uuid(): string
    {
        return Random::uuid();
    }

    public static function sanitize($input, string $type = 'string'): mixed
    {
        return Sanitizer::clean($input, $type);
    }

    public static function safeFileName(string $filename): string
    {
        return Sanitizer::filename($filename);
    }

    public static function validate($input, string $type): bool
    {
        return Validator::check($input, $type);
    }

    public static function isMalicious(string $input): bool
    {
        return Validator::isMalicious($input);
    }

    public static function csrfToken(): string
    {
        return CSRF::token();
    }

    public static function verifyCsrf(string $token): bool
    {
        return CSRF::verify($token);
    }

    public static function xssClean(string $input): string
    {
        return XSS::clean($input);
    }

    public static function hashEquals(string $known, string $user): bool
    {
        return hash_equals($known, $user);
    }
}