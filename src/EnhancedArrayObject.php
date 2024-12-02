<?php

namespace Daedalus;

/**
 * Enhanced implementation of PHP's ArrayObject with type safety and event handling
 *
 * @package Daedalus
 */
class EnhancedArrayObject extends \ArrayObject
{
    /** @var string Type constraint for array elements */
    private string $type;
    
    /** @var array Array of event listeners */
    private array $eventListeners = [];

    /**
     * Constructor
     *
     * @param array  $array Initial array of elements
     * @param string $type  Class name for type constraint
     */
    public function __construct(array $array = [], string $type = '')
    {
        parent::__construct($array);
        $this->type = $type;
    }

    /**
     * Sets an element in the array
     *
     * @param mixed $key   The array key
     * @param mixed $value The value to set
     * @throws \InvalidArgumentException If type constraint is violated
     */
    public function offsetSet($key, $value): void
    {
        if (!empty($this->type) && !$value instanceof $this->type) {
            throw new \InvalidArgumentException("Value must be of type {$this->type}");
        }
        
        $exists = $this->offsetExists($key);
        $oldValue = $exists ? $this->offsetGet($key) : null;
        
        parent::offsetSet($key, $value);
        
        if ($exists) {
            $this->triggerEvent('modify', $key, $oldValue, $value);
        } else {
            $this->triggerEvent('add', $key, $value);
        }
    }

    /**
     * Removes an element from the array
     *
     * @param mixed $key The array key to unset
     */
    public function offsetUnset($key): void
    {
        parent::offsetUnset($key);
        $this->triggerEvent('remove', $key);
    }

    /**
     * Maps elements using a callback function
     *
     * @param callable $callback Function to apply to each element
     * @return self New instance with mapped elements
     */
    public function map(callable $callback): self
    {
        $array = array_map($callback, (array) $this);
        $this->triggerEvent('map');
        return new self($array, $this->type);
    }

    /**
     * Filters elements using a callback function
     *
     * @param callable $callback Function to filter elements
     * @return self New instance with filtered elements
     */
    public function filter(callable $callback): self
    {
        $array = array_filter((array) $this, $callback);
        $this->triggerEvent('filter');
        return new self($array, $this->type);
    }

    /**
     * Reduces array to a single value
     *
     * @param callable $callback Reduction function
     * @param mixed   $initial  Initial value
     * @return mixed The reduced value
     */
    public function reduce(callable $callback, $initial)
    {
        $result = array_reduce((array) $this, $callback, $initial);
        $this->triggerEvent('reduce');
        return $result;
    }

    /**
     * Creates an immutable version of this array
     *
     * @return ImmutableArrayObject
     */
    public function toImmutable(): ImmutableArrayObject
    {
        return new ImmutableArrayObject((array) $this, $this->type);
    }

    /**
     * Sorts the array
     *
     * @param callable|null $comparisonFunction Optional custom comparison function
     * @return self New instance with sorted elements
     */
    public function sort(callable $comparisonFunction = null): self {
        $array = (array) $this;
        if ($comparisonFunction)
    {
            usort($array, $comparisonFunction);
        } else {
            sort($array);
        }
        $this->triggerEvent('sort');
        return new self($array, $this->type);
    }

    /**
     * Searches for elements using a callback function
     *
     * @param callable $callback Search predicate
     * @return array Array of matching elements
     */
    public function search(callable $callback): array
    {
        $results = [];
        foreach ($this as $key => $value) {
            if ($callback($value)) {
                $results[$key] = $value;
            }
        }
        $this->triggerEvent('search');
        return $results;
    }
    
    /**
     * Inserts multiple elements at once
     *
     * @param array $elements Elements to insert
     */
    public function bulkInsert(array $elements): void
    {
        foreach ($elements as $element) {
            $this->offsetSet(null, $element);
        }
        $this->triggerEvent('bulkInsert');
    }

    /**
     * Adds an event listener for a specific event
     *
     * @param string   $event    Event name
     * @param callable $listener Listener callback
     */
    public function addEventListener(string $event, callable $listener): void
    {
        if (!isset($this->eventListeners[$event])) {
            $this->eventListeners[$event] = [];
        }
        $this->eventListeners[$event][] = $listener;
    }

    /**
     * Removes an event listener for a specific event
     *
     * @param string   $event    Event name
     * @param callable $listener Listener callback to remove
     * @return bool True if listener was removed, false if not found
     */
    public function removeEventListener(string $event, callable $listener): bool
    {
        if (!isset($this->eventListeners[$event])) {
            return false;
        }

        $key = array_search($listener, $this->eventListeners[$event], true);
        if ($key === false) {
            return false;
        }

        unset($this->eventListeners[$event][$key]);
        if (empty($this->eventListeners[$event])) {
            unset($this->eventListeners[$event]);
        }
        return true;
    }

    /**
     * Triggers event listeners for a specific event
     *
     * @param string $event Event name
     * @param mixed  ...$args Event arguments
     */
    private function triggerEvent(string $event, ...$args): void
    {
        if (isset($this->eventListeners[$event])) {
            foreach ($this->eventListeners[$event] as $listener) {
                $listener(...$args);
            }
        }
    }

    /**
     * Checks if the array is empty
     *
     * @return bool True if the array is empty, false otherwise
     */
    public function isEmpty(): bool
    {
        return count($this) === 0;
    }

    /**
     * Merges another array or ArrayObject into this one
     *
     * @param array|\ArrayObject $array Array to merge
     * @return self New instance with merged elements
     * @throws \InvalidArgumentException If type constraint is violated
     */
    public function merge($array): self
    {
        if ($array instanceof \ArrayObject) {
            $array = $array->getArrayCopy();
        }

        if (!is_array($array)) {
            throw new \InvalidArgumentException('Argument must be an array or ArrayObject');
        }

        if (!empty($this->type)) {
            foreach ($array as $value) {
                if (!$value instanceof $this->type) {
                    throw new \InvalidArgumentException("All values must be of type {$this->type}");
                }
            }
        }

        $merged = array_merge($this->getArrayCopy(), $array);
        $this->triggerEvent('merge');
        return new self($merged, $this->type);
    }

    /**
     * Creates a deep clone of the array object
     *
     * @return self New instance with cloned elements
     */
    public function cloneDeep(): self
    {
        $array = $this->getArrayCopy();
        $cloned = array_map(function ($item) {
            return is_object($item) && method_exists($item, '__clone')
                ? clone $item
                : $item;
        }, $array);

        $this->triggerEvent('clone');
        return new self($cloned, $this->type);
    }

    /**
     * Gets the current type constraint
     *
     * @return string Current type constraint
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * Sets the type constraint
     *
     * @param string $type New type constraint
     */
    public function setType(string $type): void
    {
        $this->type = $type;
    }

    public function __clone()
    {
        // Create a new array with cloned objects
        $array = $this->getArrayCopy();
        $cloned = array_map(function ($item) {
            return is_object($item) ? clone $item : $item;
        }, $array);
        
        // Create a new ArrayObject with cloned data
        $this->exchangeArray($cloned);
        
        // Clone event listeners
        $clonedListeners = [];
        foreach ($this->eventListeners as $event => $listeners) {
            $clonedListeners[$event] = $listeners;
        }
        $this->eventListeners = $clonedListeners;
    }
}
