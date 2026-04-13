<?php

declare(strict_types=1);

namespace Marwa\Support;

class XSS
{
    private static array $htmlEntities = [
        '&' => '&amp;',
        '<' => '&lt;',
        '>' => '&gt;',
        '"' => '&quot;',
        "'" => '&#x27;',
        '/' => '&#x2F;',
    ];

    public static function clean(string $input): string
    {
        $input = self::removeEventHandlers($input);
        $input = self::removeCssExpressions($input);
        $input = self::removeDataUris($input);
        $input = strip_tags($input, self::getAllowedTags());

        return strtr($input, self::$htmlEntities);
    }

    private static function removeEventHandlers(string $input): string
    {
        $patterns = [
            '/\s+on\w+\s*=\s*(["\'])[^"\']*\1/i',
            '/\s+on\w+\s*=\s*[^\s>]+/i',
            '/\s+on\w+\s*=\s*(["\'])\s*$/mi',
        ];

        foreach ($patterns as $pattern) {
            $input = preg_replace($pattern, '', $input);
        }

        return $input;
    }

    private static function removeCssExpressions(string $input): string
    {
        $patterns = [
            '/expression\s*\(/i',
            '/url\s*\(\s*["\']?\s*javascript:/i',
            '/behavior\s*:/i',
            '/-moz-binding\s*:/i',
        ];

        foreach ($patterns as $pattern) {
            $input = preg_replace($pattern, '', $input);
        }

        return $input;
    }

    private static function removeDataUris(string $input): string
    {
        $pattern = '/\s+href\s*=\s*["\']?\s*data:/i';
        return preg_replace($pattern, '', $input);
    }

    private static function getAllowedTags(): string
    {
        return '<p><br><b><i><strong><em><ul><ol><li><a><span><div><table><thead><tbody><tr><th><td><h1><h2><h3><h4><h5><h6><img><hr><pre><code><blockquote><article><section><nav><aside><header><footer><main><figure><figcaption>';
    }
}
