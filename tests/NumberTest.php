<?php

declare(strict_types=1);

namespace Marwa\Support\Tests;

use Marwa\Support\Number;
use PHPUnit\Framework\TestCase;

class NumberTest extends TestCase
{
    public function testFormat()
    {
        $this->assertEquals('1,234', Number::format(1234));
        $this->assertEquals('1,234.57', Number::format(1234.567, 2));
    }

    public function testCurrency()
    {
        $result = Number::currency(1234.56);
        $this->assertStringContainsString('1,234.56', $result);
    }

    public function testPercentage()
    {
        $this->assertEquals('50%', Number::percentage(0.5));
        $this->assertEquals('33.3%', Number::percentage(0.333, 1));
    }

    public function testOrdinal()
    {
        $this->assertEquals('1st', Number::ordinal(1));
        $this->assertEquals('2nd', Number::ordinal(2));
        $this->assertEquals('3rd', Number::ordinal(3));
        $this->assertEquals('4th', Number::ordinal(4));
    }

    public function testCompact()
    {
        $this->assertEquals('1.5K', Number::compact(1500));
        $this->assertEquals('2M', Number::compact(2000000));
        $this->assertEquals('1B', Number::compact(1000000000));
    }

    public function testBytes()
    {
        $this->assertEquals('1 KB', Number::bytes(1024));
        $this->assertEquals('1.5 MB', Number::bytes(1572864, 1));
    }

    public function testBytesThrowsOnNegative()
    {
        $this->expectException(\InvalidArgumentException::class);
        Number::bytes(-1);
    }

    public function testRound()
    {
        $this->assertEquals(1.23, Number::round(1.234, 2));
        $this->assertEquals(1.0, Number::round(1.234));
    }

    public function testCeil()
    {
        $this->assertEquals(5, Number::ceil(4.2));
    }

    public function testFloor()
    {
        $this->assertEquals(4, Number::floor(4.8));
    }

    public function testClamp()
    {
        $this->assertEquals(5, Number::clamp(10, 0, 5));
        $this->assertEquals(3, Number::clamp(3, 0, 5));
        $this->assertEquals(0, Number::clamp(-1, 0, 5));
    }

    public function testBetween()
    {
        $this->assertTrue(Number::between(3, 1, 5));
        $this->assertFalse(Number::between(6, 1, 5));
    }

    public function testRoman()
    {
        $this->assertEquals('I', Number::roman(1));
        $this->assertEquals('IV', Number::roman(4));
        $this->assertEquals('IX', Number::roman(9));
        $this->assertEquals('MCMXCIV', Number::roman(1994));
    }

    public function testRomanThrowsOnInvalidValue()
    {
        $this->expectException(\InvalidArgumentException::class);
        Number::roman(0);

        $this->expectException(\InvalidArgumentException::class);
        Number::roman(4000);
    }
}
