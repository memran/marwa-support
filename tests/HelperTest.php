<?php
declare(strict_types=1);

namespace Marwa\Support\Tests;

use Marwa\Support\Helper;
use PHPUnit\Framework\TestCase;

class HelperTest extends TestCase
{
    // tap() tests
    public function testTapModifiesValueWithoutChangingIt()
    {
        $object = new \stdClass();
        $object->name = 'Original';
        
        $result = Helper::tap($object, function($obj) {
            $obj->name = 'Modified';
        });
        
        $this->assertEquals('Modified', $object->name);
        $this->assertSame($object, $result);
    }

    // pipe() tests
    public function testPipeProcessesThroughCallbacks()
    {
        $result = Helper::pipe(' hello ', [
            fn($s) => trim($s),
            fn($s) => strtoupper($s),
            fn($s) => $s . '!'
        ]);
        
        $this->assertEquals('HELLO!', $result);
    }

    // with() tests
    public function testWithReturnsCallbackResult()
    {
        $result = Helper::with(['a' => 1], fn($a) => $a['a']);
        $this->assertEquals(1, $result);
    }

    public function testWithReturnsDefaultWhenNull()
    {
        $result = Helper::with(null, fn($v) => $v, 'default');
        $this->assertEquals('default', $result);
    }

    // dd() tests
    public function testDdExits()
    {
        $this->expectOutputString("int(1)\n");
        $this->expectExceptionCode(1);
        
        Helper::dd(1);
    }

    // retry() tests
    public function testRetrySucceedsAfterFailures()
    {
        $counter = 0;
        $result = Helper::retry(3, function() use (&$counter) {
            if (++$counter < 2) {
                throw new \RuntimeException('Fail');
            }
            return 'success';
        });
        
        $this->assertEquals('success', $result);
        $this->assertEquals(2, $counter);
    }

    public function testRetryFailsAfterMaxAttempts()
    {
        $this->expectException(\RuntimeException::class);
        
        Helper::retry(2, function() {
            throw new \RuntimeException('Always fails');
        });
    }

    // dataGet() tests
    public function testDataGetWithDotNotation()
    {
        $data = ['user' => ['name' => 'John']];
        $this->assertEquals('John', Helper::dataGet($data, 'user.name'));
    }

    public function testDataGetReturnsDefaultForMissingKey()
    {
        $this->assertEquals('default', Helper::dataGet([], 'missing.key', 'default'));
    }

    // value() tests
    public function testValueReturnsClosureResult()
    {
        $result = Helper::value(fn() => 'closure');
        $this->assertEquals('closure', $result);
    }

    public function testValueReturnsNonClosureAsIs()
    {
        $this->assertEquals('direct', Helper::value('direct'));
    }

    // New functions tests
    public function testGroupByCreatesCorrectGroups()
    {
        $data = [
            ['type' => 'A', 'value' => 1],
            ['type' => 'B', 'value' => 2],
            ['type' => 'A', 'value' => 3]
        ];
        
        $grouped = Helper::groupBy($data, 'type');
        
        $this->assertCount(2, $grouped['A']);
        $this->assertCount(1, $grouped['B']);
    }

    public function testKeyByCreatesCorrectKeys()
    {
        $data = [
            ['id' => 1, 'name' => 'A'],
            ['id' => 2, 'name' => 'B']
        ];
        
        $keyed = Helper::keyBy($data, 'id');
        
        $this->assertEquals('A', $keyed[1]['name']);
        $this->assertEquals('B', $keyed[2]['name']);
    }

    public function testMemoizeCachesResults()
    {
        $counter = 0;
        $memoized = Helper::memoize(function($x) use (&$counter) {
            $counter++;
            return $x * 2;
        });
        
        $this->assertEquals(4, $memoized(2));
        $this->assertEquals(4, $memoized(2));
        $this->assertEquals(1, $counter);
    }

    public function testCurryPartiallyApplies()
    {
        $add = fn($a, $b, $c) => $a + $b + $c;
        $curried = Helper::curry($add);
        
        $this->assertEquals(6, $curried(1)(2)(3));
    }

    public function testEmptyChecksStrictly()
    {
        $this->assertTrue(Helper::empty(''));
        $this->assertFalse(Helper::empty(0));
        $this->assertFalse(Helper::empty(false));
    }

    public function testTypeOfReturnsDetailedTypes()
    {
        $this->assertEquals('callable', Helper::typeOf(fn() => null));
        $this->assertEquals('stdClass', Helper::typeOf(new \stdClass()));
        $this->assertEquals('string', Helper::typeOf('test'));
    }

    public function testUuidGeneratesValidFormat()
    {
        $uuid = Helper::uuid();
        $this->assertMatchesRegularExpression(
            '/^[0-9a-f]{8}-[0-9a-f]{4}-4[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/i',
            $uuid
        );
    }

    public function testTruncateHandlesUnicode()
    {
        $text = 'こんにちは世界';
        $this->assertEquals('こんにちは...', Helper::truncate($text, 5));
    }

    public function testPercentageCalculation()
    {
        $this->assertEquals(50.0, Helper::percentage(50, 100));
        $this->assertEquals(33.33, Helper::percentage(1, 3));
    }

    public function testMapRangeConvertsCorrectly()
    {
        $this->assertEquals(50, Helper::mapRange(5, 0, 10, 0, 100));
        $this->assertEquals(-50, Helper::mapRange(5, 10, 0, 0, -100));
    }

    public function testMeasureReturnsExecutionTime()
    {
        $time = Helper::measure(function() {
            usleep(100000); // 100ms
        });
        
        $this->assertGreaterThanOrEqual(100, $time);
        $this->assertLessThan(200, $time);
    }

    public function testMemoryUsageFormatsCorrectly()
    {
        $this->assertMatchesRegularExpression('/^\d+\.\d{2} (B|KB|MB|GB|TB)$/', Helper::memoryUsage(1024));
    }
}