<?php

declare(strict_types=1);

namespace Marwa\Support;

class XSS
{
    private const SAFE_TAGS = '<p><br><b><i><strong><em><ul><ol><li><a>';

    private const DANGEROUS_PROTOCOLS = '/\b(javascript|jscript|vbscript|data|blob|livescript|mhtml|xss):/i';

    private const EVENT_HANDLER_ATTRS = '/\bon\w+\s*=\s*(?:"[^"]*"|\'[^\']*\'|[^\s>]+)/i';

    public static function clean(string $input): string
    {
        $input = strip_tags($input, self::SAFE_TAGS);
        $input = preg_replace(self::EVENT_HANDLER_ATTRS, '', $input);
        $input = preg_replace(self::DANGEROUS_PROTOCOLS, '', $input);

        return htmlspecialchars($input, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }
}
