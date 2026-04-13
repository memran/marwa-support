<?php

declare(strict_types=1);

namespace Marwa\Support;

use InvalidArgumentException;
use JsonException;

class Json
{
    /**
     * Encode a value to JSON.
     *
     * @param mixed $value
     * @param int $options
     * @return string
     * @throws JsonException
     */
    public static function encode(mixed $value, int $options = JSON_THROW_ON_ERROR): string
    {
        return json_encode($value, $options);
    }

    /**
     * Decode a JSON string to a value.
     *
     * @param string $json
     * @param bool $assoc
     * @param int $depth
     * @param int $options
     * @return mixed
     * @throws JsonException
     */
    public static function decode(string $json, bool $assoc = true, int $depth = 512, int $options = 0): mixed
    {
        return json_decode($json, $assoc, $depth, $options | JSON_THROW_ON_ERROR);
    }

    /**
     * Check if a string is valid JSON.
     *
     * @param string $json
     * @return bool
     */
    public static function isValid(string $json): bool
    {
        json_decode($json);
        return json_last_error() === JSON_ERROR_NONE;
    }

    /**
     * Encode and make it readable.
     *
     * @param mixed $value
     * @param int $depth
     * @return string
     * @throws JsonException
     */
    public static function pretty(mixed $value, int $depth = 512): string
    {
        return json_encode($value, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR, $depth);
    }

    /**
     * Minify JSON.
     *
     * @param string $json
     * @return string
     * @throws JsonException
     */
    public static function minify(string $json): string
    {
        if (!self::isValid($json)) {
            throw new InvalidArgumentException('Invalid JSON string');
        }

        return json_encode(json_decode($json, true), JSON_THROW_ON_ERROR);
    }

    /**
     * Extract a value from JSON using dot notation.
     *
     * @param string $json
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public static function get(string $json, string $key, mixed $default = null): mixed
    {
        $data = self::decode($json, true);
        return Arr::get($data, $key, $default);
    }

    /**
     * Check if JSON has a key.
     *
     * @param string $json
     * @param string $key
     * @return bool
     */
    public static function has(string $json, string $key): bool
    {
        $data = self::decode($json, true);
        return Arr::has($data, $key);
    }

    /**
     * Create JSON from array with UTF-8 encoding.
     *
     * @param array $data
     * @return string
     * @throws JsonException
     */
    public static function fromArray(array $data): string
    {
        return json_encode($data, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);
    }

    /**
     * Convert JSON to array.
     *
     * @param string $json
     * @return array
     * @throws JsonException
     */
    public static function toArray(string $json): array
    {
        return self::decode($json, true);
    }
}
