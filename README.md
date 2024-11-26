# Daedalus

[![PHPUnit Tests](https://github.com/cmatosbc/daedalus/actions/workflows/phpunit.yml/badge.svg)](https://github.com/cmatosbc/daedalus/actions/workflows/phpunit.yml) [![PHP Composer](https://github.com/cmatosbc/daedalus/actions/workflows/composer.yml/badge.svg)](https://github.com/cmatosbc/daedalus/actions/workflows/composer.yml)

Daedalus is a powerful PHP library that provides advanced data structures and utilities for modern PHP applications. At its core, it offers an enhanced version of PHP's ArrayObject with additional features like type safety, event handling, immutability options, and a PSR-11 compliant dependency injection container.

The library is designed with a focus on type safety, immutability, and event-driven architecture, making it an ideal choice for building robust and maintainable applications.

License: [GNU General Public License v3.0](https://www.gnu.org/licenses/gpl-3.0.en.html)

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
- C# like Dictionary class

## Installation

```bash
composer require cmatosbc/daedalus
```

## Data Structures

### Dictionary

The `Dictionary` class provides a robust key-value collection with type safety and iteration capabilities. It implements `Iterator`, `Countable`, and `Serializable` interfaces, offering a comprehensive solution for managing key-value pairs.

#### Real-World Use Cases

1. Configuration Management
   - Store application settings with typed values
   - Manage environment-specific configurations
   - Handle feature flags and toggles

2. HTTP Headers Management
   - Store and manipulate HTTP headers
   - Handle custom header mappings
   - Normalize header names and values

3. User Session Data
   - Store user preferences
   - Manage shopping cart items
   - Cache user-specific settings

4. Language Localization
   - Map language keys to translations
   - Handle multiple locale configurations
   - Store message templates

5. Cache Implementation
   - Store computed results with unique keys
   - Implement memory-efficient caching
   - Manage cache invalidation

```php
use Daedalus\Dictionary;

// Create a new dictionary
$dict = new Dictionary();

// Add key-value pairs
$dict->add('name', 'John');
$dict->add('age', 30);

// Get values
$name = $dict->get('name'); // Returns 'John'

// Check if key exists
if ($dict->containsKey('age')) {
    // Update value
    $dict->update('age', 31);
}

// Remove a key
$dict->remove('name');

// Get all keys or values
$keys = $dict->keys();    // Returns ['age']
$values = $dict->values(); // Returns [31]

// Iterate over dictionary
foreach ($dict as $key => $value) {
    echo "$key: $value\n";
}

// Clear all items
$dict->clear();
```

### Set

The `Set` class implements a collection of unique values with standard set operations. It provides methods for union, intersection, difference, and subset operations, making it ideal for mathematical set operations and managing unique collections.

#### Real-World Use Cases

1. User Permissions and Roles
   - Store user capabilities
   - Manage role assignments
   - Calculate effective permissions through set operations
   - Check required permission sets

2. Social Network Connections
   - Manage friend lists
   - Find mutual friends (intersection)
   - Suggest new connections (difference)
   - Track group memberships

3. Product Categories and Tags
   - Manage product classifications
   - Handle multiple category assignments
   - Find products with common tags
   - Calculate related products

4. Event Management
   - Track event attendees
   - Manage waiting lists
   - Find common participants between events
   - Handle group registrations

5. Data Deduplication
   - Remove duplicate records
   - Track unique visitors
   - Manage email subscription lists
   - Handle unique identifier collections

```php
use Daedalus\Set;

// Create new sets
$set1 = new Set([1, 2, 3]);
$set2 = new Set([2, 3, 4]);

// Add and remove items
$set1->add(5);      // Returns true (added)
$set1->add(1);      // Returns false (already exists)
$set1->remove(1);   // Returns true (removed)
$set1->remove(10);  // Returns false (didn't exist)

// Check for existence
$exists = $set1->contains(2); // Returns true

// Set operations
$union = $set1->union($set2);        // {2, 3, 4, 5}
$intersection = $set1->intersection($set2); // {2, 3}
$difference = $set1->difference($set2);     // {5}

// Check subset relationship
$isSubset = $set1->isSubsetOf($set2); // Returns false

// Convert to array
$array = $set1->toArray(); // [2, 3, 5]

// Iterate over set
foreach ($set1 as $item) {
    echo $item . "\n";
}

// Clear all items
$set1->clear();
```

The Set class features:
- Unique value storage
- Standard set operations (union, intersection, difference)
- Subset checking
- Object and scalar value support
- Iterator implementation for foreach loops
- Serialization support

### DisjointSet

The `DisjointSet` class extends `Set` to provide an efficient implementation of disjoint sets (union-find data structure) with path compression and union by rank optimizations. It's particularly useful for managing non-overlapping groups and determining connectivity between elements.

#### Real-World Use Cases

1. Social Network Analysis
   - Track friend groups and communities
   - Detect connected components in social graphs
   - Analyze information spread patterns
   - Identify isolated user clusters

2. Network Infrastructure
   - Monitor network connectivity
   - Detect network partitions
   - Manage redundant connections
   - Track service dependencies

3. Image Processing
   - Connected component labeling
   - Region segmentation
   - Object detection
   - Pixel clustering

4. Game Development
   - Track team/alliance memberships
   - Manage territory control
   - Handle resource ownership
   - Implement faction systems

5. Distributed Systems
   - Partition management
   - Cluster state tracking
   - Service discovery
   - Consensus group management

```php
use Daedalus\DisjointSet;

// Create a disjoint set
$ds = new DisjointSet();

// Create individual sets
$ds->makeSet("A");
$ds->makeSet("B");
$ds->makeSet("C");
$ds->makeSet("D");

// Join sets together
$ds->union("A", "B"); // Now A and B are in the same set
$ds->union("C", "D"); // Now C and D are in the same set
$ds->union("B", "C"); // Now all elements are in the same set

// Check if elements are connected
$connected = $ds->connected("A", "D"); // Returns true

// Find the representative element of a set
$rep = $ds->find("B"); // Returns the set's representative

// Get all elements in the same set
$set = $ds->getSet("A"); // Returns ["A", "B", "C", "D"]

// Count number of disjoint sets
$count = $ds->countSets(); // Returns 1

// Clear all sets
$ds->clear();
```

The DisjointSet class features:
- Efficient union and find operations (O(α(n)), where α is the inverse Ackermann function)
- Path compression optimization
- Union by rank optimization
- Set connectivity checking
- Set membership queries
- Multiple set management

### HashSet

The `HashSet` class extends `Set` to provide constant-time performance for basic operations. It uses hash-based storage with no guarantee of iteration order, similar to Java's HashSet.

#### Real-World Use Cases

1. Caching Systems
   - Store cache keys
   - Track recently accessed items
   - Manage unique identifiers
   - Handle session tokens

2. Data Validation
   - Track processed records
   - Validate unique entries
   - Filter duplicate submissions
   - Check for existing values

3. Analytics and Tracking
   - Track unique visitors
   - Monitor unique events
   - Store distinct metrics
   - Log unique errors

```php
use Daedalus\HashSet;

// Create a new HashSet with custom load factor and capacity
$set = new HashSet([], 0.75, 16);

// Add elements
$set->add("apple");
$set->add("banana");
$set->add("apple"); // Returns false (already exists)

// Bulk operations with another set
$otherSet = new HashSet(["banana", "cherry"]);
$set->addAll($otherSet);    // Add all elements from otherSet
$set->removeAll($otherSet); // Remove all elements that exist in otherSet
$set->retainAll($otherSet); // Keep only elements that exist in otherSet

// Check contents
$exists = $set->contains("apple"); // Returns true
$count = $set->count();           // Get number of elements

// Convert to array (no guaranteed order)
$array = $set->toArray();

// Clear the set
$set->clear();
```

### TreeSet

The `TreeSet` class implements a self-balancing binary search tree (AVL tree) that maintains elements in sorted order, similar to Java's TreeSet.

#### Real-World Use Cases

1. Ranking Systems
   - Leaderboard management
   - Score tracking
   - Priority queues
   - Tournament rankings

2. Time-based Operations
   - Event scheduling
   - Task prioritization
   - Deadline management
   - Log entry ordering

3. Range Queries
   - Price range searches
   - Date range filtering
   - Numeric range queries
   - Version management

```php
use Daedalus\TreeSet;

// Create a new TreeSet
$set = new TreeSet([3, 1, 4, 1, 5]); // Duplicates are automatically removed

// Add elements (maintains order)
$set->add(2);
$set->add(6);

// Access ordered elements
$first = $set->first(); // Get smallest element (1)
$last = $set->last();   // Get largest element (6)

// Find adjacent elements
$lower = $set->lower(4);  // Get largest element < 4 (3)
$higher = $set->higher(4); // Get smallest element > 4 (5)

// Check contents
$exists = $set->contains(3); // Returns true
$count = $set->count();      // Get number of elements

// Convert to sorted array
$array = $set->toArray(); // [1, 2, 3, 4, 5, 6]

// Iterate in order
foreach ($set as $element) {
    echo $element . "\n";
}

// Clear the set
$set->clear();
```

The TreeSet class features:
- Ordered element storage
- Logarithmic-time operations (O(log n))
- Efficient range operations
- Natural ordering for scalar types
- Custom ordering via __toString for objects
- AVL tree self-balancing

### Enhanced Array Object

A PHP library that provides an enhanced version of PHP's ArrayObject with additional features like type safety, event handling, immutability options, and a PSR-11 compliant dependency injection container.

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

### Container Array Object

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

This library is licensed under the GNU General Public License v3.0 - see the LICENSE file for details.
