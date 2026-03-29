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
}
