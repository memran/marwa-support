<?php
declare(strict_types=1);

namespace Marwa\Support;

use ArrayIterator;
use Countable;
use IteratorAggregate;
use JsonSerializable;

class Collection implements Countable, IteratorAggregate, JsonSerializable
{
    protected array $items = [];

    public function __construct(array $items = [])
    {
        $this->items = $items;
    }

    public static function make(array $items = []): self
    {
        return new static($items);
    }

    public function all(): array
    {
        return $this->items;
    }

    public function get($key, $default = null)
    {
        return $this->items[$key] ?? $default;
    }

    public function put($key, $value): self
    {
        $this->items[$key] = $value;
        return $this;
    }

    public function push($value): self
    {
        $this->items[] = $value;
        return $this;
    }

    public function first(callable $callback = null, $default = null)
    {
        if (is_null($callback)) {
            return $this->items[0] ?? $default;
        }

        foreach ($this->items as $item) {
            if ($callback($item)) {
                return $item;
            }
        }

        return $default;
    }

    public function last(callable $callback = null, $default = null)
    {
        if (is_null($callback)) {
            return empty($this->items) ? $default : end($this->items);
        }

        $result = $default;
        foreach ($this->items as $item) {
            if ($callback($item)) {
                $result = $item;
            }
        }

        return $result;
    }

    public function filter(callable $callback = null): self
    {
        if ($callback) {
            return new static(array_filter($this->items, $callback, ARRAY_FILTER_USE_BOTH));
        }

        return new static(array_filter($this->items));
    }

    public function map(callable $callback): self
    {
        return new static(array_map($callback, $this->items, array_keys($this->items)));
    }

    public function each(callable $callback): self
    {
        foreach ($this->items as $key => $item) {
            if ($callback($item, $key) === false) {
                break;
            }
        }

        return $this;
    }

    public function pluck(string $value, string $key = null): self
    {
        $results = [];

        foreach ($this->items as $item) {
            $itemValue = is_object($item) ? $item->{$value} : $item[$value];

            if (is_null($key)) {
                $results[] = $itemValue;
            } else {
                $itemKey = is_object($item) ? $item->{$key} : $item[$key];
                $results[$itemKey] = $itemValue;
            }
        }

        return new static($results);
    }

    public function keys(): self
    {
        return new static(array_keys($this->items));
    }

    public function values(): self
    {
        return new static(array_values($this->items));
    }

    public function sortBy(callable $callback, bool $descending = false): self
    {
        $results = $this->items;

        uasort($results, function ($a, $b) use ($callback, $descending) {
            $aValue = $callback($a);
            $bValue = $callback($b);

            return $descending ? $bValue <=> $aValue : $aValue <=> $bValue;
        });

        return new static($results);
    }

    public function groupBy($groupBy): self
    {
        $results = [];

        foreach ($this->items as $key => $value) {
            $groupKey = is_callable($groupBy) ? $groupBy($value, $key) : 
                       (is_object($value) ? $value->{$groupBy} : $value[$groupBy]);

            $results[$groupKey][] = $value;
        }

        return new static($results);
    }

    public function count(): int
    {
        return count($this->items);
    }

    public function isEmpty(): bool
    {
        return empty($this->items);
    }

    public function isNotEmpty(): bool
    {
        return !empty($this->items);
    }

    public function toArray(): array
    {
        return array_map(function ($value) {
            return $value instanceof self ? $value->toArray() : $value;
        }, $this->items);
    }

    public function toJson(int $options = 0): string
    {
        return json_encode($this->toArray(), $options);
    }

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }

    public function getIterator(): ArrayIterator
    {
        return new ArrayIterator($this->items);
    }

    public function __toString(): string
    {
        return $this->toJson();
    }
}