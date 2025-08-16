<?php
declare(strict_types=1);

namespace Marwa\Support\Tests;

use Marwa\Support\Arr;
use PHPUnit\Framework\TestCase;

class ArrTest extends TestCase
{
    public function testGet()
    {
        $array = ['user' => ['name' => 'John']];
        $this->assertEquals('John', Arr::get($array, 'user.name'));
        $this->assertEquals('default', Arr::get($array, 'user.age', 'default'));
    }

    public function testSet()
    {
        $array = [];
        Arr::set($array, 'user.name', 'John');
        $this->assertEquals(['user' => ['name' => 'John']], $array);
    }

    public function testHas()
    {
        $array = ['user' => ['name' => 'John']];
        $this->assertTrue(Arr::has($array, 'user.name'));
        $this->assertFalse(Arr::has($array, 'user.age'));
    }

    public function testFirst()
    {
        $array = [100, 200, 300];
        $this->assertEquals(100, Arr::first($array));
    }

    public function testWhere()
    {
        $array = ['a' => 1, 'b' => 2, 'c' => 3];
        $filtered = Arr::where($array, fn($value) => $value > 1);
        $this->assertEquals(['b' => 2, 'c' => 3], $filtered);
    }
}