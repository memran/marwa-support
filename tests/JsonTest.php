<?php

declare(strict_types=1);

namespace Marwa\Support\Tests;

use Marwa\Support\Json;
use PHPUnit\Framework\TestCase;

class JsonTest extends TestCase
{
    public function testEncode()
    {
        $result = Json::encode(['foo' => 'bar']);
        $this->assertEquals('{"foo":"bar"}', $result);
    }

    public function testDecode()
    {
        $result = Json::decode('{"foo":"bar"}');
        $this->assertEquals(['foo' => 'bar'], $result);
    }

    public function testDecodeAsObject()
    {
        $result = Json::decode('{"foo":"bar"}', false);
        $this->assertIsObject($result);
    }

    public function testIsValid()
    {
        $this->assertTrue(Json::isValid('{"foo":"bar"}'));
        $this->assertFalse(Json::isValid('not json'));
    }

    public function testPretty()
    {
        $result = Json::pretty(['foo' => 'bar']);
        $this->assertStringContainsString('"foo"', $result);
        $this->assertStringContainsString("\n", $result);
    }

    public function testMinify()
    {
        $json = '{"foo":"bar","baz":"qux"}';
        $result = Json::minify($json);
        $this->assertEquals('{"foo":"bar","baz":"qux"}', $result);
    }

    public function testMinifyThrowsOnInvalidJson()
    {
        $this->expectException(\InvalidArgumentException::class);
        Json::minify('not json');
    }

    public function testGet()
    {
        $json = '{"user":{"name":"John","email":"john@example.com"}}';
        $this->assertEquals('John', Json::get($json, 'user.name'));
        $this->assertEquals('default', Json::get($json, 'user.phone', 'default'));
    }

    public function testHas()
    {
        $json = '{"user":{"name":"John"}}';
        $this->assertTrue(Json::has($json, 'user.name'));
        $this->assertFalse(Json::has($json, 'user.phone'));
    }

    public function testFromArray()
    {
        $result = Json::fromArray(['foo' => 'bar']);
        $this->assertEquals('{"foo":"bar"}', $result);
    }

    public function testToArray()
    {
        $result = Json::toArray('{"foo":"bar"}');
        $this->assertEquals(['foo' => 'bar'], $result);
    }
}
