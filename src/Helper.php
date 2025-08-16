<?php
declare(strict_types=1);

namespace Marwa\Support;

class Helper
{
    /**
     * Tap into the chain without breaking it
     *
     * @template T
     * @param T $value
     * @param callable(T): void $callback
     * @return T
     */
    public static function tap($value, callable $callback)
    {
        $callback($value);
        return $value;
    }

    /**
     * Pipe a value through a series of callables
     *
     * @template T
     * @param T $value
     * @param callable[] $callbacks
     * @return mixed
     */
    public static function pipe($value, array $callbacks)
    {
        return array_reduce(
            $callbacks,
            fn ($carry, callable $callback) => $callback($carry),
            $value
        );
    }

    /**
     * Execute callback with given value and return default
     *
     * @template T
     * @template U
     * @param T $value
     * @param callable(T): U $callback
     * @param U $default
     * @return U
     */
    public static function with($value, callable $callback, ?string $default = null)
    {
        return $callback($value) ?? $default;
    }

    /**
     * Dump and die (strict typed version)
     *
     * @param mixed ...$args
     * @return never
     */
    public static function dd(...$args): void
    {
        foreach ($args as $arg) {
            var_dump($arg);
        }
        exit(1);
    }

    /**
     * Retry operation with strict typing
     *
     * @param positive-int $times
     * @param callable(): mixed $callback
     * @param int<0, max> $sleep Milliseconds
     * @return mixed
     * @throws \Exception
     */
    public static function retry(int $times, callable $callback, int $sleep = 0)
    {
        beginning:
        try {
            return $callback();
        } catch (\Exception $e) {
            if (--$times < 1) {
                throw $e;
            }

            if ($sleep > 0) {
                usleep($sleep * 1000);
            }

            goto beginning;
        }
    }

    /**
     * Get nested data with strict typing
     *
     * @param mixed $target
     * @param string|array<int, string> $key
     * @param mixed $default
     * @return mixed
     */
    public static function dataGet($target, $key, ?string $default = null)
    {
        if (is_null($key)) {
            return $target;
        }

        $key = is_array($key) ? $key : explode('.', $key);

        foreach ($key as $segment) {
            if (is_array($target)) {
                if (!array_key_exists($segment, $target)) {
                    return $default;
                }
                $target = $target[$segment];
            } elseif (is_object($target)) {
                if (!isset($target->{$segment})) {
                    return $default;
                }
                $target = $target->{$segment};
            } else {
                return $default;
            }
        }

        return $target;
    }

    /**
     * Return default value of given value
     *
     * @template T
     * @template U
     * @param T $value
     * @param array<U> $args
     * @return T|U
     */
    public static function value($value, ...$args)
    {
        return $value instanceof \Closure ? $value(...$args) : $value;
    }


    /**
     * Check if value is empty (strict version)
     * @param mixed $value
     * @return bool
     */
    public static function empty($value): bool {
        if (is_numeric($value)) return false;
        if (is_bool($value)) return false;
        if (is_object($value)) return false;
        return empty($value);
    }

    /**
     * Get variable type (improved)
     * @param mixed $var
     * @return string
     */
    public static function typeOf($var): string {
        if (is_object($var)) return get_class($var);
        if (is_callable($var)) return 'callable';
        return gettype($var);
    }

    /**
     * Memoize function results
     * @param callable $function
     * @return callable
     */
    public static function memoize(callable $function): callable {
        return function() use ($function) {
            static $cache = [];
            $args = func_get_args();
            $key = serialize($args);
            return $cache[$key] ?? ($cache[$key] = $function(...$args));
        };
    }

    /**
     * Curry a function
     * @param callable $function
     * @return callable
     */
    public static function curry(callable $function): callable {
        return function(...$args) use ($function) {
            return count($args) >= (new \ReflectionFunction($function))->getNumberOfRequiredParameters()
                ? $function(...$args)
                : self::curry(fn(...$newArgs) => $function(...array_merge($args, $newArgs)));
        };
    }

    /**
     * Group array items by key/callback
     * @param array $array
     * @param string|callable $groupBy
     * @return array
     */
    public static function groupBy(array $array, $groupBy): array {
        $result = [];
        foreach ($array as $item) {
            $key = is_callable($groupBy) ? $groupBy($item) : ($item[$groupBy] ?? null);
            $result[$key][] = $item;
        }
        return $result;
    }

    /**
     * Key an array by a field/callback
     * @param array $array
     * @param string|callable $keyBy
     * @return array
     */
    public static function keyBy(array $array, $keyBy): array {
        $result = [];
        foreach ($array as $item) {
            $key = is_callable($keyBy) ? $keyBy($item) : ($item[$keyBy] ?? null);
            $result[$key] = $item;
        }
        return $result;
    }
       /**
     * Calculate percentage
     * @param float|int $part
     * @param float|int $whole
     * @param int $decimals
     * @return float
     */
    public static function percentage($part, $whole, int $decimals = 2): float {
        return round(($part / $whole) * 100, $decimals);
    }

    /**
     * Map a number from one range to another
     * @param float $value
     * @param float $fromLow
     * @param float $fromHigh
     * @param float $toLow
     * @param float $toHigh
     * @return float
     */
    public static function mapRange(
        float $value,
        float $fromLow,
        float $fromHigh,
        float $toLow,
        float $toHigh
    ): float {
        return ($value - $fromLow) * ($toHigh - $toLow) / ($fromHigh - $fromLow) + $toLow;
    }

    /**
     * Deep clone an object
     * @param object $object
     * @return object
     */
    public static function deepClone(object $object): object {
        return unserialize(serialize($object));
    }

    /**
     * Check if object implements interface
     * @param object|string $object
     * @param string $interface
     * @return bool
     */
    public static function implements($object, string $interface): bool {
        return in_array($interface, class_implements($object));
    }
        /**
     * Generate UUID v4
     * @return string
     */
    public static function uuid(): string {
        return sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            mt_rand(0, 0xffff), mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0x0fff) | 0x4000,
            mt_rand(0, 0x3fff) | 0x8000,
            mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
        );
    }

    /**
     * Truncate string with proper unicode support
     * @param string $string
     * @param int $length
     * @param string $append
     * @return string
     */
    public static function truncate(string $string, int $length, string $append = '...'): string {
        if (mb_strlen($string) <= $length) return $string;
        return mb_substr($string, 0, $length) . $append;
    }
     /**
         * Measure execution time of callback
         * @param callable $callback
         * @param int $precision
         * @return float Execution time in milliseconds
         */
        public static function measure(callable $callback, int $precision = 4): float {
            $start = microtime(true);
            $callback();
            return round((microtime(true) - $start) * 1000, $precision);
        }

        /**
         * Get memory usage in human-readable format
         * @param int $bytes
         * @return string
         */
        public static function memoryUsage(int $bytes): string {
            $units = ['B', 'KB', 'MB', 'GB', 'TB'];
            $bytes = max($bytes, 0);
            $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
            $pow = min($pow, count($units) - 1);
            $bytes /= (1 << (10 * $pow));
            return round($bytes, 2) . ' ' . $units[$pow];
        }
}