<?php

declare(strict_types=1);

namespace Marwa\Support\Tests;

use Marwa\Support\Validation;
use Marwa\Support\Validator;
use PHPUnit\Framework\TestCase;

class ValidationTest extends TestCase
{
    public function testNullableRuleSkipsFurtherValidation(): void
    {
        $validator = Validation::make(
            ['profile' => ['email' => null]],
            ['profile.email' => 'nullable|email']
        );

        $this->assertTrue($validator->validate());
        $this->assertSame([], $validator->errors());
    }

    public function testNestedFieldValidationUsesDotNotation(): void
    {
        $validator = Validation::make(
            ['user' => ['email' => 'invalid']],
            ['user.email' => 'required|email']
        );

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('user.email', $validator->errors());
    }

    public function testBooleanValidatorAcceptsFalseLikeValues(): void
    {
        $this->assertTrue(Validator::check('false', 'boolean'));
        $this->assertTrue(Validator::check(false, 'boolean'));
        $this->assertFalse(Validator::check('not-a-bool', 'boolean'));
    }
}
