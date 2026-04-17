<?php

declare(strict_types=1);

namespace Marwa\Support\Tests;

use Marwa\Support\Validation\ErrorBag;
use Marwa\Support\Validation\RequestValidator;
use Marwa\Support\Validation\ValidationException;
use PHPUnit\Framework\TestCase;

class RequestValidatorTest extends TestCase
{
    private RequestValidator $validator;

    protected function setUp(): void
    {
        $this->validator = new RequestValidator();
    }

    public function testValidateInputWithRequiredRule(): void
    {
        $result = $this->validator->validateInput(
            ['name' => 'John'],
            ['name' => 'required']
        );

        $this->assertSame(['name' => 'John'], $result);
    }

    public function testValidateInputWithEmailRule(): void
    {
        $result = $this->validator->validateInput(
            ['email' => 'john@example.com'],
            ['email' => 'required|email']
        );

        $this->assertSame(['email' => 'john@example.com'], $result);
    }

    public function testValidateInputFailsWithInvalidEmail(): void
    {
        $this->expectException(ValidationException::class);

        $this->validator->validateInput(
            ['email' => 'invalid-email'],
            ['email' => 'required|email']
        );
    }

    public function testValidateInputWithMinRule(): void
    {
        $result = $this->validator->validateInput(
            ['password' => 'secret123'],
            ['password' => 'required|string|min:6']
        );

        $this->assertSame(['password' => 'secret123'], $result);
    }

    public function testValidateInputFailsWithMinRule(): void
    {
        $this->expectException(ValidationException::class);

        $this->validator->validateInput(
            ['password' => '123'],
            ['password' => 'required|string|min:6']
        );
    }

    public function testValidateInputWithMaxRule(): void
    {
        $result = $this->validator->validateInput(
            ['name' => 'John'],
            ['name' => 'required|string|max:10']
        );

        $this->assertSame(['name' => 'John'], $result);
    }

    public function testValidateInputWithBetweenRule(): void
    {
        $result = $this->validator->validateInput(
            ['age' => 25],
            ['age' => 'required|numeric|between:18,150']
        );

        $this->assertSame(['age' => 25.0], $result);
    }

    public function testValidateInputWithInRule(): void
    {
        $result = $this->validator->validateInput(
            ['role' => 'admin'],
            ['role' => 'required|in:admin,user,guest']
        );

        $this->assertSame(['role' => 'admin'], $result);
    }

    public function testValidateInputWithSameRule(): void
    {
        $result = $this->validator->validateInput(
            ['password' => 'secret', 'password_confirmation' => 'secret'],
            ['password_confirmation' => 'same:password']
        );

        $this->assertSame(['password_confirmation' => 'secret'], $result);
    }

    public function testValidateInputWithDateRule(): void
    {
        $result = $this->validator->validateInput(
            ['birthdate' => '2024-01-15'],
            ['birthdate' => 'required|date']
        );

        $this->assertSame(['birthdate' => '2024-01-15'], $result);
    }

    public function testValidateInputWithRegexRule(): void
    {
        $result = $this->validator->validateInput(
            ['code' => 'ABC'],
            ['code' => 'required|regex:/^[A-Z]+$/']
        );

        $this->assertSame(['code' => 'ABC'], $result);
    }

    public function testValidateInputWithNumericRule(): void
    {
        $result = $this->validator->validateInput(
            ['amount' => '123.45'],
            ['amount' => 'required|numeric']
        );

        $this->assertSame(['amount' => 123.45], $result);
    }

    public function testValidateInputWithIntegerRule(): void
    {
        $result = $this->validator->validateInput(
            ['count' => '42'],
            ['count' => 'required|integer']
        );

        $this->assertSame(['count' => 42], $result);
    }

    public function testValidateInputWithBooleanRule(): void
    {
        $result = $this->validator->validateInput(
            ['active' => 'true'],
            ['active' => 'required|boolean']
        );

        $this->assertSame(['active' => true], $result);
    }

    public function testValidateInputWithArrayRule(): void
    {
        $result = $this->validator->validateInput(
            ['tags' => ['php', 'java']],
            ['tags' => 'required|array']
        );

        $this->assertSame(['tags' => ['php', 'java']], $result);
    }

    public function testValidateInputWithUrlRule(): void
    {
        $result = $this->validator->validateInput(
            ['website' => 'https://example.com'],
            ['website' => 'required|url']
        );

        $this->assertSame(['website' => 'https://example.com'], $result);
    }

    public function testValidateInputWithAcceptedRule(): void
    {
        $result = $this->validator->validateInput(
            ['terms' => 'yes'],
            ['terms' => 'required|accepted']
        );

        $this->assertSame(['terms' => 'yes'], $result);
    }

    public function testValidationExceptionContainsErrors(): void
    {
        try {
            $this->validator->validateInput(
                ['email' => 'invalid'],
                ['email' => 'required|email']
            );
            $this->fail('Expected ValidationException');
        } catch (ValidationException $e) {
            $this->assertInstanceOf(ErrorBag::class, $e->errors());
            $this->assertNotEmpty($e->getErrors());
            $this->assertNotEmpty($e->getFirstErrors());
        }
    }

    public function testNullableRuleAllowsNullValues(): void
    {
        $result = $this->validator->validateInput(
            ['email' => null],
            ['email' => 'nullable|email']
        );

        $this->assertNull($result['email']);
    }

    public function testSometimesRuleSkipsMissingFields(): void
    {
        $result = $this->validator->validateInput(
            ['name' => 'John'],
            ['email' => 'sometimes|email']
        );

        $this->assertSame([], $result);
    }

    public function testBailRuleStopsOnFirstFailure(): void
    {
        $this->expectException(ValidationException::class);

        $this->validator->validateInput(
            ['email' => 'invalid'],
            ['email' => 'required|bail|email']
        );
    }

    public function testValidateInputSupportsArrayRuleFormat(): void
    {
        $result = $this->validator->validateInput(
            ['name' => 'John'],
            ['name' => ['required', 'string', 'min:2']]
        );

        $this->assertSame(['name' => 'John'], $result);
    }

    public function testValidateInputWithCustomMessages(): void
    {
        try {
            $this->validator->validateInput(
                ['email' => 'invalid'],
                ['email' => 'required|email'],
                ['email.email' => 'Please enter a valid email address']
            );
            $this->fail('Expected ValidationException');
        } catch (ValidationException $e) {
            $errors = $e->getFirstErrors();
            $this->assertStringContainsString('valid email', $errors['email'] ?? '');
        }
    }
}
