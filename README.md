# Marwa Support Library

[![Latest Version](https://img.shields.io/packagist/v/memran/marwa-support.svg?style=flat-square)](https://packagist.org/packages/memran/marwa-support)
[![Total Downloads](https://img.shields.io/packagist/dt/memran/marwa-support.svg?style=flat-square)](https://packagist.org/packages/memran/marwa-support)
[![License](https://img.shields.io/packagist/l/memran/marwa-support.svg?style=flat-square)](https://packagist.org/packages/memran/marwa-support)
[![PHP Version](https://img.shields.io/packagist/php-v/memran/marwa-support.svg?style=flat-square)](https://php.net)

A Laravel-inspired PHP utility library with expressive syntax for common operations.

## Installation

```bash
composer require memran/marwa-support
```
## Features
- **String Manipulation**: Laravel-style string helpers
- **Array Utilities**: Dot notation access, filtering, and more
- **File System**: Simplified file operations
- **Object Helpers**: Object property access and manipulation
- **Date Handling**: Easy date formatting and calculations
- **Helper Functions**: Convenient utility functions

## Namespace
All classes are under the Marwa\Support namespace.

## Usage

### String Operations

```bash 
use Marwa\Support\Str;

// Convert to slug
$slug = Str::slug('Hello World!'); // "hello-world"

// Limit string length
$limited = Str::limit('This is a long string', 10); // "This is a..."

// Convert to camelCase
$camel = Str::camel('hello_world'); // "helloWorld"

// New pattern matching
Str::is('*.php', 'test.php'); // true

// String conversion
Str::toBytes('2MB'); // 2097152

// HTML handling
Str::escape('<script>alert()</script>'); // &lt;script&gt;alert()&lt;/script&gt;

// Advanced string extraction
Str::between('[tag]content[/tag]', '[tag]', '[/tag]'); // 'content'

```

# Array Operations

```bash
use Marwa\Support\Arr;

$array = ['user' => ['name' => 'John', 'age' => 30]];

// Get value using dot notation
$name = Arr::get($array, 'user.name'); // "John"

// Check if key exists
$exists = Arr::has($array, 'user.email'); // false

// Get only specified keys
$filtered = Arr::only($array, ['user.name']); // ['user' => ['name' => 'John']]
```

# File System Operations
```bash
use Marwa\Support\File;

// Write to file
File::put('test.txt', 'Hello World');

// Read from file
$content = File::get('test.txt'); // "Hello World"

// Check if file exists
$exists = File::exists('test.txt'); // true
```

# Object Operation

```bash
use Marwa\Support\Obj;

$object = new \stdClass();
$object->user = new \stdClass();
$object->user->name = 'John';

// Get property using dot notation
$name = Obj::get($object, 'user.name'); // "John"

// Convert object to array
$array = Obj::toArray($object); // ['user' => ['name' => 'John']]
```

# Date Operation
```bash
use Marwa\Support\Date;

// Format date
$formatted = Date::format('2023-01-01', 'F j, Y'); // "January 1, 2023"

// Add days to date
$tomorrow = Date::addDays(Date::now(), 1);

// Check if date is in future
$isFuture = Date::isAfter('2023-12-31', Date::now());
```

# Helper
```bash
use Marwa\Support\Helper;

// Dump and die
Helper::dd($variable);

// Get nested data
$value = Helper::dataGet($target, 'nested.key', 'default');

// Retry operation
$result = Helper::retry(3, function() {
    // Operation that might fail
});
```
# HTML

```bash
// Generate a link
echo Html::link('https://example.com', 'Visit Example');

// Generate a form
echo Html::form('/submit', 'POST', [
    'class' => 'my-form'
]);

// Parse HTML
$results = Html::extract($html, 'div.user > span.name');

// Generate complete document
echo Html::document('My Page', '<h1>Hello World</h1>', [
    'description' => 'A test page'
]);

```

# Collection

```bash
// Collection usage
$collection = Collection::make([1, 2, 3, 4, 5])
    ->filter(fn($n) => $n > 2)
    ->map(fn($n) => $n * 2)
    ->values();
```

# Finder

```bash
// Finder with collection methods
$files = Finder::in('/app')
    ->name('.*\.php$')
    ->files()
    ->map(fn($file) => $file->getPathname())
    ->sortBy(fn($path) => $path)
    ->values();
```
# Validation Example

```bash
// Basic validation
$data = [
    'name' => 'John Doe',
    'email' => 'john@example.com',
    'password' => 'secret',
    'password_confirmation' => 'secret',
    'age' => 25
];

$rules = [
    'name' => 'required|string|min:3|max:255',
    'email' => 'required|email',
    'password' => 'required|min:6|same:password_confirmation',
    'age' => 'required|numeric|min:18'
];

$validator = Validation::make($data, $rules);

if ($validator->fails()) {
    $errors = $validator->errors();
    // Handle errors
} else {
    // Validation passed
}

// Custom error messages
$messages = [
    'email.required' => 'We need your email address!',
    'password.min' => 'Password must be at least 6 characters long!'
];

$validator = Validation::make($data, $rules, $messages);

// Custom validation rule
$validator->extend('uppercase', function($field, $value, $parameters) {
    return strtoupper($value) === $value;
});

$rules = ['name' => 'required|uppercase'];
```

# Security Example

```bash
// Password handling
$hash = Security::passwordHash('secret123');
$isValid = Security::passwordVerify('secret123', $hash);

// Encryption
$encrypted = Security::encrypt('sensitive data', 'your-secret-key');
$decrypted = Security::decrypt($encrypted, 'your-secret-key');

// CSRF protection
$token = Security::csrfToken();
// In form: <input type="hidden" name="csrf_token" value="<?= $token ?>">

if ($_POST) {
    $isValid = Security::verifyCsrfToken($_POST['csrf_token']);
}

// Sanitization
$clean = Security::sanitize($_POST['input'], 'string');
$isEmail = Security::validate($_POST['email'], 'email');

// XSS prevention
$safeHtml = Security::xssClean('<script>alert("xss")</script>');

// File upload safety
$safeName = Security::safeFileName('malicious file.php.jpg');

```

## Available Classe
| Class | Description |
| :------- | :------: |
| Marwa\Support\Str  | String manipulation utilities |
| Marwa\Support\Arr  | Array utilities with dot notation support |
| Marwa\Support\FileSystem	| File system operations |
| Marwa\Support\Obj	| Object utilities |
| Marwa\Support\Date	| Date and time helpers |
| Marwa\Support\Helper	| Miscellaneous utility functions |

### Testing
> composer test

### Security
If you discover any security related issues, please use the issue tracker.

### Contributing
Pull requests are welcome. For major changes, please open an issue first to discuss what you would like to change.

## License
📜 [MIT License](LICENSE) - See the full license text

## Author
👤 **Mohammad Emran**  
- GitHub: [@memran](https://github.com/memran)  

## Package Links
- 🔗 [Package on Packagist](https://packagist.org/packages/memran/marwa-support)
- 📦 [Source Code on GitHub](https://github.com/memran/marwa-support)

