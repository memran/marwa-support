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
String Manipulation: Laravel-style string helpers
Array Utilities: Dot notation access, filtering, and more
File System: Simplified file operations
Object Helpers: Object property access and manipulation
Date Handling: Easy date formatting and calculations
Helper Functions: Convenient utility functions

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
```
