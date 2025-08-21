<?php
declare(strict_types=1);

namespace Marwa\Support;

use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use SplFileInfo;

class Finder
{
    protected array $paths = [];
    protected array $filters = [];

    public static function in(string|array $paths): self
    {
        $finder = new static();
        $finder->paths = is_array($paths) ? $paths : [$paths];
        return $finder;
    }

    public function files(): Collection
    {
        return $this->find()->filter(fn($file) => $file->isFile());
    }

    public function directories(): Collection
    {
        return $this->find()->filter(fn($file) => $file->isDir());
    }

    public function name(string $pattern): self
    {
        $this->filters[] = fn($file) => preg_match("/{$pattern}/", $file->getFilename());
        return $this;
    }

    public function size(string $operator, int $size): self
    {
        $this->filters[] = fn($file) => match($operator) {
            '>' => $file->getSize() > $size,
            '>=' => $file->getSize() >= $size,
            '<' => $file->getSize() < $size,
            '<=' => $file->getSize() <= $size,
            '=' => $file->getSize() === $size,
            default => false
        };
        return $this;
    }

    protected function find(): Collection
    {
        $results = new Collection();

        foreach ($this->paths as $path) {
            if (!is_dir($path)) continue;

            $iterator = new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator($path, RecursiveDirectoryIterator::SKIP_DOTS),
                RecursiveIteratorIterator::SELF_FIRST
            );

            foreach ($iterator as $file) {
                if ($this->passesFilters($file)) {
                    $results->push($file);
                }
            }
        }

        return $results;
    }

    protected function passesFilters(SplFileInfo $file): bool
    {
        foreach ($this->filters as $filter) {
            if (!$filter($file)) {
                return false;
            }
        }
        return true;
    }
}