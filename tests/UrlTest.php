<?php

declare(strict_types=1);

namespace Marwa\Support\Tests;

use Marwa\Support\Url;
use PHPUnit\Framework\TestCase;

class UrlTest extends TestCase
{
    public function testParse()
    {
        $result = Url::parse('https://example.com:8080/path?query=value#fragment');
        $this->assertEquals('https', $result['scheme']);
        $this->assertEquals('example.com', $result['host']);
        $this->assertEquals(8080, $result['port']);
        $this->assertEquals('/path', $result['path']);
        $this->assertEquals('query=value', $result['query']);
        $this->assertEquals('fragment', $result['fragment']);
    }

    public function testParseThrowsOnInvalid()
    {
        $this->expectException(\InvalidArgumentException::class);
        Url::parse('not a url');
    }

    public function testBuild()
    {
        $components = [
            'scheme' => 'https',
            'host' => 'example.com',
            'path' => '/path',
            'query' => 'foo=bar',
        ];
        $this->assertEquals('https://example.com/path?foo=bar', Url::build($components));
    }

    public function testQuery()
    {
        $result = Url::query('https://example.com?foo=bar&baz=qux');
        $this->assertEquals(['foo' => 'bar', 'baz' => 'qux'], $result);
    }

    public function testQueryEmpty()
    {
        $result = Url::query('https://example.com');
        $this->assertEquals([], $result);
    }

    public function testWithQuery()
    {
        $result = Url::withQuery('https://example.com?foo=bar', ['baz' => 'qux']);
        $this->assertStringContainsString('foo=bar', $result);
        $this->assertStringContainsString('baz=qux', $result);
    }

    public function testGetQuery()
    {
        $this->assertEquals('value', Url::getQuery('https://example.com?key=value', 'key'));
        $this->assertEquals('default', Url::getQuery('https://example.com', 'key', 'default'));
    }

    public function testWithoutQuery()
    {
        $result = Url::withoutQuery('https://example.com?foo=bar&baz=qux', 'foo');
        $this->assertStringContainsString('baz=qux', $result);
        $this->assertStringNotContainsString('foo=bar', $result);
    }

    public function testScheme()
    {
        $this->assertEquals('https', Url::scheme('https://example.com'));
        $this->assertNull(Url::scheme('/path'));
    }

    public function testHost()
    {
        $this->assertEquals('example.com', Url::host('https://example.com/path'));
    }

    public function testPort()
    {
        $this->assertEquals(8080, Url::port('https://example.com:8080'));
        $this->assertNull(Url::port('https://example.com'));
    }

    public function testPath()
    {
        $this->assertEquals('/path/to', Url::path('https://example.com/path/to'));
    }

    public function testFragment()
    {
        $this->assertEquals('section', Url::fragment('https://example.com#section'));
    }

    public function testDomain()
    {
        $this->assertEquals('example.com', Url::domain('https://www.example.com/path'));
    }

    public function testIsAbsolute()
    {
        $this->assertTrue(Url::isAbsolute('https://example.com'));
        $this->assertFalse(Url::isAbsolute('/path'));
    }

    public function testFullDomain()
    {
        $this->assertEquals('https://example.com', Url::fullDomain('https://example.com/path'));
        $this->assertEquals('https://example.com:8080', Url::fullDomain('https://example.com:8080/path'));
    }
}
