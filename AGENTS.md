# Agent Guidelines

Quick reference for AI agents working on this codebase.

## Quick Reference

```bash
# Install & test
composer install
composer test

# Lint & analyze
vendor/bin/php-cs-fixer fix --dry-run
vendor/bin/phpstan analyse --level=5

# Run single test
vendor/bin/phpunit tests/StrTest.php
vendor/bin/phpunit --filter testSlug
```

## Project Structure

### Directory Layout
```
src/           # Library code (Marwa\Support namespace)
src/Validation/  # Validation system (Rules, Helpers, Contracts)
tests/         # PHPUnit tests (*Test.php)
coverage/     # Test coverage output
```

### Naming Conventions
- Classes: `PascalCase` (e.g., `Str.php`, `RequestValidator.php`)
- Methods: `camelCase`
- Tests: `*Test.php` (e.g., `StrTest.php`)
- Interfaces: `*Interface` (e.g., `RuleInterface`)
- Exceptions: `*Exception` (e.g., `ValidationException`)

## Code Standards

### PHP Requirements
- PHP 8.0+
- `declare(strict_types=1);` at top of every file

### Style Rules
- 4-space indentation
- Opening braces on next line for classes/methods
- PSR-1, PSR-12, PSR-4 compliant

### Best Practices
- Keep files under 200 lines/class
- Keep methods under 20 lines
- Use typed properties and explicit return types
- Use constants/enums for finite states
- Validate all inputs
- Prefer composition over inheritance

## Validation System

### Structure
```
src/Validation/
├── Contracts/      # Interfaces
├── Helpers/        # Utility classes
├── Rules/          # Rule classes
├── AbstractRule.php
├── ErrorBag.php
├── RequestValidator.php
├── RuleRegistry.php
└── ValidationException.php
```

### Creating Rules

```php
use Marwa\Support\Validation\AbstractRule;

class MyRule extends AbstractRule
{
    public function name(): string
    {
        return 'my_rule';
    }

    public function validate(mixed $value, array $context): bool
    {
        return $value === 'expected';
    }

    public function message(string $field, array $attributes): string
    {
        return $this->formatMessage(
            'The :attribute is invalid.',
            $field,
            $attributes
        );
    }
}
```

### Using Validation

```php
use Marwa\Support\Validation\RequestValidator;
use Marwa\Support\Validation\ValidationException;

$validator = new RequestValidator();

try {
    $validated = $validator->validateInput($data, $rules);
} catch (ValidationException $e) {
    $errors = $e->getErrors();
}
```

### Available Rules
- Type: `required`, `string`, `integer`, `numeric`, `boolean`, `array`
- Format: `email`, `url`, `ip`, `mac`
- Size: `min:value`, `max:value`, `between:min,max`
- Content: `in:val1,val2`, `same:field`, `confirmed`
- Date: `date`, `date_format:Y-m-d`, `regex:pattern`
- File: `file`, `image`, `mimes:jpeg,png`, `size:1MB`
- Special: `accepted`, `declined`, `nullable`, `sometimes`

## Testing

### Guidelines
- Add tests in `tests/` directory
- Name tests after class: `tests/StrTest.php`
- Cover edge cases and boundary conditions
- Aim for 80% minimum coverage

### Running Tests
```bash
composer test              # Run all tests
composer test-coverage   # Generate coverage report
vendor/bin/phpunit --filter testMethod  # Run specific test
```

### Test Structure
```php
use PHPUnit\Framework\TestCase;

class MyClassTest extends TestCase
{
    public function testMethodName(): void
    {
        $result = MyClass::method('input');
        $this->assertExpected($result);
    }
}
```

## Commit Guidelines

### Commit Message Format
- Use short, imperative subjects
- Keep focused: one logical change per commit

### Good Examples
```
Add str slug method
Fix validation email rule
Add mimes and size rules
Update composer version constraints
```

### PR Requirements
- Describe behavioral change
- List affected classes
- Mention added/updated tests
- Include usage examples for public APIs

## Configuration

### Environment
- Copy `.env.example` to `.env`
- Never commit secrets

### Dependencies
- Add via `composer require`
- Document new packages in README

## Never Do This

- ❌ Edit `vendor/*` code
- ❌ Expose secrets/passwords
- ❌ Create duplicate classes
- ❌ Skip validation on inputs

## Versioning

- Current: `v1.3.0`
- Updated: `2026-04-17`

## Change Log

- `v1.3.0`: Rule-based validation system, file validation, IP/MAC rules
- `v1.2.x`: Bug fixes and improvements
- `v1.0.0`: Initial release