<?php declare(strict_types=1);

namespace Marwa\Support;

class Obj
{
    /**
     * Convert an object to an array.
     *
     * @param object $object
     * @return array
     */
    public static function toArray(object $object): array
    {
        return json_decode(json_encode($object), true);
    }

    /**
     * Fill an object with properties.
     *
     * @param object $object
     * @param array $properties
     * @return object
     */
    public static function fill(object $object, array $properties): object
    {
        foreach ($properties as $key => $value) {
            $object->$key = $value;
        }
        return $object;
    }

    /**
     * Get a property from an object using "dot" notation.
     *
     * @param object $object
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public static function get(object $object, string $key, $default = null)
    {
        if (strpos($key, '.') === false) {
            return $object->$key ?? $default;
        }

        foreach (explode('.', $key) as $segment) {
            if (is_object($object) && isset($object->$segment)) {
                $object = $object->$segment;
            } else {
                return $default;
            }
        }

        return $object;
    }

    /**
     * Check if a property exists on an object using "dot" notation.
     *
     * @param object $object
     * @param string $key
     * @return bool
     */
    public static function has(object $object, string $key): bool
    {
        if (strpos($key, '.') === false) {
            return isset($object->$key);
        }

        foreach (explode('.', $key) as $segment) {
            if (is_object($object) && isset($object->$segment)) {
                $object = $object->$segment;
            } else {
                return false;
            }
        }

        return true;
    }

    /**
     * Convert the object to its JSON representation.
     *
     * @param object $object
     * @param int $options
     * @return string
     */
    public static function toJson(object $object, int $options = 0): string
    {
        return json_encode($object, $options);
    }

    /**
     * Clone the given object.
     *
     * @param object $object
     * @return object
     */
    public static function clone(object $object): object
    {
        return clone $object;
    }

    /**
     * Get the class name of the given object.
     *
     * @param object $object
     * @return string
     */
    public static function className(object $object): string
    {
        return get_class($object);
    }

    /**
     * Get all properties of an object.
     *
     * @param object $object
     * @return array
     */
    public static function properties(object $object): array
    {
        return get_object_vars($object);
    }

}