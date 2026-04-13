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

## API Reference

### Strings (`Str`)

String manipulation utilities with full Unicode support.

```php
use Marwa\Support\Str;

// Case conversion
Str::lower('HELLO');                    // "hello"
Str::upper('hello');                    // "HELLO"
Str::title('hello world');              // "Hello World"

// Case styles
Str::camel('hello_world');              // "helloWorld"
Str::studly('hello_world');             // "HelloWorld"
Str::snake('helloWorld');               // "hello_world"
Str::snake('HelloWorld', '-');          // "hello-world"

// Slug generation
Str::slug('Hello World!');              // "hello-world"
Str::slug('Secure Helper Library');    // "secure-helper-library"

// String checks
Str::contains('hello world', 'world');  // true
Str::contains('hello world', ['foo', 'world']); // true
Str::startsWith('hello', 'hel');        // true
Str::endsWith('hello', 'llo');         // true
Str::is('*.php', 'controller.php');    // true
Str::is('test*', 'testing');           // true

// String extraction
Str::between('hello[world]', '[', ']'); // "world"
Str::substring('hello', 1, 3);         // "ell"
Str::limit('hello world', 5);          // "hello..."
Str::limit('hello world', 5, '…');     // "hello…"

// HTML handling
Str::escape('<script>');               // "&lt;script&gt;"
Str::stripTags('<b>hello</b>');       // "hello"
Str::hash('password');                // "5e884898da28047151d0e56f8dc6292773603d0d6aabbdd62a11ef721d1542d8"

// Misc
Str::random(16);                       // "aB3kL9mNpQxR2tYv"
Str::wordCount('hello world');        // 2
Str::pad('hello', 10, '-', STR_PAD_BOTH); // "--hello---"
Str::repeat('ab', 3);                  // "ababab"
Str::reverse('hello');                 // "olleh"
Str::toBytes('10MB');                 // 10485760
```

### Arrays (`Arr`)

Array manipulation with dot notation support.

```php
use Marwa\Support\Arr;

// Dot notation access
$data = ['user' => ['name' => 'John', 'email' => 'john@example.com']];
Arr::get($data, 'user.name');                          // "John"
Arr::get($data, 'user.phone', 'N/A');                  // "N/A"
Arr::has($data, 'user.email');                         // true
Arr::has($data, 'user.phone');                         // false

// Dot notation set
$array = [];
Arr::set($array, 'user.name', 'John');                 // ['user' => ['name' => 'John']]
Arr::set($array, 'items.0.name', 'Item 1');           // ['user' => [...], 'items' => [['name' => 'Item 1']]]

// Flatten to dot notation
Arr::dot(['a' => ['b' => 1]]);                         // ['a.b' => 1]

// Find elements
Arr::first([1, 2, 3]);                                 // 1
Arr::first([1, 2, 3], fn($v) => $v > 1);              // 2
Arr::last([1, 2, 3]);                                 // 3
Arr::last([1, 2, 3], fn($v) => $v < 3);               // 2

// Transform
Arr::pluck([['id' => 1, 'name' => 'a'], ['id' => 2, 'name' => 'b']], 'name');
// ['a', 'b']
Arr::pluck([['id' => 1, 'name' => 'a'], ['id' => 2, 'name' => 'b']], 'name', 'id');
// [1 => 'a', 2 => 'b']
Arr::where([1, 2, 3, 4], fn($v) => $v > 2);           // [3, 4]
Arr::only(['a' => 1, 'b' => 2, 'c' => 3], ['a', 'c']); // ['a' => 1, 'c' => 3]
Arr::except(['a' => 1, 'b' => 2, 'c' => 3], ['b']);   // ['a' => 1, 'c' => 3]
```

### Files (`File`)

Atomic file operations with validation and directory creation.

