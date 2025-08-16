<?php
declare(strict_types=1);

namespace Marwa\Support\Tests;

use Marwa\Support\Str;
use PHPUnit\Framework\TestCase;

class StrTest extends TestCase
{
    public function testSlug()
    {
        $this->assertEquals('hello-world', Str::slug('Hello World!'));
        $this->assertEquals('hello_world', Str::slug('Hello World!', '_'));
    }

    public function testLimit()
    {
        $this->assertEquals('Hello...', Str::limit('Hello World', 5));
        $this->assertEquals('Hello~~~', Str::limit('Hello World', 5, '~~~'));
    }

    public function testRandom()
    {
        $this->assertEquals(16, strlen(Str::random()));
        $this->assertEquals(32, strlen(Str::random(32)));
    }

    public function testContains()
    {
        $this->assertTrue(Str::contains('Hello World', 'World'));
        $this->assertFalse(Str::contains('Hello World', 'world'));
    }

    public function testCamel()
    {
        $this->assertEquals('helloWorld', Str::camel('hello_world'));
    }

    public function testStudly()
    {
        $this->assertEquals('HelloWorld', Str::studly('hello_world'));
    }
}