<?php

declare(strict_types=1);

namespace Marwa\Support\Tests;

use Marwa\Support\Sanitizer;
use PHPUnit\Framework\TestCase;

class SanitizerTest extends TestCase
{
    public function testFilenameRemovesUnsafeCharactersAndEmptyNames(): void
    {
        $this->assertSame('report_final.pdf', Sanitizer::filename('report final?.pdf'));
        $this->assertSame('file', Sanitizer::filename('$$$'));
    }

    public function testCleanHandlesNullAndNestedArrays(): void
    {
        $cleaned = Sanitizer::clean(['<b>x</b>', null], 'string');

        $this->assertSame(['&lt;b&gt;x&lt;/b&gt;', null], $cleaned);
        $this->assertNull(Sanitizer::clean(null));
    }

    public function testUrlSanitizerRejectsUnsafeSchemes(): void
    {
        $this->assertSame('', Sanitizer::clean('javascript:alert(1)', 'url'));
        $this->assertSame('', Sanitizer::clean('data:text/html,<script>alert(1)</script>', 'url'));
    }

    public function testUrlSanitizerAllowsHttpAndRelativeUrls(): void
    {
        $this->assertSame('https://example.com/path', Sanitizer::clean('https://example.com/path', 'url'));
        $this->assertSame('/account/settings', Sanitizer::clean('/account/settings', 'url'));
    }
}
