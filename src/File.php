<?php declare(strict_types=1);

namespace Marwa\Support;

class File
{
    /**
     * Get the contents of a file.
     *
     * @param string $path
     * @return string|false
     */
    public static function get(string $path)
    {
        return file_get_contents($path);
    }

    /**
     * Write the contents of a file.
     *
     * @param string $path
     * @param string $contents
     * @return int|false
     */
    public static function put(string $path, string $contents)
    {
        return file_put_contents($path, $contents);
    }

    /**
     * Append to a file.
     *
     * @param string $path
     * @param string $contents
     * @return int|false
     */
    public static function append(string $path, string $contents)
    {
        return file_put_contents($path, $contents, FILE_APPEND);
    }

    /**
     * Delete a file.
     *
     * @param string $path
     * @return bool
     */
    public static function delete(string $path): bool
    {
        return unlink($path);
    }

    /**
     * Check if a file exists.
     *
     * @param string $path
     * @return bool
     */
    public static function exists(string $path): bool
    {
        return file_exists($path);
    }

    /**
     * Get the file extension.
     *
     * @param string $path
     * @return string
     */
    public static function extension(string $path): string
    {
        return pathinfo($path, PATHINFO_EXTENSION);
    }

    /**
     * Get the file name without extension.
     *
     * @param string $path
     * @return string
     */
    public static function name(string $path): string
    {
        return pathinfo($path, PATHINFO_FILENAME);
    }

    /**
     * Get the file size in bytes.
     *
     * @param string $path
     * @return int|false
     */
    public static function size(string $path)
    {
        return filesize($path);
    }

    /**
     * Get the file's last modification time.
     *
     * @param string $path
     * @return int|false
     */
    public static function lastModified(string $path)
    {
        return filemtime($path);
    }

    /**
     * Create a directory.
     *
     * @param string $path
     * @param int $mode
     * @param bool $recursive
     * @return bool
     */
    public static function makeDirectory(string $path, int $mode = 0755, bool $recursive = false): bool
    {
        return mkdir($path, $mode, $recursive);
    }
}