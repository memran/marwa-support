<?php declare(strict_types=1);

require 'vendor/autoload.php';

use Marwa\Support\{File, Str, Arr, Obj, Date, Helper};

// String examples
$slug = Str::slug('Hello World!'); // "hello-world"
$limited = Str::limit('This is a long string', 10); // "This is a..."

// Array examples
$array = ['user' => ['name' => 'John', 'age' => 30]];
$name = Arr::get($array, 'user.name'); // "John"

// File system examples
File::put('test.txt', 'Hello World');
$content = File::get('test.txt'); // "Hello World"

// Object examples
$object = new \stdClass();
$object->user = new \stdClass();
$object->user->name = 'John';
$name = Obj::get($object, 'user.name'); // "John"

// Date examples
$now = Date::now();
$tomorrow = Date::addDays($now, 1);

// Helper examples
Helper::dd($slug, $limited, $name);

// Tap example
$result = Helper::tap($value, function($v) {
    Logger::debug('Current value', $v);
});

// Pipe example
$processed = Helper::pipe(
    ' hello ',
    [
        fn ($s) => trim($s),
        fn ($s) => strtoupper($s),
        fn ($s) => Str::slug($s)
    ]
);

// With example
$config = Helper::with(
    Helper::dataGet($settings, 'app.config'),
    fn ($c) => json_decode($c, true),
    ['default' => 'value']
);