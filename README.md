# Marwa Support

[![Latest Version](https://img.shields.io/packagist/v/memran/marwa-support.svg)](https://packagist.org/packages/memran/marwa-support)
[![Total Downloads](https://img.shields.io/packagist/dt/memran/marwa-support.svg)](https://packagist.org/packages/memran/marwa-support)
[![License](https://img.shields.io/packagist/l/memran/marwa-support.svg)](https://packagist.org/packages/memran/marwa-support)
[![PHP Version](https://img.shields.io/packagist/php-v/memran/marwa-support.svg)](https://packagist.org/packages/memran/marwa-support)
[![CI](https://github.com/memran/marwa-support/actions/workflows/ci.yml/badge.svg)](https://github.com/memran/marwa-support/actions/workflows/ci.yml)
[![Coverage](https://img.shields.io/codecov/c/github/memran/marwa-support.svg)](https://codecov.io/gh/memran/marwa-support)
[![PHPStan](https://img.shields.io/badge/PHPStan-level%205-brightgreen.svg)](https://phpstan.org/)

Framework-agnostic PHP support utilities for common string, array, file, object, validation, HTML, and security tasks. The package is designed for library and application code that needs small, composable helpers with strict typing and predictable behavior.

## Requirements

- PHP 8.2 or newer
- Extensions: `json`, `mbstring`, `openssl`

## Installation

```bash
composer require memran/marwa-support
```

For local development:

```bash
composer install
```

## Usage

```php
use Marwa\Support\Crypt;
use Marwa\Support\File;
use Marwa\Support\Helper;
use Marwa\Support\Str;
use Marwa\Support\Validation;

$slug = Str::slug('Secure Helper Library');
$payload = Crypt::encrypt('secret', $_ENV['APP_KEY']);
File::put(__DIR__ . '/storage/example.json', ['slug' => $slug]);

$validator = Validation::make(
    ['user' => ['email' => 'team@example.com']],
    ['user.email' => 'required|email']
);

if ($validator->fails()) {
    var_dump($validator->errors());
}

$result = Helper::retry(3, fn () => 'ok');
```

## Configuration

The library is mostly configuration-free. Pass secrets such as encryption keys from your application configuration or environment, not from hard-coded strings. For CSRF helpers, call them only in applications that already manage PHP sessions safely.

## Testing

```bash
composer test
composer test:coverage
```

## Static Analysis

```bash
composer analyse
composer lint
composer fix
```

`phpstan.neon.dist` runs analysis on `src/` at level 5. `.php-cs-fixer.dist.php` enforces PSR-12-style formatting with strict typing rules.

## CI/CD

GitHub Actions runs Composer validation, coding standards, PHPStan, and PHPUnit on PHP 8.2, 8.3, and 8.4 through [`.github/workflows/ci.yml`](./.github/workflows/ci.yml).

## Contributing

1. Install dependencies with `composer install`.
2. Run `composer ci` before opening a pull request.
3. Add or update PHPUnit coverage for behavior changes.
4. Keep commits focused and describe the behavioral impact in the PR.

Repository-specific contributor guidance is available in [`AGENTS.md`](./AGENTS.md).
