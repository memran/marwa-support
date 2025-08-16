<?php
declare(strict_types=1);

namespace Marwa\Support\Tests;

use Marwa\Support\Obj;
use PHPUnit\Framework\TestCase;

class ObjTest extends TestCase
{
    public function testToArray()
    {
        $object = new \stdClass();
        $object->name = 'John';
        $this->assertEquals(['name' => 'John'], Obj::toArray($object));
    }

    public function testGet()
    {
        $object = new \stdClass();
        $object->user = new \stdClass();
        $object->user->name = 'John';
        $this->assertEquals('John', Obj::get($object, 'user.name'));
    }

    public function testHas()
    {
        $object = new \stdClass();
        $object->name = 'John';
        $this->assertTrue(Obj::has($object, 'name'));
        $this->assertFalse(Obj::has($object, 'age'));
    }
}