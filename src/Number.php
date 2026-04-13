<?php

declare(strict_types=1);

namespace Marwa\Support;

use InvalidArgumentException;
use NumberFormatter;

class Number
{
    /**
     * Format a number with thousand separators.
     *
     * @param float|int $value
     * @param int $decimals
     * @param string $decimalPoint
     * @param string $thousandsSeparator
     * @return string
     */
    public static function format(float|int $value, int $decimals = 0, string $decimalPoint = '.', string $thousandsSeparator = ','): string
    {
        return number_format((float) $value, $decimals, $decimalPoint, $thousandsSeparator);
    }

    /**
     * Format as currency.
     *
     * @param float|int $value
     * @param string $currency
     * @param string $locale
     * @return string
     */
    public static function currency(float|int $value, string $currency = 'USD', string $locale = 'en_US'): string
    {
        if (!class_exists(NumberFormatter::class)) {
            return '$' . self::format($value, 2);
        }
        $formatter = new NumberFormatter($locale, NumberFormatter::CURRENCY);
        return $formatter->formatCurrency((float) $value, $currency);
    }

    /**
     * Format as ordinal (1st, 2nd, 3rd, etc.).
     *
     * @param int $value
     * @param string $locale
     * @return string
     */
    public static function ordinal(int $value, string $locale = 'en_US'): string
    {
        if (!class_exists(NumberFormatter::class)) {
            $suffix = match ($value % 10) {
                1 => $value % 100 === 11 ? 'th' : 'st',
                2 => $value % 100 === 12 ? 'th' : 'nd',
                3 => $value % 100 === 13 ? 'th' : 'rd',
                default => 'th',
            };
            return $value . $suffix;
        }
        $formatter = new NumberFormatter($locale, NumberFormatter::ORDINAL);
        return $formatter->format($value);
    }

    /**
     * Format as spellout number.
     *
     * @param float|int $value
     * @param string $locale
     * @return string
     */
    public static function spellout(float|int $value, string $locale = 'en_US'): string
    {
        if (!class_exists(NumberFormatter::class)) {
            return (string) $value;
        }
        $formatter = new NumberFormatter($locale, NumberFormatter::SPELLOUT);
        return $formatter->format((float) $value);
    }

    /**
     * Convert number to words.
     *
     * @param float|int $value
     * @param string $locale
     * @return string
     */
    public static function toWords(float|int $value, string $locale = 'en_US'): string
    {
        return self::spellout($value, $locale);
    }

    /**
     * Parse a localized number string.
     *
     * @param string $value
     * @param string $locale
     * @return float
     */
    public static function parse(string $value, string $locale = 'en_US'): float
    {
        if (!class_exists(NumberFormatter::class)) {
            return (float) str_replace(',', '', $value);
        }
        $formatter = new NumberFormatter($locale, NumberFormatter::DECIMAL);
        $parsed = $formatter->parse($value);

        if ($parsed === false) {
            throw new InvalidArgumentException("Cannot parse number: {$value}");
        }

        return (float) $parsed;
    }

    /**
     * Format as percentage.
     *
     * @param float|int $value
     * @param int $decimals
     * @return string
     */
    public static function percentage(float|int $value, int $decimals = 0): string
    {
        return number_format((float) $value * 100, $decimals) . '%';
    }

    /**
     * Format with compact notation (1K, 1M, etc.).
     *
     * @param float|int $value
     * @param int $decimals
     * @return string
     */
    public static function compact(float|int $value, int $decimals = 1): string
    {
        $value = (float) $value;

        if (abs($value) >= 1000000000) {
            return round($value / 1000000000, $decimals) . 'B';
        }

        if (abs($value) >= 1000000) {
            return round($value / 1000000, $decimals) . 'M';
        }

        if (abs($value) >= 1000) {
            return round($value / 1000, $decimals) . 'K';
        }

        return (string) round($value, $decimals);
    }

    /**
     * Format bytes to human readable.
     *
     * @param int $bytes
     * @param int $decimals
     * @return string
     */
    public static function bytes(int $bytes, int $decimals = 2): string
    {
        if ($bytes < 0) {
            throw new InvalidArgumentException('Bytes must be non-negative');
        }

        $units = ['B', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= (1 << (10 * $pow));

        return round($bytes, $decimals) . ' ' . $units[$pow];
    }

    /**
     * Round to specified precision.
     *
     * @param float|int $value
     * @param int $precision
     * @param int $mode
     * @return float
     */
    public static function round(float|int $value, int $precision = 0, int $mode = PHP_ROUND_HALF_UP): float
    {
        return round((float) $value, $precision, $mode === 0 ? PHP_ROUND_HALF_UP : $mode);
    }

    /**
     * Round up to nearest integer.
     *
     * @param float|int $value
     * @return int
     */
    public static function ceil(float|int $value): int
    {
        return (int) ceil((float) $value);
    }

    /**
     * Round down to nearest integer.
     *
     * @param float|int $value
     * @return int
     */
    public static function floor(float|int $value): int
    {
        return (int) floor((float) $value);
    }

    /**
     * Clamp a value between min and max.
     *
     * @param float|int $value
     * @param float|int $min
     * @param float|int $max
     * @return float|int
     */
    public static function clamp(float|int $value, float|int $min, float|int $max)
    {
        if ($value < $min) {
            return $min;
        }
        if ($value > $max) {
            return $max;
        }
        return $value;
    }

    /**
     * Check if a value is between min and max (inclusive).
     *
     * @param float|int $value
     * @param float|int $min
     * @param float|int $max
     * @return bool
     */
    public static function between(float|int $value, float|int $min, float|int $max): bool
    {
        return $value >= $min && $value <= $max;
    }

    /**
     * Format as Roman numerals.
     *
     * @param int $value
     * @return string
     * @throws InvalidArgumentException
     */
    public static function roman(int $value): string
    {
        if ($value < 1 || $value > 3999) {
            throw new InvalidArgumentException('Value must be between 1 and 3999');
        }

        $roman = '';
        $values = [1000, 900, 500, 400, 100, 90, 50, 40, 10, 9, 5, 4, 1];
        $numerals = ['M', 'CM', 'D', 'CD', 'C', 'XC', 'L', 'XL', 'X', 'IX', 'V', 'IV', 'I'];

        foreach ($values as $i => $value2) {
            while ($value >= $value2) {
                $roman .= $numerals[$i];
                $value -= $value2;
            }
        }

        return $roman;
    }
}
