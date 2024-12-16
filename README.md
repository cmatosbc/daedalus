# Daedalus

[![PHPUnit Tests](https://github.com/cmatosbc/daedalus/actions/workflows/phpunit.yml/badge.svg)](https://github.com/cmatosbc/daedalus/actions/workflows/phpunit.yml) [![PHP Composer](https://github.com/cmatosbc/daedalus/actions/workflows/composer.yml/badge.svg)](https://github.com/cmatosbc/daedalus/actions/workflows/composer.yml) [![PHP Lint](https://github.com/cmatosbc/daedalus/actions/workflows/lint.yml/badge.svg)](https://github.com/cmatosbc/daedalus/actions/workflows/lint.yml)

## Quick Navigation
- [Overview](#overview)
- [Installation](#installation)
- [Features](#features)
- [Data Structures](#data-structures)
  - [Dictionary](#dictionary)
  - [MultiMap](#multimap)
  - [SortedMap](#sortedmap)
  - [Set](#set)
  - [DisjointSet](#disjointset)
  - [HashSet](#hashset)
  - [TreeSet](#treeset)
  - [Matrix](#matrix)
  - [Enhanced Array Object](#enhanced-array-object)
- [Use Cases](#why-use-daedalus)
  - [E-commerce Product Catalogs](#1-e-commerce-product-catalogs)
  - [User Management Systems](#2-user-management-systems)
  - [Financial Applications](#3-financial-applications)
  - [Content Management Systems](#4-content-management-systems)
  - [API Response Handling](#5-api-response-handling)
  - [Configuration Management](#6-configuration-management)
- [Advanced Usage](#advanced-usage)
- [Error Handling](#error-handling)
- [Contributing](#contributing)
- [License](#license)

## Overview

Daedalus is a powerful PHP library that provides advanced data structures and utilities for modern PHP applications. At its core, it offers an enhanced version of PHP's ArrayObject with additional features like type safety, event handling, immutability options, and a PSR-11 compliant dependency injection container.

The library is designed with a focus on type safety, immutability, and event-driven architecture, making it an ideal choice for building robust and maintainable applications.

## Installation

```bash
composer require cmatosbc/daedalus
```

## Features

- Type safety for array elements
- Event handling for array modifications
- Immutable array support
- Array manipulation methods (map, filter, reduce)
- Deep cloning capability
- Array merging with type checking
- Event listener management
- PSR-11 compliant dependency injection container
- Singleton pattern support
- Automatic dependency resolution

## Why Use Daedalus?

This library is particularly useful in scenarios where you need robust array handling with type safety and change tracking. Here are some real-world use cases:

### 1. E-commerce Product Catalogs
- Maintain type-safe collections of products
- Track changes to inventory in real-time using event listeners
- Filter and transform product lists efficiently
- Ensure data integrity with immutable product records

### 2. User Management Systems
- Manage collections of user objects with type enforcement
- Track user list modifications for audit logging
- Filter users based on roles or permissions
- Safely share user lists across system components

### 3. Financial Applications
- Maintain immutable transaction records
- Track changes to financial data with event listeners
- Perform calculations on collections of financial entries
- Ensure data consistency with type constraints

### 4. Content Management Systems
- Manage collections of different content types
- Track content modifications with event listeners
- Filter and transform content collections
- Maintain immutable content versions

### 5. API Response Handling
- Transform API responses into typed collections
- Cache immutable response data
- Filter and map response data efficiently
- Track data access patterns with events

### 6. Configuration Management
- Store immutable configuration settings
- Track configuration changes with events
- Maintain type-safe option collections
- Merge configuration from multiple sources

## Data Structures

### Dictionary
A type-safe key-value collection with:
- Strict type enforcement
- Iteration capabilities
- Serialization support
- Ideal for configurations and mappings

Example:
```php
use Daedalus\Dictionary;

$dict = new Dictionary();
$dict->add('name', 'John');
$dict->add('age', 30);

$name = $dict->get('name'); // Returns 'John'
```

### MultiMap
A map that allows multiple values per key:
- Store multiple values for a single key
- Maintain insertion order
- Type-safe value collections
- Ideal for tags, categories, and relationships

Example:
```php
use Daedalus\MultiMap;

$tags = new MultiMap();
$tags->add('article1', 'php');
$tags->add('article1', 'programming');
$tags->get('article1'); // ['php', 'programming']

// Bulk operations
$tags->addAll('article2', ['web', 'tutorial']);
$tags->remove('article1', 'php');
$tags->removeAll('article2');

// Check contents
$hasTag = $tags->contains('article1', 'programming'); // true
$count = $tags->count(); // Total number of key-value pairs
```

### SortedMap
A map that maintains its keys in sorted order:
- Automatic key sorting
- Customizable sort order
- Type-safe values
- Efficient range operations

Example:
```php
use Daedalus\SortedMap;

$scores = new SortedMap();
$scores['alice'] = 95;
$scores['bob'] = 87;
$scores['carol'] = 92;

// Keys are automatically sorted
foreach ($scores as $name => $score) {
    echo "$name: $score\n";
} // Outputs in alphabetical order

// Range operations
$topScores = $scores->subMap('alice', 'bob'); // Get scores from alice to bob
$highScores = $scores->tailMap(90); // Get all scores >= 90
$lowScores = $scores->headMap(90);  // Get all scores < 90

// Find adjacent entries
$nextStudent = $scores->higherKey('bob');   // 'carol'
$prevStudent = $scores->lowerKey('carol');  // 'bob'
```

### Set
Collections of unique values with:
- Set operations (union, intersection)
- Order maintenance (TreeSet)
- Type safety
- Efficient lookups

Example:
```php
use Daedalus\Set;

$set1 = new Set([1, 2, 3]);
$set2 = new Set([2, 3, 4]);

$union = $set1->union($set2);        // {1, 2, 3, 4}
$intersection = $set1->intersection($set2); // {2, 3}
```

### DisjointSet
A specialized set implementation for managing non-overlapping groups:
- Efficient union-find operations
- Path compression optimization
- Group membership tracking
- Connected component analysis

### HashSet
An unordered set implementation with constant-time operations:
- O(1) add/remove/contains operations
- No order guarantee
- Memory-efficient storage
- High performance for large datasets

### TreeSet
An ordered set implementation using a self-balancing tree:
- Maintains elements in sorted order
- O(log n) operations
- Range query support
- Ordered iteration

### Matrix
A 2D array implementation with:
- Row and column operations
- Mathematical operations
- Type-safe elements
- Efficient traversal

Example:
```php
use Daedalus\Matrix;

$matrix = new Matrix([
    [1, 2],
    [3, 4]
]);

$transposed = $matrix->transpose();
```

### Enhanced Array Object
An improved version of PHP's ArrayObject with:
- Type safety for elements
- Event handling for modifications
- Array manipulation (map, filter, reduce)
- Deep cloning and merging

Example:
```php
use Daedalus\EnhancedArrayObject;

$array = new EnhancedArrayObject([1, 2, 3]);
$array->addEventListener('set', function($key, $value) {
    echo "Element set at key $key with value $value\n";
});

$doubled = $array->map(fn($n) => $n * 2);
```

## Advanced Usage

### Type Safety
```php
use Daedalus\EnhancedArrayObject;

class User {
    public function __construct(public string $name) {}
}

$users = new EnhancedArrayObject([], User::class);
$users[] = new User("John"); // Works
$users[] = "Not a user";     // Throws InvalidArgumentException
```

### Event Handling
```php
$array = new EnhancedArrayObject([1, 2, 3]);
$array->addEventListener('set', function($key, $value) {
    echo "Value changed at $key to $value\n";
});
```

## Error Handling

The library throws the following exceptions:
- `InvalidArgumentException`: When type constraints are violated
- `LogicException`: When attempting to modify immutable arrays

## Contributing

We welcome contributions! Please feel free to submit a Pull Request.

## License

This library is licensed under the GNU General Public License v3.0 - see the LICENSE file for details.
