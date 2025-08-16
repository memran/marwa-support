<?php
declare(strict_types=1);

namespace Marwa\Support\Tests;

use Marwa\Support\Date;
use PHPUnit\Framework\TestCase;

class DateTest extends TestCase
{
    public function testFormat()
    {
        $this->assertEquals('2023-01-01', Date::format('2023-01-01', 'Y-m-d'));
    }

    public function testAddDays()
    {
        $date = Date::addDays('2023-01-01', 1);
        $this->assertEquals('2023-01-02', $date->format('Y-m-d'));
    }

    public function testDiffInDays()
    {
        $days = Date::diffInDays('2023-01-01', '2023-01-03');
        $this->assertEquals(2, $days);
    }

    public function testIsAfter()
    {
        $this->assertTrue(Date::isAfter('2023-01-02', '2023-01-01'));
        $this->assertFalse(Date::isAfter('2023-01-01', '2023-01-02'));
    }
}