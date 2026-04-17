<?php

declare(strict_types=1);

namespace Marwa\Support\Validation;

class ErrorBag
{
    private array $errors = [];

    public function add(string $field, string $message): void
    {
        $this->errors[$field][] = $message;
    }

    public function has(string $field): bool
    {
        return isset($this->errors[$field]) && count($this->errors[$field]) > 0;
    }

    public function hasAny(): bool
    {
        return count($this->errors) > 0;
    }

    public function get(string $field): array
    {
        return $this->errors[$field] ?? [];
    }

    public function all(): array
    {
        return $this->errors;
    }

    public function first(string $field): ?string
    {
        return $this->errors[$field][0] ?? null;
    }

    public function firstOfAll(): array
    {
        $first = [];
        foreach ($this->errors as $field => $messages) {
            $first[$field] = $messages[0];
        }
        return $first;
    }

    public function count(): int
    {
        return count($this->errors);
    }
}