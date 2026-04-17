<?php

declare(strict_types=1);

namespace Marwa\Support\Validation\Contracts;

use Psr\Http\Message\ServerRequestInterface;

interface ValidatorInterface
{
    public function validateRequest(ServerRequestInterface $request, array $rules): array;
}