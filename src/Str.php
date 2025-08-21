<?php declare(strict_types=1);

namespace Marwa\Support;

class Str
{
    /**
     * Convert a string to lowercase.
     *
     * @param string $value
     * @return string
     */
    public static function lower(string $value): string
    {
        return mb_strtolower($value, 'UTF-8');
    }

    /**
     * Convert a string to uppercase.
     *
     * @param string $value
     * @return string
     */
    public static function upper(string $value): string
    {
        return mb_strtoupper($value, 'UTF-8');
    }

    /**
     * Convert a string to title case.
     *
     * @param string $value
     * @return string
     */
    public static function title(string $value): string
    {
        return mb_convert_case($value, MB_CASE_TITLE, 'UTF-8');
    }

    /**
     * Generate a random string.
     *
     * @param int $length
     * @return string
     */
    public static function random(int $length = 16): string
    {
        $string = '';
        while (($len = strlen($string)) < $length) {
            $size = $length - $len;
            $bytes = random_bytes($size);
            $string .= substr(str_replace(['/', '+', '='], '', base64_encode($bytes)), 0, $size);
        }
        return $string;
    }

    /**
     * Limit the number of characters in a string.
     *
     * @param string $value
     * @param int $limit
     * @param string $end
     * @return string
     */
    public static function limit(string $value, int $limit = 100, string $end = '...'): string
    {
        if (mb_strwidth($value, 'UTF-8') <= $limit) {
            return $value;
        }
        return rtrim(mb_strimwidth($value, 0, $limit, '', 'UTF-8')) . $end;
    }

    /**
     * Determine if a string contains a given substring.
     *
     * @param string $haystack
     * @param string|array $needles
     * @return bool
     */
    public static function contains(string $haystack, $needles): bool
    {
        foreach ((array) $needles as $needle) {
            if ($needle !== '' && mb_strpos($haystack, $needle) !== false) {
                return true;
            }
        }
        return false;
    }

    /**
     * Determine if a string starts with a given substring.
     *
     * @param string $haystack
     * @param string|array $needles
     * @return bool
     */
    public static function startsWith(string $haystack, $needles): bool
    {
        foreach ((array) $needles as $needle) {
            if ((string) $needle !== '' && strncmp($haystack, $needle, strlen($needle)) === 0) {
                return true;
            }
        }
        return false;
    }

    /**
     * Determine if a string ends with a given substring.
     *
     * @param string $haystack
     * @param string|array $needles
     * @return bool
     */
    public static function endsWith(string $haystack, $needles): bool
    {
        foreach ((array) $needles as $needle) {
            if ($needle !== '' && substr($haystack, -strlen($needle)) === (string) $needle) {
                return true;
            }
        }
        return false;
    }

    /**
     * Convert a string to snake case.
     *
     * @param string $value
     * @param string $delimiter
     * @return string
     */
    public static function snake(string $value, string $delimiter = '_'): string
    {
        if (!ctype_lower($value)) {
            $value = preg_replace('/\s+/u', '', ucwords($value));
            $value = static::lower(preg_replace('/(.)(?=[A-Z])/u', '$1' . $delimiter, $value));
        }
        return $value;
    }

    /**
     * Convert a string to camel case.
     *
     * @param string $value
     * @return string
     */
    public static function camel(string $value): string
    {
        return lcfirst(static::studly($value));
    }

    /**
     * Convert a string to studly caps case.
     *
     * @param string $value
     * @return string
     */
    public static function studly(string $value): string
    {
        $value = ucwords(str_replace(['-', '_'], ' ', $value));
        return str_replace(' ', '', $value);
    }

    /**
     * Generate a URL friendly "slug" from a given string.
     *
     * @param string $title
     * @param string $separator
     * @return string
     */
    public static function slug(string $title, string $separator = '-'): string
    {
        // Convert all dashes/underscores into separator
        $flip = $separator === '-' ? '_' : '-';
        $title = preg_replace('![' . preg_quote($flip) . ']+!u', $separator, $title);
        
        // Replace @ with the word 'at'
        $title = str_replace('@', $separator . 'at' . $separator, $title);
        
        // Remove all characters that are not the separator, letters, numbers, or whitespace
        $title = preg_replace('![^' . preg_quote($separator) . '\pL\pN\s]+!u', '', static::lower($title));
        
        // Replace all separator characters and whitespace by a single separator
        $title = preg_replace('![' . preg_quote($separator) . '\s]+!u', $separator, $title);
        
        return trim($title, $separator);
    }

     /**
     * Get portion of string between two strings
     */
    public static function between(string $string, string $start, string $end): string
    {
        return strstr(ltrim(strstr($string, $start)), $end, true) ?: '';
    }

    /**
     * Determine if string matches a pattern (supports * wildcards)
     */
    public static function is(string $pattern, string $value): bool
    {
        if ($pattern === $value) {
            return true;
        }
        $pattern = preg_quote($pattern, '#');
        $pattern = str_replace('\*', '.*', $pattern);
        return preg_match('#^'.$pattern.'\z#u', $value) === 1;
    }

    /**
     * Convert HTML to plain text
     */
    public static function stripTags(string $html, string $allowedTags = ''): string
    {
        return html_entity_decode(strip_tags($html, $allowedTags));
    }

    /**
     * Convert special characters to HTML entities
     */
    public static function escape(string $value, bool $doubleEncode = true): string
    {
        return htmlspecialchars($value, ENT_QUOTES, 'UTF-8', $doubleEncode);
    }

    /**
     * Generate a hash from string
     */
    public static function hash(string $value, string $algorithm = 'sha256'): string
    {
        return hash($algorithm, $value);
    }

    /**
     * Get number of words in string
     */
    public static function wordCount(string $string): int
    {
        return str_word_count($string);
    }

    /**
     * Pad string to a certain length
     */
    public static function pad(string $value, int $length, string $padString = ' ', int $type = STR_PAD_RIGHT): string
    {
        return str_pad($value, $length, $padString, $type);
    }

    /**
     * Repeat string N times
     */
    public static function repeat(string $string, int $times): string
    {
        return str_repeat($string, $times);
    }

    /**
     * Reverse string with multibyte support
     */
    public static function reverse(string $string): string
    {
        return implode('', array_reverse(mb_str_split($string)));
    }

    /**
     * Get substring with multibyte support
     */
    public static function substring(string $string, int $start, ?int $length = null): string
    {
        return mb_substr($string, $start, $length, 'UTF-8');
    }

    /**
     * Convert string to bytes (for memory size strings)
     */
    public static function toBytes(string $value): int
    {
        preg_match('/^\s*([0-9.]+)\s*([KMGTPE]?B)\s*$/i', $value, $matches);
        $number = (float) $matches[1];
        $unit = strtoupper($matches[2] ?? 'B');
        
        return match($unit) {
            'KB' => $number * 1024,
            'MB' => $number * 1024 ** 2,
            'GB' => $number * 1024 ** 3,
            'TB' => $number * 1024 ** 4,
            'PB' => $number * 1024 ** 5,
            'EB' => $number * 1024 ** 6,
            default => (int) $number
        };
    }

}