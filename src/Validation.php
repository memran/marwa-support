<?php

declare(strict_types=1);

namespace Marwa\Support;

use Marwa\Support\Validation\RequestValidator;
use Marwa\Support\Validation\Rules\ClosureRule;
use Marwa\Support\Validation\ValidationException;

class Validation
{
    protected array $data = [];
    protected array $rules = [];
    protected array $errors = [];
    protected array $messages = [];
    protected array $customRules = [];
    protected bool $validated = false;
    protected ?array $validatedData = null;

    private RequestValidator $validator;

    public static function make(array $data, array $rules, array $messages = []): self
    {
        $validator = new self();
        $validator->data = $data;
        $validator->rules = $rules;
        $validator->messages = $messages;
        $validator->validator = new RequestValidator();
        return $validator;
    }

    public function validate(): bool
    {
        $this->errors = [];
        $this->validated = true;

        try {
            if (!empty($this->customRules)) {
                $rules = $this->injectCustomRules();
                $this->validatedData = $this->validator->validateInput(
                    $this->data,
                    $rules,
                    $this->messages
                );
            } else {
                $this->validatedData = $this->validator->validateInput(
                    $this->data,
                    $this->rules,
                    $this->messages
                );
            }
            return true;
        } catch (ValidationException $e) {
            $this->errors = $e->getErrors();
            return false;
        }
    }

    public function errors(): array
    {
        return $this->errors;
    }

    public function fails(): bool
    {
        if (!$this->validated) {
            return !$this->validate();
        }
        return !empty($this->errors);
    }

    public function addError(string $field, string $message): void
    {
        $this->errors[$field][] = $message;
    }

    public function extend(string $rule, callable $callback): void
    {
        $this->customRules[$rule] = $callback;
    }

    private function injectCustomRules(): array
    {
        $rules = $this->rules;

        foreach ($this->customRules as $ruleName => $callback) {
            foreach ($rules as $field => $fieldRules) {
                $ruleList = is_array($fieldRules) ? $fieldRules : explode('|', $fieldRules);

                if (in_array($ruleName, $ruleList, true)) {
                    $rules[$field][] = $this->createClosureRule($callback);
                }
            }
        }

        return $rules;
    }

    private function createClosureRule(callable $callback): ClosureRule
    {
        return new ClosureRule($callback);
    }
}
