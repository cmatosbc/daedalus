<?php

namespace Daedalus;

use InvalidArgumentException;

/**
 * A map implementation that keeps its keys in sorted order.
 * Keys must be comparable (strings, numbers, or objects implementing Comparable).
 */
class SortedMap
{
    private array $entries = [];
    private $comparator;

    /**
     * Create a new SortedMap instance
     *
     * @param callable|null $comparator Optional custom comparison function
     */
    public function __construct(?callable $comparator = null)
    {
        $this->comparator = $comparator ?? fn($a, $b) => $a <=> $b;
    }

    /**
     * Put a key-value pair into the map
     *
     * @param mixed $key The key
     * @param mixed $value The value
     * @return void
     */
    public function put($key, $value): void
    {
        $this->entries[$key] = $value;
        $this->sort();
    }

    /**
     * Get a value by key
     *
     * @param mixed $key The key to look up
     * @return mixed|null The value, or null if not found
     */
    public function get($key)
    {
        return $this->entries[$key] ?? null;
    }

    /**
     * Remove a key-value pair from the map
     *
     * @param mixed $key The key to remove
     * @return void
     */
    public function remove($key): void
    {
        unset($this->entries[$key]);
    }

    /**
     * Get all keys in sorted order
     *
     * @return array
     */
    public function keys(): array
    {
        return array_keys($this->entries);
    }

    /**
     * Get all values in order corresponding to sorted keys
     *
     * @return array
     */
    public function values(): array
    {
        return array_values($this->entries);
    }

    /**
     * Get the first key in the sorted map
     *
     * @return mixed|null
     */
    public function firstKey()
    {
        if (empty($this->entries)) {
            return null;
        }
        $keys = $this->keys();
        return reset($keys);
    }

    /**
     * Get the last key in the sorted map
     *
     * @return mixed|null
     */
    public function lastKey()
    {
        if (empty($this->entries)) {
            return null;
        }
        $keys = $this->keys();
        return end($keys);
    }

    /**
     * Check if the map contains a key
     *
     * @param mixed $key The key to check
     * @return bool
     */
    public function containsKey($key): bool
    {
        return array_key_exists($key, $this->entries);
    }

    /**
     * Get the number of entries in the map
     *
     * @return int
     */
    public function count(): int
    {
        return count($this->entries);
    }

    /**
     * Clear all entries from the map
     *
     * @return void
     */
    public function clear(): void
    {
        $this->entries = [];
    }

    /**
     * Sort the internal entries array using the comparator
     */
    private function sort(): void
    {
        uksort($this->entries, $this->comparator);
    }
}
