# Repository Guidelines

## Project Structure & Module Organization
`src/` contains the library code under the `Marwa\Support\` namespace. Each utility is a single class file such as `Str.php`, `Arr.php`, `File.php`, and `Validation.php`. `tests/` mirrors that layout with PHPUnit cases like `StrTest.php` and `ArrTest.php`. `example.php` is a lightweight usage sample, and `README.md` is the public-facing package documentation. Composer autoloading is PSR-4, so new classes should live in `src/` with matching namespaces and filenames.

## Build, Test, and Development Commands
Run `composer install` to install PHPUnit and regenerate autoload files. Use `composer test` for the default test suite and `composer test-coverage` to generate HTML coverage in `coverage/`. For focused work, run a single file or test with `vendor/bin/phpunit tests/StrTest.php` or `vendor/bin/phpunit --filter testSlug`.

## Coding Style & Naming Conventions
Target PHP 8.0+ and keep `declare(strict_types=1);` at the top of PHP files. Follow the existing style: 4-space indentation, opening braces on the next line for classes and methods, and descriptive `camelCase` method names. Class names use `StudlyCase`, test classes end in `Test`, and static utility APIs should remain small and type-hinted. No formatter or linter is configured in Composer, so match the surrounding code when editing.

## Testing Guidelines
Tests use PHPUnit 9.6 with `phpunit.xml` loading `vendor/autoload.php` and collecting coverage from `src/`. Add or update tests whenever behavior changes, especially for edge cases in pure utility methods. Name new tests after the class under test, for example `tests/RandomTest.php`. Note: the current suite exits early around `HelperTest::testDdExits`, so verify targeted tests locally when changing adjacent helper behavior.

## Commit & Pull Request Guidelines
Recent commit messages are short, imperative, and plain, for example `update composer`, `Fix issues`, and `Split security to multiple classes`. Keep commits focused and similarly direct. Pull requests should describe the behavioral change, list affected classes, mention added or updated tests, and include example usage when public APIs or README-visible behavior changes.
