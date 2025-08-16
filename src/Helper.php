<?php declare(strict_type=1);

namespace Marwa\Support;

class Helper
{
    /**
     * Dump the passed variables and end the script.
     *
     * @param mixed ...$args
     * @return void
     */
    public static function dd(...$args): void
    {
        foreach ($args as $arg) {
            var_dump($arg);
        }
        die(1);
    }

    /**
     * Retry an operation a given number of times.
     *
     * @param int $times
     * @param callable $callback
     * @param int $sleep
     * @return mixed
     */
    public static function retry(int $times, callable $callback, int $sleep = 0)
    {
        $times--;

        beginning:
        try {
            return $callback();
        } catch (\Exception $e) {
            if ($times < 1) {
                throw $e;
            }

            $times--;

            if ($sleep > 0) {
                usleep($sleep * 1000);
            }

            goto beginning;
        }
    }

    /**
     * Return the default value of the given value.
     *
     * @param mixed $value
     * @param mixed ...$args
     * @return mixed
     */
    public static function value($value, ...$args)
    {
        return $value instanceof \Closure ? $value(...$args) : $value;
    }

    /**
     * Get an item from an array or object using "dot" notation.
     *
     * @param mixed $target
     * @param string|array|int|null $key
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
                if (!isset($target->$segment)) {
                    return $default;
                }
                $target = $target->$segment;
            } else {
                return $default;
            }
        }

        return $target;
    }

    /**
     * Set an item on an array or object using dot notation.
     *
     * @param mixed $target
     * @param string|array $key
     * @param mixed $value
     * @return mixed
     */
    public static function dataSet(&$target, $key, $value)
    {
        $segments = is_array($key) ? $key : explode('.', $key);
        $segment = array_shift($segments);

        if (is_array($target)) {
            if (empty($segments)) {
                $target[$segment] = $value;
            } else {
                if (!array_key_exists($segment, $target)) {
                    $target[$segment] = [];
                }
                static::dataSet($target[$segment], $segments, $value);
            }
        } elseif (is_object($target)) {
            if (empty($segments)) {
                $target->$segment = $value;
            } else {
                if (!isset($target->$segment)) {
                    $target->$segment = new \stdClass();
                }
                static::dataSet($target->$segment, $segments, $value);
            }
        }

        return $target;
    }
}