<?php
declare(strict_types=1);

namespace Marwa\Support;

class XSS
{
    public static function clean(string $input): string
    {
        $input = preg_replace('/on\w+=\s*(["\']).*?\1/i', '', $input);
        $input = preg_replace('/on\w+=\s*[^>]*/i', '', $input);
        $input = preg_replace('/(javascript:|jscript:|vbscript:)/i', '', $input);
        $input = strip_tags($input, '<p><br><b><i><strong><em><ul><ol><li><a>');
        
        return htmlspecialchars($input, ENT_QUOTES, 'UTF-8');
    }
}