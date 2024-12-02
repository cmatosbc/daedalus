<?php

namespace Daedalus;

use Exception;
use Serializable;
use Countable;
use Iterator;

/**
 * Dictionary implementation with Iterator, Countable, and Serializable interfaces
 *
 * Provides a key-value collection with standard dictionary operations and iteration capabilities.
 *
 * @package Daedalus
 */
class Dictionary implements Iterator, Countable, Serializable
{
    /** @var array Storage for dictionary items */
    private $items = [];

    /** @var int Current position for iteration */
    private $position = 0;

    /**
     * Adds a key-value pair to the dictionary
     *
     * @param mixed $key   The key to add
     * @param mixed $value The value to associate with the key
     * @throws Exception If the key already exists
     */
    public function add($key, $value)
    {
        if ($this->containsKey($key)) {
            throw new Exception("Key already exists.");
        }
        $this->items[$key] = $value;
    }

    /**
     * Gets a value by its key
     *
     * @param mixed $key The key to look up
     * @return mixed The value associated with the key
     * @throws Exception If the key doesn't exist
     */
    public function get($key)
    {
        if (!$this->containsKey($key)) {
            throw new Exception("Key not found.");
        }
        return $this->items[$key];
    }

    /**
     * Removes a key-value pair from the dictionary
     *
     * @param mixed $key The key to remove
     * @throws Exception If the key doesn't exist
     */
    public function remove($key)
    {
        if (!$this->containsKey($key)) {
            throw new Exception("Key not found.");
        }
        unset($this->items[$key]);
    }

    /**
     * Updates a value for an existing key
     *
     * @param mixed $key   The key to update
     * @param mixed $value The new value
     * @throws Exception If the key doesn't exist
     */
    public function update($key, $value)
    {
        if (!$this->containsKey($key)) {
            throw new Exception("Key not found.");
        }
        $this->items[$key] = $value;
    }

    /**
     * Checks if a key exists in the dictionary
     *
     * @param mixed $key The key to check
     * @return bool True if the key exists, false otherwise
     */
    public function containsKey($key): bool
    {
        return array_key_exists($key, $this->items);
    }

    /**
     * Gets all keys in the dictionary
     *
     * @return array List of all keys
     */
    public function keys(): ?array
    {
        return array_keys($this->items);
    }

    /**
     * Gets all values in the dictionary
     *
     * @return array List of all values
     */
    public function values(): ?array
    {
        return array_values($this->items);
    }

    /**
     * Removes all items from the dictionary
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
    public function current()
    {
        return array_values($this->items)[$this->position];
    }

    /**
     * Gets the current key during iteration
     *
     * @return mixed The current key
     */
    public function key()
    {
        return array_keys($this->items)[$this->position];
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
        return isset(array_keys($this->items)[$this->position]);
    }

    /**
     * Gets the count of items in the dictionary
     *
     * @return int The number of items
     */
    public function count(): int
    {
        return count($this->items);
    }

    /**
     * Serializes the dictionary to a string
     *
     * @return string The serialized dictionary
     */
    public function serialize(): string
    {
        return serialize($this->items);
    }

    /**
     * Unserializes a string back into a dictionary
     *
     * @param string $data The serialized dictionary data
     */
    public function unserialize(string $data)
    {
        $this->items = unserialize($data);
    }
}