```php
use Marwa\Support\File;

// Write/Read
File::put(__DIR__ . '/data.json', ['key' => 'value']); // Returns byte count
File::get(__DIR__ . '/data.json');                     // Returns file contents

// Append
File::append(__DIR__ . '/log.txt', "New log entry\n");

// Delete
File::delete(__DIR__ . '/old.txt');                    // Returns bool

// Directory operations
File::makeDirectory(__DIR__ . '/newfolder');

// File info
File::exists(__DIR__ . '/file.txt');                   // bool
File::size(__DIR__ . '/file.txt');                     // int (bytes)
File::lastModified(__DIR__ . '/file.txt');             // int (timestamp)

// Copy/Move
File::copy('source.txt', 'dest.txt');
File::move('old.txt', 'new.txt');
```

### Security (`Security`, `Crypt`, `Hash`, `Random`, `Sanitizer`, `XSS`, `CSRF`, `Validator`)

Comprehensive security utilities.

```php
use Marwa\Support\Security;
use Marwa\Support\Crypt;
use Marwa\Support\Hash;
use Marwa\Support\Random;
use Marwa\Support\Sanitizer;
use Marwa\Support\XSS;
use Marwa\Support\CSRF;
use Marwa\Support\Validator;

// Encryption/Decryption (AES-256-GCM)
$encrypted = Crypt::encrypt('secret data', 'my-key');
$decrypted = Crypt::decrypt($encrypted, 'my-key');

// Password hashing
$hash = Hash::make('password');
Hash::verify('password', $hash);                       // true
Hash::needsRehash($hash);                             // bool

// Random generation
Random::bytes(32);                                    // Raw bytes
Random::string(16);                                   // Alphanumeric string
Random::string(16, 'alpha');                          // Letters only
Random::string(16, 'hex');                            // Hex characters
Random::uuid();                                       // v4 UUID

// Security facade
Security::encrypt($data, $key);
Security::decrypt($data, $key);
Security::hash('password');
Security::verifyHash('password', $hash);
Security::randomBytes(32);
Security::randomString(16);
Security::uuid();
Security::sanitize($input, 'email');
Security::safeFileName('file name.exe');
Security::validate('test@example.com', 'email');
Security::isMalicious('<script>alert(1)</script>'); // true
Security::csrfToken();
Security::verifyCsrf($token);
Security::xssClean($input);
Security::hashEquals($known, $user);

// Sanitization
Sanitizer::clean('<script>alert(1)</script>', 'string');
Sanitizer::clean('test@example.com', 'email');
Sanitizer::clean('http://example.com', 'url');
Sanitizer::clean('123.45', 'float');
Sanitizer::filename('test file.php');                // "test_file.php"

// XSS Cleaning
XSS::clean('<script>alert(1)</script><b>safe</b>');
// Output: "&lt;script&gt;alert(1)&lt;/script&gt;<b>safe</b>"

// CSRF Tokens (requires session)
$token = CSRF::token();
CSRF::verify($token);                                 // true/false

// Validation
Validator::check('test@example.com', 'email');       // true
Validator::check('http://example.com', 'url');       // true
Validator::check('192.168.1.1', 'ip');                // true
Validator::check('00:1B:44:11:3A:B7', 'mac');        // true
Validator::isMalicious('<script>alert(1)</script>'); // true
```

### Validation (`Validation`)

Full-featured validation with pipe syntax.

```php
use Marwa\Support\Validation;

$validator = Validation::make(
    [
        'name' => 'John',
        'email' => 'john@example.com',
        'age' => 25,
        'password' => 'secret123',
        'password_confirmation' => 'secret123',
        'role' => 'admin',
    ],
    [
        'name' => 'required|min:2|max:50',
        'email' => 'required|email',
        'age' => 'numeric|min:18|max:150',
        'password' => 'required|string|min:6',
        'password_confirmation' => 'same:password',
        'role' => 'in:admin,user,guest',
    ]
);

if ($validator->fails()) {
    print_r($validator->errors());
}

// Custom messages
$validator = Validation::make(
    ['email' => 'invalid'],
    ['email' => 'email'],
    ['email.email' => 'Please enter a valid email address']
);

// Custom rules
$validator = Validation::make(['code' => 'ABC123'], ['code' => 'required']);
$validator->extend('uppercase', fn($field, $value) => strtoupper($value) === $value);
$validator->addError('code', 'Code must be uppercase');
```

