<?php

require 'vender/autoload.php';

use Support\{FileSystem, Str, Arr, Obj, Date, Helper};

// String examples
$slug = Str::slug('Hello World!'); // "hello-world"
$limited = Str::limit('This is a long string', 10); // "This is a..."

// Array examples
$array = ['user' => ['name' => 'John', 'age' => 30]];
$name = Arr::get($array, 'user.name'); // "John"

// File system examples
FileSystem::put('test.txt', 'Hello World');
$content = FileSystem::get('test.txt'); // "Hello World"

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