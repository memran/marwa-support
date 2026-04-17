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

## Style

- `declare(strict_types=1);`
- PSR-1, PSR-12, PSR-4
- 4-space indentation
- Typed properties and explicit return types
- PascalCase classes
- `*Interface`, `*Exception`
- Prefer small, single-purpose classes
- Keep files small: prefer max 200 lines/class, 20 lines/method
- Use constants and enums for finite states

## Engineering Principles

- KISS, DRY, SOLID
- Understand context before coding
- Prefer composition over inheritance
- Keep architecture modular and decoupled
- Write production-ready, maintainable, scalable code
- Prefer clarity over cleverness
- Align with project architecture
- Edit existing code over creating duplicates
- Maintain backward compatibility
- Keep changes minimal and scoped
- Validate all inputs
- Use composer packages by creating adapter

## Testing

- Add tests in `tests/`
- Use `*Test.php` or `*_test.php`
- Cover routing, bootstrapping, middleware, and adapters
- Run `composer test`, then `composer stan`
- Aim for 80% minimum coverage
- Every public service method needs unit tests

## Commit & PR

- Use short, imperative commit subjects
- Keep commits focused: one logical change per commit
- PRs should explain the problem, approach, and verification
- Link related issues
- Include CLI output or request/response examples when user-facing behavior changes

## Configuration

- Copy `.env.example` to `.env` in consuming apps
- Never commit secrets
- Document new env keys in `README.MD` or the relevant docs

## Never

- Never change `vendor/*`
- Never expose secrets or passwords
- Suggest changes to vendor code instead of editing it

## Error Handling

- Use centralized exception handling
- Log critical errors
- Fail gracefully with meaningful responses

## Performance

- Optimize for readability first
- Avoid premature optimization
- Cache where necessary

## Documentation

- Update readme when code changes require it
- Keep explanations useful
- Add section anchors for navigation
- Add examples
- Add diagrams only when they help

## Versioning

- Version: `v1.0.0`
- Updated: `2026-04-17`

## Change Log

- DATE : Change
