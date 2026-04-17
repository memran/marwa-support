<?php

declare(strict_types=1);

namespace Marwa\Support\Validation\Rules;

use Marwa\Support\Validation\AbstractRule;
use Psr\Http\Message\UploadedFileInterface;

class MimesRule extends AbstractRule
{
    private const MIME_MAP = [
        'htm' => 'text/html',
        'html' => 'text/html',
        'php' => 'text/php',
        'txt' => 'text/plain',
        'csv' => 'text/csv',
        'json' => 'application/json',
        'pdf' => 'application/pdf',
        'zip' => 'application/zip',
        'doc' => 'application/msword',
        'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'xls' => 'application/vnd.ms-excel',
        'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        'ppt' => 'application/vnd.ms-powerpoint',
        'pptx' => 'application/vnd.openxmlformats-officedocument.presentationml.presentation',
        'odt' => 'application/vnd.oasis.opendocument.text',
        'ods' => 'application/vnd.oasis.opendocument.spreadsheet',
        'jpg' => 'image/jpeg',
        'jpeg' => 'image/jpeg',
        'png' => 'image/png',
        'gif' => 'image/gif',
        'webp' => 'image/webp',
        'svg' => 'image/svg+xml',
        'bmp' => 'image/bmp',
        'tiff' => 'image/tiff',
        'tif' => 'image/tiff',
        'ico' => 'image/x-icon',
    ];

    public function name(): string
    {
        return 'mimes';
    }

    public function validate(mixed $value, array $context): bool
    {
        if (!$value instanceof UploadedFileInterface) {
            return false;
        }

        $allowedMimes = $this->getAllowedMimes();
        $mimeType = $value->getClientMediaType();

        return in_array($mimeType, $allowedMimes, true);
    }

    private function getAllowedMimes(): array
    {
        $params = $this->params();
        $mimes = [];

        foreach ($params as $ext) {
            $ext = strtolower(trim((string) $ext));
            if (isset(self::MIME_MAP[$ext])) {
                $mimes[] = self::MIME_MAP[$ext];
            } else {
                $mimes[] = $ext;
            }
        }

        return $mimes;
    }

    public function message(string $field, array $attributes): string
    {
        return $this->formatMessage(
            'The :attribute must be a file of type: :values.',
            $field,
            $attributes
        );
    }
}