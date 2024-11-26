<?php

namespace Daedalus;

use Exception;
use Countable;
use Iterator;
use Serializable;

/**
 * Set implementation with Iterator, Countable, and Serializable interfaces
 * 
 * Provides a collection of unique values with standard set operations like union,
 * intersection, and difference.
 * 
 * @package Daedalus
 */
class Set implements Iterator, Countable, Serializable
{
    /** @var array Storage for set items */
    private array $items = [];

    /** @var int Current position for iteration */
    private int $position = 0;

    /**
     * Constructor
     * 
     * @param array $items Initial items for the set
     */
    public function __construct(array $items = [])
    {
        foreach ($items as $item) {
            $this->add($item);
        }
    }

    /**
     * Adds an item to the set
     * 
     * @param mixed $item The item to add
     * @return bool True if the item was added, false if it already existed
     */
    public function add(mixed $item): bool
    {
        $hash = $this->hash($item);
        if (!isset($this->items[$hash])) {
            $this->items[$hash] = $item;
            return true;
        }
        return false;
    }

    /**
     * Removes an item from the set
     * 
     * @param mixed $item The item to remove
     * @return bool True if the item was removed, false if it didn't exist
     */
    public function remove(mixed $item): bool
    {
        $hash = $this->hash($item);
        if (isset($this->items[$hash])) {
            unset($this->items[$hash]);
            return true;
        }
        return false;
    }

    /**
     * Checks if an item exists in the set
     * 
     * @param mixed $item The item to check
     * @return bool True if the item exists, false otherwise
     */
    public function contains(mixed $item): bool
    {
        return isset($this->items[$this->hash($item)]);
    }

    /**
     * Returns the union of this set with another set
     * 
     * @param Set $other The other set
     * @return Set A new set containing all items from both sets
     */
    public function union(Set $other): Set
    {
        $result = new Set();
        foreach ($this->items as $item) {
            $result->add($item);
        }
        foreach ($other->toArray() as $item) {
            $result->add($item);
        }
        return $result;
    }

    /**
     * Returns the intersection of this set with another set
     * 
     * @param Set $other The other set
     * @return Set A new set containing items present in both sets
     */
    public function intersection(Set $other): Set
    {
        $result = new Set();
        foreach ($this->items as $item) {
            if ($other->contains($item)) {
                $result->add($item);
            }
        }
        return $result;
    }

    /**
     * Returns the difference of this set with another set
     * 
     * @param Set $other The other set
     * @return Set A new set containing items present in this set but not in the other
     */
    public function difference(Set $other): Set
    {
        $result = new Set();
        foreach ($this->items as $item) {
            if (!$other->contains($item)) {
                $result->add($item);
            }
        }
        return $result;
    }

    /**
     * Checks if this set is a subset of another set
     * 
     * @param Set $other The other set
     * @return bool True if this set is a subset of the other set
     */
    public function isSubsetOf(Set $other): bool
    {
        foreach ($this->items as $item) {
            if (!$other->contains($item)) {
                return false;
            }
        }
        return true;
    }

    /**
     * Converts the set to an array
     * 
     * @return array Array containing all items in the set
     */
    public function toArray(): array
    {
        return array_values($this->items);
    }

    /**
     * Clears all items from the set
     */
    public function clear(): void
    {
        $this->items = [];
    }

    /**
     * Gets the current value during iteration
     * 
     * @return mixed The current value
     */
    public function current(): mixed
    {
        return array_values($this->items)[$this->position];
    }

    /**
     * Gets the current key during iteration
     * 
     * @return int The current position
     */
    public function key(): int
    {
        return $this->position;
    }

    /**
     * Moves to the next position in iteration
     */
    public function next(): void
    {
        ++$this->position;
    }

    /**
     * Rewinds the iterator to the beginning
     */
    public function rewind(): void
    {
        $this->position = 0;
    }

    /**
     * Checks if the current position is valid
     * 
     * @return bool True if the position is valid, false otherwise
     */
    public function valid(): bool
    {
        return isset(array_values($this->items)[$this->position]);
    }

    /**
     * Gets the count of items in the set
     * 
     * @return int The number of items
     */
    public function count(): int
    {
        return count($this->items);
    }

    /**
     * Serializes the set to a string
     * 
     * @return string The serialized set
     */
    public function serialize(): string
    {
        return serialize($this->items);
    }

    /**
     * Unserializes a string back into a set
     * 
     * @param string $data The serialized set data
     */
    public function unserialize(string $data): void
    {
        $this->items = unserialize($data);
    }

    /**
     * Generates a hash for an item
     * 
     * @param mixed $item The item to hash
     * @return string The hash value
     */
    private function hash(mixed $item): string
    {
        if (is_object($item)) {
            return spl_object_hash($item);
        }
        return md5(serialize($item));
    }
}
