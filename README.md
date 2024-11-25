# Enhanced Array Object

A PHP library that provides an enhanced version of PHP's ArrayObject with additional features like type safety, event handling, immutability options, and a PSR-11 compliant dependency injection container.

## Why Use Enhanced Array Object?

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

The library combines the power of PHP's ArrayObject with modern programming practices like immutability, type safety, and event-driven architecture, making it a valuable tool for building robust and maintainable applications.

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

## Installation

```bash
composer require daedalus/enhanced-array-object
```

## Usage

### Basic Usage

```php
use Daedalus\EnhancedArrayObject;

// Create a basic array object
$array = new EnhancedArrayObject([1, 2, 3]);

// Add elements
$array->offsetSet(null, 4); // Appends 4
$array[5] = 6; // Array access syntax

// Remove elements
$array->offsetUnset(0);
unset($array[1]); // Array access syntax
```

### Type Safety

```php
use Daedalus\EnhancedArrayObject;

class User {
    public function __construct(public string $name) {}
}

// Create a typed array that only accepts User objects
$users = new EnhancedArrayObject([], User::class);

// This works
$users[] = new User("John");

// This throws InvalidArgumentException
$users[] = "Not a user object";
```

### Event Handling

```php
use Daedalus\EnhancedArrayObject;

$array = new EnhancedArrayObject([1, 2, 3]);

// Add event listener for modifications
$array->addEventListener('set', function($key, $value) {
    echo "Element set at key $key with value $value\n";
});

$array[] = 4; // Triggers event: "Element set at key 3 with value 4"

// Remove event listener
$listener = function($key) {
    echo "Element removed at key $key\n";
};
$array->addEventListener('unset', $listener);
unset($array[0]); // Triggers event

// Remove the listener when no longer needed
$array->removeEventListener('unset', $listener);
```

### Array Operations

```php
use Daedalus\EnhancedArrayObject;

$array = new EnhancedArrayObject([1, 2, 3, 4, 5]);

// Mapping
$doubled = $array->map(fn($n) => $n * 2);
// Result: [2, 4, 6, 8, 10]

// Filtering
$evens = $array->filter(fn($n) => $n % 2 === 0);
// Result: [2, 4]

// Reducing
$sum = $array->reduce(fn($carry, $n) => $carry + $n, 0);
// Result: 15

// Merging arrays
$other = new EnhancedArrayObject([6, 7, 8]);
$merged = $array->merge($other);
// Result: [1, 2, 3, 4, 5, 6, 7, 8]

// Check if empty
if ($array->isEmpty()) {
    echo "Array is empty\n";
}

// Deep cloning
$clone = $array->cloneDeep();
```

### Immutable Arrays

```php
use Daedalus\EnhancedArrayObject;
use Daedalus\ImmutableArrayObject;

// Create a mutable array
$mutable = new EnhancedArrayObject([1, 2, 3]);

// Convert to immutable
$immutable = $mutable->toImmutable();

// These will throw LogicException
$immutable[] = 4;
unset($immutable[0]);
```

## Container Usage

The library includes a powerful PSR-11 compliant container with singleton management and automatic dependency injection:

```php
use Daedalus\ContainerArrayObject;

// Create a container
$container = new ContainerArrayObject();

// 1. Basic Value Binding
$container->bind('app.name', 'My Application');
$container->bind('app.config', [
    'api_key' => 'secret_key',
    'cache_ttl' => 3600
]);

// 2. Class Registration with Dependencies
class Database {
    public function __construct(
        private string $host,
        private string $username
    ) {}
}

class Logger {
    private $logFile;
    
    public function __construct(string $logFile = 'app.log') {
        $this->logFile = $logFile;
    }
}

// Register singleton with factory function
$container->singleton('database', function($container) {
    return new Database('localhost', 'root');
});

// Register class for auto-instantiation
$container->bind(Logger::class, Logger::class);

// 3. Automatic Dependency Injection
class UserRepository {
    public function __construct(
        private Database $database,
        private Logger $logger
    ) {}
}

// Container will automatically resolve dependencies
$container->bind(UserRepository::class, UserRepository::class);
$repo = $container->get(UserRepository::class);

// 4. Interface Binding
interface PaymentGateway {
    public function process(float $amount): bool;
}

class StripeGateway implements PaymentGateway {
    public function process(float $amount): bool {
        return true;
    }
}

// Bind interface to implementation
$container->bind(PaymentGateway::class, StripeGateway::class);

// 5. Instance Management
if ($container->has('database')) {
    $db = $container->get('database');
}

// Clear cached instances
$container->clearInstances();
```

Key Container Features:
- PSR-11 compliance
- Singleton management
- Automatic dependency injection
- Factory function support
- Interface binding
- Value binding
- Instance lifecycle management
- Fluent interface

## Advanced Usage

### Combining Features

```php
use Daedalus\EnhancedArrayObject;

class Product {
    public function __construct(
        public string $name,
        public float $price
    ) {}
}

// Create a typed array with event handling
$products = new EnhancedArrayObject([], Product::class);

// Add event listeners
$products->addEventListener('set', function($key, $value) {
    echo "Added product: {$value->name} at \${$value->price}\n";
});

// Add products
$products[] = new Product("Widget", 9.99);
$products[] = new Product("Gadget", 19.99);

// Filter expensive products
$expensive = $products->filter(fn($p) => $p->price > 10);

// Calculate total value
$total = $products->reduce(fn($sum, $p) => $sum + $p->price, 0);
```

## Error Handling

The library throws the following exceptions:

- `InvalidArgumentException`: When type constraints are violated
- `LogicException`: When attempting to modify immutable arrays

## Contributing

Contributions are welcome! Please feel free to submit a Pull Request.

## License

This library is licensed under the MIT License - see the LICENSE file for details.
