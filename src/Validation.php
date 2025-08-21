<?php
declare(strict_types=1);

namespace Marwa\Support;

use Exception;

class Validation
{
    protected array $data = [];
    protected array $rules = [];
    protected array $errors = [];
    protected array $messages = [];
    protected array $customRules = [];

    public static function make(array $data, array $rules, array $messages = []): self
    {
        $validator = new static();
        $validator->data = $data;
        $validator->rules = $rules;
        $validator->messages = $messages;
        return $validator;
    }

    public function validate(): bool
    {
        $this->errors = [];

        foreach ($this->rules as $field => $rules) {
            $rules = is_array($rules) ? $rules : explode('|', $rules);
            $value = $this->getValue($field);

            foreach ($rules as $rule) {
                $this->validateRule($field, $value, $rule);
            }
        }

        return empty($this->errors);
    }

    public function errors(): array
    {
        return $this->errors;
    }

    public function fails(): bool
    {
        return !$this->validate();
    }

    public function addError(string $field, string $message): void
    {
        $this->errors[$field][] = $message;
    }

    public function extend(string $rule, callable $validator): void
    {
        $this->customRules[$rule] = $validator;
    }

    protected function validateRule(string $field, $value, string $rule): void
    {
        [$rule, $parameters] = $this->parseRule($rule);

        if ($rule === 'nullable' && ($value === null || $value === '')) {
            return;
        }

        $method = 'validate' . str_replace(' ', '', ucwords(str_replace('_', ' ', $rule)));

        if (method_exists($this, $method)) {
            if (!$this->$method($field, $value, $parameters)) {
                $this->addError($field, $this->getMessage($field, $rule, $parameters));
            }
        } elseif (isset($this->customRules[$rule])) {
            if (!$this->customRules[$rule]($field, $value, $parameters, $this->data)) {
                $this->addError($field, $this->getMessage($field, $rule, $parameters));
            }
        }
    }

    protected function parseRule(string $rule): array
    {
        $parameters = [];

        if (str_contains($rule, ':')) {
            [$rule, $parameter] = explode(':', $rule, 2);
            $parameters = explode(',', $parameter);
        }

        return [$rule, $parameters];
    }

    protected function getValue(string $field)
    {
        return $this->data[$field] ?? null;
    }

    protected function getMessage(string $field, string $rule, array $parameters): string
    {
        $key = "{$field}.{$rule}";

        if (isset($this->messages[$key])) {
            return $this->messages[$key];
        }

        if (isset($this->messages[$rule])) {
            return $this->messages[$rule];
        }

        return $this->getDefaultMessage($field, $rule, $parameters);
    }

    protected function getDefaultMessage(string $field, string $rule, array $parameters): string
    {
        $messages = [
            'required' => "The {$field} field is required.",
            'email' => "The {$field} must be a valid email address.",
            'min' => "The {$field} must be at least {$parameters[0]} characters.",
            'max' => "The {$field} may not be greater than {$parameters[0]} characters.",
            'numeric' => "The {$field} must be a number.",
            'string' => "The {$field} must be a string.",
            'array' => "The {$field} must be an array.",
            'in' => "The {$field} must be one of: " . implode(', ', $parameters) . ".",
            'same' => "The {$field} must match {$parameters[0]}.",
        ];

        return $messages[$rule] ?? "The {$field} field is invalid.";
    }

    // Built-in validation rules
    protected function validateRequired(string $field, $value): bool
    {
        if (is_null($value)) {
            return false;
        } elseif (is_string($value) && trim($value) === '') {
            return false;
        } elseif (is_array($value) && count($value) === 0) {
            return false;
        }

        return true;
    }

    protected function validateEmail(string $field, $value): bool
    {
        return filter_var($value, FILTER_VALIDATE_EMAIL) !== false;
    }

    protected function validateMin(string $field, $value, array $parameters): bool
    {
        $min = (int) $parameters[0];

        if (is_string($value)) {
            return mb_strlen($value) >= $min;
        } elseif (is_array($value)) {
            return count($value) >= $min;
        } elseif (is_numeric($value)) {
            return $value >= $min;
        }

        return false;
    }

    protected function validateMax(string $field, $value, array $parameters): bool
    {
        $max = (int) $parameters[0];

        if (is_string($value)) {
            return mb_strlen($value) <= $max;
        } elseif (is_array($value)) {
            return count($value) <= $max;
        } elseif (is_numeric($value)) {
            return $value <= $max;
        }

        return false;
    }

    protected function validateNumeric(string $field, $value): bool
    {
        return is_numeric($value);
    }

    protected function validateString(string $field, $value): bool
    {
        return is_string($value);
    }

    protected function validateArray(string $field, $value): bool
    {
        return is_array($value);
    }

    protected function validateIn(string $field, $value, array $parameters): bool
    {
        return in_array($value, $parameters, true);
    }

    protected function validateSame(string $field, $value, array $parameters): bool
    {
        $otherField = $parameters[0];
        $otherValue = $this->getValue($otherField);

        return $value === $otherValue;
    }

    protected function validateNullable(string $field, $value): bool
    {
        return true; // Always passes, used for early return in validateRule
    }
}