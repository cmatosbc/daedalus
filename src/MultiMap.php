<?php

namespace Daedalus;

use InvalidArgumentException;
use Countable;

/**
 * A map implementation that allows multiple values to be associated with a single key.
 */
class MultiMap implements Countable
{
    private array $map = [];
    private bool $allowDuplicates;

    /**
     * Create a new MultiMap instance
     *
     * @param bool $allowDuplicates Whether to allow duplicate values for the same key
     */
    public function __construct(bool $allowDuplicates = true)
    {
        $this->allowDuplicates = $allowDuplicates;
    }

    /**
     * Add a value to the collection of values associated with a key
     *
     * @param mixed $key The key
     * @param mixed $value The value to add
     * @return bool True if the value was added, false if it was a duplicate and duplicates are not allowed
     */
    public function put($key, $value): bool
    {
        if (!isset($this->map[$key])) {
            $this->map[$key] = [];
        }

        if (!$this->allowDuplicates && in_array($value, $this->map[$key], true)) {
            return false;
        }

        $this->map[$key][] = $value;
        return true;
    }

    /**
     * Get all values associated with a key
     *
     * @param mixed $key The key to look up
     * @return array Array of values associated with the key
     */
    public function get($key): array
    {
        return $this->map[$key] ?? [];
    }

    /**
     * Remove a specific value from a key's collection
     *
     * @param mixed $key The key
     * @param mixed $value The value to remove
     * @return bool True if the value was removed, false if it wasn't found
     */
    public function removeValue($key, $value): bool
    {
        if (!isset($this->map[$key])) {
            return false;
        }

        $index = array_search($value, $this->map[$key], true);
        if ($index === false) {
            return false;
        }

        array_splice($this->map[$key], $index, 1);
        if (empty($this->map[$key])) {
            unset($this->map[$key]);
        }
        return true;
    }

    /**
     * Remove all values associated with a key
     *
     * @param mixed $key The key to remove
     * @return array The removed values
     */
    public function removeAll($key): array
    {
        $values = $this->get($key);
        unset($this->map[$key]);
        return $values;
    }

    /**
     * Get all keys that have at least one value
     *
     * @return array
     */
    public function keys(): array
    {
        return array_keys($this->map);
    }

    /**
     * Get all values in the multimap
     *
     * @return array
     */
    public function values(): array
    {
        return array_merge(...array_values($this->map));
    }

    /**
     * Check if the multimap contains a key
     *
     * @param mixed $key The key to check
     * @return bool
     */
    public function containsKey($key): bool
    {
        return isset($this->map[$key]);
    }

    /**
     * Check if the multimap contains a value for any key
     *
     * @param mixed $value The value to check
     * @return bool
     */
    public function containsValue($value): bool
    {
        foreach ($this->map as $values) {
            if (in_array($value, $values, true)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Get the total number of values across all keys
     *
     * @return int
     */
    public function count(): int
    {
        return array_sum(array_map('count', $this->map));
    }

    /**
     * Get the number of keys in the multimap
     *
     * @return int
     */
    public function keyCount(): int
    {
        return count($this->map);
    }

    /**
     * Clear all entries from the multimap
     *
     * @return void
     */
    public function clear(): void
    {
        $this->map = [];
    }
}
