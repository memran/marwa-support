<?php

declare(strict_types=1);

namespace Marwa\Support\Tests;

use InvalidArgumentException;
use Marwa\Support\Html;
use PHPUnit\Framework\TestCase;

class HtmlTest extends TestCase
{
    public function testElementEscapesContentByDefault(): void
    {
        $html = Html::element('div', [], '<img src=x onerror=alert(1)>');

        $this->assertSame('<div>&lt;img src=x onerror=alert(1)&gt;</div>', $html);
    }

    public function testRawElementAllowsTrustedHtmlContent(): void
    {
        $html = Html::rawElement('div', [], '<span>trusted</span>');

        $this->assertSame('<div><span>trusted</span></div>', $html);
    }

    public function testAttributesRejectInvalidNames(): void
    {
        $this->expectException(InvalidArgumentException::class);

        Html::element('div', ['x" onclick="' => 'alert(1)']);
    }

    public function testAttributesRejectEventHandlers(): void
    {
        $this->expectException(InvalidArgumentException::class);

        Html::element('button', ['onclick' => 'alert(1)'], 'Click');
    }

    public function testUrlAttributesRejectUnsafeSchemes(): void
    {
        $this->expectException(InvalidArgumentException::class);

        Html::link('javascript:alert(1)', 'Bad');
    }

    public function testUrlAttributesRejectWhitespaceBeforeSchemes(): void
    {
        $this->expectException(InvalidArgumentException::class);

        Html::link(' javascript:alert(1)', 'Bad');
    }

    public function testDocumentEscapesTitleAndBodyButKeepsDocumentMarkup(): void
    {
        $html = Html::document('<bad>', '<script>alert(1)</script>');

        $this->assertStringContainsString('<html lang="en">', $html);
        $this->assertStringContainsString('<title>&lt;bad&gt;</title>', $html);
        $this->assertStringContainsString('<body>&lt;script&gt;alert(1)&lt;/script&gt;</body>', $html);
    }
}