### Helpers (`Helper`)

Functional programming utilities.

```php
use Marwa\Support\Helper;

// Tap - execute callback without changing value
$result = Helper::tap('hello', fn($v) => strtoupper($v)); // Returns "hello"

// Pipe - chain transformations
$result = Helper::pipe(5, [
    fn($v) => $v * 2,
    fn($v) => $v + 1,
]); // 11

// With - apply callback to value, return default on null
Helper::with(null, fn($v) => strtoupper($v), 'default'); // "default"
Helper::with('hello', fn($v) => strtoupper($v));         // "HELLO"

// Debugging
Helper::dump($var);  // Returns var_dump output as string
Helper::dd($var);    // Dumps and exits

// Retry with backoff
$result = Helper::retry(3, fn() => someApiCall(), 100); // retries 3 times, 100ms sleep

// Data get (nested access)
Helper::dataGet(['user' => ['name' => 'John']], 'user.name'); // "John"
Helper::dataGet(['items' => [['a' => 1], ['a' => 2]]], 'items.*.a'); // [1, 2]

// Value - unwrap closures or return as-is
Helper::value('hello');                                      // "hello"
Helper::value(fn() => 'computed');                           // "computed"

// Type checking
Helper::empty('');           // true
Helper::empty('0');          // false
Helper::typeOf($var);        // "string", "array", "classname", etc.

// Functional
$memoized = Helper::memoize(fn($n) => expensive($n));
$curried = Helper::curry(fn($a, $b, $c) => $a + $b + $c);

// Array operations
Helper::groupBy([['role' => 'admin'], ['role' => 'user']], 'role');
// ['admin' => [[...]], 'user' => [[...]]]
Helper::keyBy([['id' => 1], ['id' => 2]], 'id');
// [1 => [...], 2 => [...]]

// Math
Helper::percentage(25, 100);      // 25.0
Helper::mapRange(50, 0, 100, 0, 1); // 0.5

// Object
Helper::deepClone($object);
Helper::implements($object, 'JsonSerializable');

// Misc
Helper::uuid();                    // UUID v4
Helper::truncate('long string', 10); // "long strin..."
Helper::measure(fn() => sleep(1));   // Returns execution time in ms
Helper::memoryUsage(memory_get_usage()); // "1.5 MB"
```

### Objects (`Obj`)

Object manipulation utilities.

```php
use Marwa\Support\Obj;

class User {
    public string $name = 'John';
    public array $data = [];
}

$user = new User();

// Convert to array
Obj::toArray($user);    // ['name' => 'John', 'data' => []]

// Fill properties
Obj::fill($user, ['name' => 'Jane', 'age' => 30]);

// Get nested property
$obj = (object) ['user' => ['name' => 'John']];
Obj::get($obj, 'user.name'); // "John"

// Check property exists
Obj::has($obj, 'user.name'); // true

// Convert to JSON
Obj::toJson($user);    // '{"name":"John","data":{}}'

// Other
Obj::clone($object);
Obj::className($object);    // "User"
Obj::properties($object);   // ['name' => 'John', ...]
```

### HTML (`Html`)

HTML generation and manipulation.

