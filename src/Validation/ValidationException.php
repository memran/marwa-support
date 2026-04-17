<?php

declare(strict_types=1);

namespace Marwa\Support\Validation;

use Exception;

class ValidationException extends Exception
{
    private ErrorBag $errors;
    private array $input;

    public function __construct(ErrorBag $errors, array $input, string $message = 'Validation failed')
    {
        parent::__construct($message);
        $this->errors = $errors;
        $this->input = $input;
    }

    public function errors(): ErrorBag
    {
        return $this->errors;
    }

    public function input(): array
    {
        return $this->input;
    }

    public function getErrors(): array
    {
        return $this->errors->all();
    }

    public function getFirstErrors(): array
    {
        return $this->errors->firstOfAll();
    }
}