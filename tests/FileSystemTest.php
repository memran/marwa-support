<?php
declare(strict_types=1);

namespace Marwa\Support\Tests;

use Marwa\Support\File as FileSystem;
use PHPUnit\Framework\TestCase;
use RuntimeException;
use InvalidArgumentException;

class FileSystemTest extends TestCase
{
    private $testDir = __DIR__ . '/test-dir';
    private $testFile = __DIR__ . '/test-dir/testfile.txt';
    private $tempFile = __DIR__ . '/test-dir/tempfile.tmp';

    protected function setUp(): void
    {
        $this->cleanup();
        
    }

    protected function tearDown(): void
    {
        $this->cleanup();
    }
   
    private function cleanup(): void
    {
        if (file_exists($this->testFile)) {
            unlink($this->testFile);
        }
        if (file_exists($this->tempFile)) {
            unlink($this->tempFile);
        }
        if (is_dir($this->testDir)) {
            rmdir($this->testDir);
        }
    }

    protected function makeDirectoryIfNotExits()
    {
         // First create the test directory if it doesn't exist
        if (!is_dir($this->testDir)) {
            mkdir($this->testDir, 0755, true);
        }

    }
    public function testPutCreatesFileAndDirectory()
    {
        $this->assertDirectoryDoesNotExist($this->testDir);
        
        $bytes = FileSystem::put($this->testFile, 'Hello World');
        $this->assertEquals(11, $bytes);
        
        $this->assertFileExists($this->testFile);
        $this->assertEquals('Hello World', file_get_contents($this->testFile));
    }

    public function testPutWithArrayConvertsToJson()
    {
        $data = ['name' => 'John', 'age' => 30];
        FileSystem::put($this->testFile, $data);
        
        $this->assertJsonStringEqualsJsonString(
            json_encode($data),
            file_get_contents($this->testFile)
        );
    }

    public function testPutWithInvalidPathThrowsException()
    {
        $this->expectException(InvalidArgumentException::class);
        FileSystem::put("invalid\0path.txt", 'content');
    }

    public function testPutWithoutDirectoryCreationThrows()
    {
        $this->expectException(RuntimeException::class);
        FileSystem::put($this->testFile, 'content', 0, 0755, false);
    }

    public function testAppendToExistingFile()
    {
        FileSystem::put($this->testFile, 'Hello');
        FileSystem::append($this->testFile, ' World');
        
        $this->assertEquals('Hello World', file_get_contents($this->testFile));
    }

    public function testAppendCreatesFileIfNotExists()
    {
        $this->assertFileDoesNotExist($this->testFile);
        FileSystem::append($this->testFile, 'content');
        $this->assertFileExists($this->testFile);
    }
    public function testGetNonExistentFileThrows()
    {
        $this->expectException(RuntimeException::class);
        FileSystem::get($this->testFile);
    }


    public function testGetFileContents()
    {
        // First create the test directory if it doesn't exist
        if (!is_dir($this->testDir)) {
            mkdir($this->testDir, 0755, true);
        }
        
        // Create the test file with known content
        file_put_contents($this->testFile, 'Test content');
        
        // Verify the file was created
        $this->assertFileExists($this->testFile);
        
        // Test the get() method
        $this->assertEquals('Test content', FileSystem::get($this->testFile));
    }

    public function testDeleteExistingFile()
    {
        $this->makeDirectoryIfNotExits();
        file_put_contents($this->testFile, '');
        $this->assertTrue(FileSystem::delete($this->testFile));
        $this->assertFileDoesNotExist($this->testFile);
    }

    public function testDeleteNonExistentFileReturnsFalse()
    {
        $this->assertFalse(FileSystem::delete($this->testFile));
    }

    public function testMakeDirectory()
    {
        $this->assertDirectoryDoesNotExist($this->testDir);
        $this->assertTrue(FileSystem::makeDirectory($this->testDir));
        $this->assertDirectoryExists($this->testDir);
    }

    public function testMakeExistingDirectoryReturnsTrue()
    {
        mkdir($this->testDir);
        $this->assertTrue(FileSystem::makeDirectory($this->testDir));
    }

    public function testFileSize()
    {
        $this->makeDirectoryIfNotExits();
        file_put_contents($this->testFile, '12345');
        $this->assertEquals(5, FileSystem::size($this->testFile));
    }

    public function testLastModified()
    {
        $this->makeDirectoryIfNotExits();
        file_put_contents($this->testFile, 'test');
        $expected = filemtime($this->testFile);
        $this->assertEquals($expected, FileSystem::lastModified($this->testFile));
    }

    public function testExists()
    {
        $this->makeDirectoryIfNotExits();
        $this->assertFalse(FileSystem::exists($this->testFile));
        file_put_contents($this->testFile, '');
        $this->assertTrue(FileSystem::exists($this->testFile));
    }

    public function testCopyFile()
    {
        $this->makeDirectoryIfNotExits();
        file_put_contents($this->testFile, 'source');
        $this->assertTrue(FileSystem::copy($this->testFile, $this->tempFile));
        $this->assertFileEquals($this->testFile, $this->tempFile);
    }

    public function testCopyToExistingWithoutOverwriteThrows()
    {
        file_put_contents($this->testFile, 'source');
        file_put_contents($this->tempFile, 'destination');
        
        $this->expectException(RuntimeException::class);
        FileSystem::copy($this->testFile, $this->tempFile);
    }

    public function testMoveFile()
    {
        $this->makeDirectoryIfNotExits();
        file_put_contents($this->testFile, 'content');
        $this->assertTrue(FileSystem::move($this->testFile, $this->tempFile));
        $this->assertFileDoesNotExist($this->testFile);
        $this->assertFileExists($this->tempFile);
    }

    public function testMoveNonExistentFileThrows()
    {
        $this->expectException(RuntimeException::class);
        FileSystem::move($this->testFile, $this->tempFile);
    }

    public function testPutWithCustomPermissions()
    {
        $this->makeDirectoryIfNotExits();
        FileSystem::put($this->testFile, 'test', 0, 0644);
        $this->assertEquals('0644', substr(sprintf('%o', fileperms($this->testFile)), -4));
    }

    public function testPutAtomicOperation()
    {
        FileSystem::put($this->testFile, 'test');
        $this->assertFileExists($this->testFile);
        $this->assertStringEqualsFile($this->testFile, 'test');
    }
}