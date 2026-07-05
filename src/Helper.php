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
    public static function with($value, callable $callback, $default = null)
    {
        if ($value === null) {
            return $default;
        }

        return $callback($value) ?? $default;
    }

    /**
     * Dump values as a string without terminating execution.
     */
    public static function dump(...$args): string
    {
        ob_start();

        foreach ($args as $arg) {
            var_dump($arg);
        }

        return (string) ob_get_clean();
    }

    /**
     * Dump and die (strict typed version)
     *
     * @param mixed ...$args
     * @return never
     */
    public static function dd(...$args): void
    {
        echo self::dump(...$args);
        exit(1);
    }

    /**
     * Retry operation with strict typing
     *
     * @param int $times
     * @param callable(): mixed $callback
     * @param int<0, max> $sleep Milliseconds
     * @return mixed
     * @throws \Exception
     */
    public static function retry(int $times, callable $callback, int $sleep = 0)
    {
        if ($times < 1) {
            throw new \InvalidArgumentException('Retry attempts must be greater than zero');
        }

        do {
            try {
                return $callback();
            } catch (\Throwable $e) {
                if (--$times < 1) {
                    throw $e;
                }

                if ($sleep > 0) {
                    usleep($sleep * 1000);
                }
            }
        } while (true);
    }

    /**
     * Get nested data with strict typing
     *
     * @param mixed $target
     * @param string|array<int, string>|null $key
     * @param mixed $default
     * @return mixed
     */
    public static function dataGet($target, $key, $default = null)
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
    public static function empty($value): bool
    {
        if (is_numeric($value) || is_bool($value)) {
            return false;
        }

        if (is_object($value)) {
            return count(get_object_vars($value)) === 0;
        }

        return empty($value);
    }

    /**
     * Get variable type (improved)
     * @param mixed $var
     * @return string
     */
    public static function typeOf($var): string
    {
        if ($var instanceof \Closure) {
            return 'callable';
        }

        if (is_object($var)) {
            return get_class($var);
        }

        return gettype($var);
    }

    /**
     * Memoize function results
     * @param callable $function
     * @return callable
     */
    public static function memoize(callable $function, int $maxSize = 128): callable
    {
        return function (...$args) use ($function, $maxSize) {
            static $cache = [];
            $key = serialize($args);

            if (array_key_exists($key, $cache)) {
                return $cache[$key];
            }

            if (count($cache) >= $maxSize) {
                array_shift($cache);
            }

            $cache[$key] = $function(...$args);
            return $cache[$key];
        };
    }

    /**
     * Curry a function
     * @param callable $function
     * @return callable
     */
    public static function curry(callable $function): callable
    {
        $reflection = new \ReflectionFunction(\Closure::fromCallable($function));
        $requiredParameters = $reflection->getNumberOfRequiredParameters();

        $resolver = static function (array $arguments) use (&$resolver, $function, $requiredParameters) {
            return static function (...$newArguments) use (&$resolver, $function, $requiredParameters, $arguments) {
                $mergedArguments = array_merge($arguments, $newArguments);

                if (count($mergedArguments) >= $requiredParameters) {
                    return $function(...$mergedArguments);
                }

                return $resolver($mergedArguments);
            };
        };

        return $resolver([]);
    }

    /**
     * Group array items by key/callback
     * @param array $array
     * @param string|callable $groupBy
     * @return array
     */
    public static function groupBy(array $array, $groupBy): array
    {
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
    public static function keyBy(array $array, $keyBy): array
    {
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
    public static function percentage($part, $whole, int $decimals = 2): float
    {
        if ((float) $whole === 0.0) {
            throw new \InvalidArgumentException('Whole must not be zero');
        }

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
        if ($fromHigh === $fromLow) {
            throw new \InvalidArgumentException('Source range must not be zero');
        }

        return ($value - $fromLow) * ($toHigh - $toLow) / ($fromHigh - $fromLow) + $toLow;
    }

    /**
     * Deep clone an object without triggering __wakeup/__destruct magic methods.
     *
     * @template T of object
     * @param T $object
     * @return T
     */
    public static function deepClone(object $object): object
    {
        return self::recursiveClone($object, []);
    }

    /**
     * @param object $object
     * @param array<int, object> $cloned
     * @return object
     */
    private static function recursiveClone(object $object, array $cloned): object
    {
        $oid = spl_object_id($object);
        if (isset($cloned[$oid])) {
            return $cloned[$oid];
        }

        $clone = clone $object;
        $cloned[$oid] = $clone;

        $reflection = new \ReflectionObject($clone);
        foreach ($reflection->getProperties() as $property) {
            $property->setAccessible(true);
            $value = $property->getValue($clone);

            if (is_object($value)) {
                $property->setValue($clone, self::recursiveClone($value, $cloned));
            } elseif (is_array($value)) {
                $property->setValue($clone, self::cloneArrayValues($value, $cloned));
            }
        }

        return $clone;
    }

    /**
     * @param array $array
     * @param array<int, object> $cloned
     * @return array
     */
    private static function cloneArrayValues(array $array, array $cloned): array
    {
        $result = [];
        foreach ($array as $key => $value) {
            if (is_object($value)) {
                $result[$key] = self::recursiveClone($value, $cloned);
            } elseif (is_array($value)) {
                $result[$key] = self::cloneArrayValues($value, $cloned);
            } else {
                $result[$key] = $value;
            }
        }
        return $result;
    }

    /**
     * Check if object implements interface
     * @param object|string $object
     * @param string $interface
     * @return bool
     */
    public static function implements($object, string $interface): bool
    {
        return in_array($interface, class_implements($object), true);
    }
    /**
     * Generate UUID v4
     * @return string
     */
    public static function uuid(): string
    {
        return Random::uuid();
    }

    /**
     * Truncate string with proper unicode support
     * @param string $string
     * @param int $length
     * @param string $append
     * @return string
     */
    public static function truncate(string $string, int $length, string $append = '...'): string
    {
        if (mb_strlen($string) <= $length) {
            return $string;
        }
        return mb_substr($string, 0, $length) . $append;
    }
    /**
        * Measure execution time of callback
        * @param callable $callback
        * @param int $precision
        * @return float Execution time in milliseconds
        */
    public static function measure(callable $callback, int $precision = 4): float
    {
        $start = microtime(true);
        $callback();
        return round((microtime(true) - $start) * 1000, $precision);
    }

    /**
     * Get memory usage in human-readable format
     * @param int $bytes
     * @return string
     */
    public static function memoryUsage(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= (1 << (10 * $pow));
        return number_format($bytes, 2, '.', '') . ' ' . $units[$pow];
    }
}
