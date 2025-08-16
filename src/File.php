<?php
declare(strict_types=1);

namespace Marwa\Support;

use RuntimeException;
use InvalidArgumentException;

class File
{
    /**
     * Write file contents with atomic operation and directory creation
     */
    public static function put(
        string $path,
        $contents,
        int $flags = 0,
        int $mode = 0755,
        bool $createDir = true
    ): int {
        self::validatePath($path);
        
        if ($createDir) {
            self::ensureDirectoryExists(dirname($path), $mode);
        }

        if (is_array($contents) || is_object($contents)) {
            $contents = json_encode($contents, JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR);
        }

        $tempPath = $path . '.' . uniqid('', true) . '.tmp';
        $bytes = file_put_contents($tempPath, (string)$contents, $flags | LOCK_EX);
        
        if ($bytes === false || !rename($tempPath, $path)) {
            throw new RuntimeException("Failed to write file: {$path}");
        }

        chmod($path, $mode);
        return $bytes;
    }

    /**
     * Safely append to a file
     */
    public static function append(string $path, string $contents, int $mode = 0755): int
    {
        self::validatePath($path);
        self::ensureFileExists($path, $mode);
        
        $bytes = file_put_contents($path, $contents, FILE_APPEND | LOCK_EX);
        if ($bytes === false) {
            throw new RuntimeException("Failed to append to file: {$path}");
        }
        
        return $bytes;
    }

    /**
     * Read file contents with validation
     */
    public static function get(string $path): string
    {
        self::validatePath($path);
        self::validateFileExists($path);
        
        $contents = file_get_contents($path);
        if ($contents === false) {
            throw new RuntimeException("Failed to read file: {$path}");
        }
        
        return $contents;
    }

    /**
     * Delete file with existence check
     */
    public static function delete(string $path): bool
    {
        self::validatePath($path);
        
        if (!file_exists($path)) {
            return false;
        }
        
        if (!unlink($path)) {
            throw new RuntimeException("Failed to delete file: {$path}");
        }
        
        return true;
    }

    /**
     * Create directory with recursive option
     */
    public static function makeDirectory(string $path, int $mode = 0755, bool $recursive = true): bool
    {
        self::validatePath($path);
        
        if (is_dir($path)) {
            return true;
        }
        
        if (!mkdir($path, $mode, $recursive)) {
            throw new RuntimeException("Failed to create directory: {$path}");
        }
        
        return true;
    }

    /**
     * Get file size with validation
     */
    public static function size(string $path): int
    {
        self::validatePath($path);
        self::validateFileExists($path);
        
        $size = filesize($path);
        if ($size === false) {
            throw new RuntimeException("Failed to get file size: {$path}");
        }
        
        return $size;
    }

    /**
     * Get file modification time
     */
    public static function lastModified(string $path): int
    {
        self::validatePath($path);
        self::validateFileExists($path);
        
        $time = filemtime($path);
        if ($time === false) {
            throw new RuntimeException("Failed to get modification time: {$path}");
        }
        
        return $time;
    }

    /**
     * Check if path exists
     */
    public static function exists(string $path): bool
    {
        self::validatePath($path);
        return file_exists($path);
    }

    /**
     * Copy file with overwrite protection
     */
    public static function copy(string $source, string $destination, bool $overwrite = false): bool
    {
        self::validatePath($source);
        self::validatePath($destination);
        self::validateFileExists($source);
        
        if (!$overwrite && file_exists($destination)) {
            throw new RuntimeException("Destination file exists: {$destination}");
        }
        
        self::ensureDirectoryExists(dirname($destination));
        
        if (!copy($source, $destination)) {
            throw new RuntimeException("Failed to copy file from {$source} to {$destination}");
        }
        
        return true;
    }

    /**
     * Move file with atomic operation
     */
    public static function move(string $source, string $destination): bool
    {
        self::validatePath($source);
        self::validatePath($destination);
        self::validateFileExists($source);
        
        self::ensureDirectoryExists(dirname($destination));
        
        if (!rename($source, $destination)) {
            throw new RuntimeException("Failed to move file from {$source} to {$destination}");
        }
        
        return true;
    }

    private static function validatePath(string $path): void
    {
        if ($path === '') {
            throw new InvalidArgumentException('Path cannot be empty');
        }
        
        if (strpos($path, "\0") !== false) {
            throw new InvalidArgumentException('Path contains null bytes');
        }
    }

    private static function validateFileExists(string $path): void
    {
        if (!file_exists($path)) {
            throw new RuntimeException("File does not exist: {$path}");
        }
        
        if (!is_file($path)) {
            throw new RuntimeException("Path is not a file: {$path}");
        }
    }

    private static function ensureFileExists(string $path, int $mode = 0755): void
    {
        if (!file_exists($path)) {
            self::put($path, '', 0, $mode);
        }
    }

    private static function ensureDirectoryExists(string $path, int $mode = 0755): void
    {
        if (!is_dir($path) && !self::makeDirectory($path, $mode, true)) {
            throw new RuntimeException("Failed to create directory: {$path}");
        }
    }
}