```php
use Marwa\Support\Html;

// Element generation
Html::element('div', ['class' => 'container'], 'Content');
// <div class="container">Content</div>

Html::element('img', ['src' => 'image.jpg', 'alt' => 'Photo']);
// <img src="image.jpg" alt="Photo">

// Common tags
Html::link('https://example.com', 'Visit Site', ['class' => 'btn']);
Html::image('photo.jpg', 'A photo');
Html::script('app.js');
Html::style('style.css');
Html::meta('description', 'My website');
Html::form('/submit', 'POST', ['class' => 'form']);

// Form elements
Html::input('text', 'name', 'John', ['class' => 'input']);
Html::input('email', 'email', 'test@example.com');
Html::select('country', ['us' => 'USA', 'uk' => 'UK'], 'us');
Html::select('color', ['red' => 'Red', 'blue' => 'Blue'], null, ['multiple' => true]);

// HTML array structure
$structure = [
    'div' => [
        'attributes' => ['class' => 'card'],
        'content' => [
            'h2' => 'Title',
            'p' => 'Description'
        ]
    ]
];
Html::fromArray($structure);

// Utilities
Html::escape('<script>');                 // "&lt;script&gt;"
Html::decode('&lt;script&gt;');          // "<script>"
Html::text('<b>Hello</b>');              // "Hello"
Html::minify('<div>   </div>');          // "<div></div>"
Html::doctype();                         // "<!DOCTYPE html>"

// Document generation
Html::document('My Page', '<h1>Hello</h1>', ['description' => 'Page description']);

// Extract elements
$html = '<div class="item">Content</div>';
Html::extract($html, '.item');
// Returns array of matched elements with html, text, and attributes
```

### Dates (`Date`)

Date and time utilities.

```php
use Marwa\Support\Date;

// Formatting
Date::format('2024-01-15');                       // "2024-01-15 00:00:00"
Date::format('2024-01-15', 'Y-m-d');             // "2024-01-15"
Date::format(1705276800, 'F j, Y');              // "January 15, 2024"

// Date arithmetic
Date::addDays('2024-01-15', 10);                 // DateTime object
Date::subDays('2024-01-15', 5);                  // DateTime object

// Comparison
Date::isAfter('2024-01-15', '2024-01-10');       // true
Date::isBefore('2024-01-15', '2024-01-20');     // true
Date::diffInDays('2024-01-20', '2024-01-15');   // 5

// Current time
Date::now();                                     // DateTime object
Date::timestamp();                               // int (current timestamp)
```

### Finder (`Finder`)

File system searching with fluent API.

```php
use Marwa\Support\Finder;

// Find files
$files = Finder::in(__DIR__ . '/src')
    ->files()
    ->name('/\.php$/');

// Find directories
$dirs = Finder::in(__DIR__ . '/src')
    ->directories();

// Filter by name pattern
$phpFiles = Finder::in(__DIR__)
    ->name('\.php$')
    ->files();

// Filter by size
$largeFiles = Finder::in(__DIR__)
    ->files()
    ->size('>', 1024 * 1024); // Files > 1MB

// Chain with Collection methods
$results = Finder::in(__DIR__ . '/src')
    ->files()
    ->name('/\.php$/')
    ->filter(fn($f) => $f->getSize() > 1000)
    ->map(fn($f) => $f->getFilename())
    ->all();
```

### Collection (`Collection`)

Array-backed collection with chainable methods.

```php
use Marwa\Support\Collection;

// Create
$collection = Collection::make([1, 2, 3, 4, 5]);
$collection = new Collection(['a' => 1, 'b' => 2]);

// Access
$collection->all();                // [1, 2, 3, 4, 5]
$collection->get('a', 'default');  // 1
$collection->first();             // 1
$collection->last();               // 5

// Modification
$collection->put('c', 3);
$collection->push(6);
$collection->first(fn($v) => $v > 2); // Returns first matching

// Transform
$collection->map(fn($v) => $v * 2); // Collection [2, 4, 6, 8, 10]
$collection->filter(fn($v) => $v % 2 === 0); // Collection [2, 4]
$collection->pluck('name');       // Collection of field values
$collection->pluck('name', 'id'); // Collection keyed by field
$collection->keys();              // Collection ['a', 'b', 'c']
$collection->values();            // Collection [1, 2, 3]
$collection->each(fn($v, $k) => print "$k: $v\n");

// Sorting
$sorted = $collection->sortBy(fn($v) => $v, true); // descending

// Grouping
$grouped = Collection::make([
    ['role' => 'admin', 'name' => 'Alice'],
    ['role' => 'user', 'name' => 'Bob'],
])->groupBy('role');

// Check
$collection->count();         // 5
$collection->isEmpty();       // false
$collection->isNotEmpty();    // true

// Output
$collection->toArray();
$collection->toJson();
(string) $collection; // auto-converts to JSON
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