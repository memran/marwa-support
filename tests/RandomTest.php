<?php

declare(strict_types=1);

namespace Marwa\Support\Tests;

use Marwa\Support\Random;
use PHPUnit\Framework\TestCase;
use RuntimeException;

class RandomTest extends TestCase
{
    public function testStringSupportsNamedCharsets(): void
    {
        $this->assertMatchesRegularExpression('/^[a-f0-9]{32}$/', Random::string(32, 'hex'));
        $this->assertMatchesRegularExpression('/^[0-9]{12}$/', Random::string(12, 'numeric'));
        $this->assertMatchesRegularExpression('/^[A-Za-z0-9_-]{24}$/', Random::string(24, 'base64url'));
    }

    public function testStringRejectsUnsupportedCharset(): void
    {
        $this->expectException(RuntimeException::class);
        Random::string(8, 'unsupported');
    }
}
