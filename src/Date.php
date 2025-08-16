<?php declare(strict_types=1);

namespace Marwa\Support;

class Date
{
    /**
     * Format a date.
     *
     * @param \DateTimeInterface|string|int $date
     * @param string $format
     * @return string
     */
    public static function format($date, string $format = 'Y-m-d H:i:s'): string
    {
        if (is_numeric($date)) {
            return date($format, $date);
        }

        if (is_string($date)) {
            $date = new \DateTime($date);
        }

        return $date->format($format);
    }

    /**
     * Get the difference in days between two dates.
     *
     * @param \DateTimeInterface|string $date1
     * @param \DateTimeInterface|string $date2
     * @return int
     */
    public static function diffInDays($date1, $date2): int
    {
        if (is_string($date1)) {
            $date1 = new \DateTime($date1);
        }

        if (is_string($date2)) {
            $date2 = new \DateTime($date2);
        }

        return $date1->diff($date2)->days;
    }

    /**
     * Add days to a date.
     *
     * @param \DateTimeInterface|string $date
     * @param int $days
     * @return \DateTime
     */
    public static function addDays($date, int $days): \DateTime
    {
        if (is_string($date)) {
            $date = new \DateTime($date);
        }

        return $date->add(new \DateInterval("P{$days}D"));
    }

    /**
     * Subtract days from a date.
     *
     * @param \DateTimeInterface|string $date
     * @param int $days
     * @return \DateTime
     */
    public static function subDays($date, int $days): \DateTime
    {
        if (is_string($date)) {
            $date = new \DateTime($date);
        }

        return $date->sub(new \DateInterval("P{$days}D"));
    }

    /**
     * Check if a date is after another date.
     *
     * @param \DateTimeInterface|string $date
     * @param \DateTimeInterface|string $compareTo
     * @return bool
     */
    public static function isAfter($date, $compareTo): bool
    {
        if (is_string($date)) {
            $date = new \DateTime($date);
        }

        if (is_string($compareTo)) {
            $compareTo = new \DateTime($compareTo);
        }

        return $date > $compareTo;
    }

    /**
     * Check if a date is before another date.
     *
     * @param \DateTimeInterface|string $date
     * @param \DateTimeInterface|string $compareTo
     * @return bool
     */
    public static function isBefore($date, $compareTo): bool
    {
        if (is_string($date)) {
            $date = new \DateTime($date);
        }

        if (is_string($compareTo)) {
            $compareTo = new \DateTime($compareTo);
        }

        return $date < $compareTo;
    }

    /**
     * Get the current date and time.
     *
     * @return \DateTime
     */
    public static function now(): \DateTime
    {
        return new \DateTime();
    }

    /**
     * Get the current timestamp.
     *
     * @return int
     */
    public static function timestamp(): int
    {
        return time();
    }
}