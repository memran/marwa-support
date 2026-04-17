<?php

declare(strict_types=1);

namespace Marwa\Support\Validation\Helpers;

class TypeValidators
{
    public function isEmpty(mixed $value, bool $checkFalsy = false): bool
    {
        if (is_null($value)) {
            return true;
        }

        if (is_string($value) && trim($value) === '') {
            return true;
        }

        if (is_array($value) && count($value) === 0) {
            return true;
        }

        if ($checkFalsy) {
            if ($value === false) {
                return true;
            }
            if ($value === 0 || $value === '0') {
                return true;
            }
        }

        return false;
    }

    public function isInteger(mixed $value): bool
    {
        if (is_int($value)) {
            return true;
        }

        if (is_string($value) && preg_match('/^-?\d+$/', $value)) {
            return true;
        }

        return false;
    }

    public function isBoolean(mixed $value): bool
    {
        if (is_bool($value)) {
            return true;
        }

        if (is_string($value)) {
            $lower = strtolower($value);
            return in_array($lower, ['true', 'false', '1', '0', 'yes', 'no'], true);
        }

        return false;
    }

    public function isAccepted(mixed $value): bool
    {
        if (is_bool($value)) {
            return $value === true;
        }

        if (is_string($value)) {
            $lower = strtolower($value);
            return in_array($lower, ['yes', 'on', '1', 'true'], true);
        }

        return $value == 1;
    }

    public function isDeclined(mixed $value): bool
    {
        if (is_bool($value)) {
            return $value === false;
        }

        if (is_string($value)) {
            $lower = strtolower($value);
            return in_array($lower, ['no', 'off', '0', 'false'], true);
        }

        return $value == 0;
    }

    /**
     * @param \Psr\Http\Message\UploadedFileInterface|object $value
     */
    public function isImage(object $value): bool
    {
        if (!($value instanceof \Psr\Http\Message\UploadedFileInterface)) {
            return false;
        }

        $mimeType = $value->getClientMediaType();
        $allowedMimes = [
            'image/jpeg',
            'image/png',
            'image/gif',
            'image/webp',
            'image/svg+xml',
            'image/bmp',
            'image/tiff',
        ];

        return in_array($mimeType, $allowedMimes, true);
    }
}