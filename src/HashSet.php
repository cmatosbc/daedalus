<?php

namespace Daedalus;

/**
 * HashSet implementation with hash-based storage and collision handling
 *
 * Provides constant-time O(1) performance for basic operations (add, remove, contains)
 * using hash buckets for collision handling. No guarantee on iteration order.
 *
 * @package Daedalus
 */
class HashSet implements \Iterator, \Countable, \Serializable
{
    /** @var array[] Array of buckets for hash collisions */
    private array $buckets = [];

    /** @var float Maximum load factor before rehashing */
    private float $loadFactor;

    /** @var int Current capacity (number of buckets) */
    private int $capacity;

    /** @var int Current number of items */
    private int $size = 0;

    /** @var int Current bucket for iteration */
    private int $currentBucket = 0;

    /** @var int Current position in bucket for iteration */
    private int $positionInBucket = 0;

    /**
     * Constructor
     *
     * @param array $items Initial items for the set
     * @param float $loadFactor Maximum load factor (default: 0.75)
     * @param int $initialCapacity Initial capacity (default: 16)
     */
    public function __construct(
        array $items = [],
        float $loadFactor = 0.75,
        int $initialCapacity = 16
    ) {
        $this->loadFactor = $loadFactor;
        $this->capacity = $initialCapacity;
        $this->initializeBuckets();
        
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
        if ($this->shouldRehash()) {
            $this->rehash();
        }

        $hash = $this->hash($item);
        $bucket = $this->getBucketIndex($hash);

        if (!isset($this->buckets[$bucket])) {
            $this->buckets[$bucket] = [];
        }

        foreach ($this->buckets[$bucket] as $existingItem) {
            if ($this->equals($existingItem, $item)) {
                return false;
            }
        }

        $this->buckets[$bucket][] = $item;
        $this->size++;
        return true;
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
        $bucket = $this->getBucketIndex($hash);

        if (!isset($this->buckets[$bucket])) {
            return false;
        }

        foreach ($this->buckets[$bucket] as $key => $existingItem) {
            if ($this->equals($existingItem, $item)) {
                unset($this->buckets[$bucket][$key]);
                $this->size--;
                if (empty($this->buckets[$bucket])) {
                    unset($this->buckets[$bucket]);
                } else {
                    $this->buckets[$bucket] = array_values($this->buckets[$bucket]);
                }
                return true;
            }
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
        $hash = $this->hash($item);
        $bucket = $this->getBucketIndex($hash);

        if (!isset($this->buckets[$bucket])) {
            return false;
        }

        foreach ($this->buckets[$bucket] as $existingItem) {
            if ($this->equals($existingItem, $item)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Adds all items from another set to this set
     *
     * @param HashSet $other The other set
     * @return bool True if any items were added
     */
    public function addAll(HashSet $other): bool
    {
        $modified = false;
        foreach ($other->toArray() as $item) {
            if ($this->add($item)) {
                $modified = true;
            }
        }
        return $modified;
    }

    /**
     * Removes all items that exist in another set
     *
     * @param HashSet $other The other set
     * @return bool True if any items were removed
     */
    public function removeAll(HashSet $other): bool
    {
        $modified = false;
        foreach ($other->toArray() as $item) {
            if ($this->remove($item)) {
                $modified = true;
            }
        }
        return $modified;
    }

    /**
     * Retains only items that exist in another set
     *
     * @param HashSet $other The other set
     * @return bool True if any items were removed
     */
    public function retainAll(HashSet $other): bool
    {
        $modified = false;
        $toRemove = [];
        
        foreach ($this->toArray() as $item) {
            if (!$other->contains($item)) {
                $toRemove[] = $item;
                $modified = true;
            }
        }
        
        foreach ($toRemove as $item) {
            $this->remove($item);
        }
        
        return $modified;
    }

    /**
     * Converts the set to an array
     *
     * @return array Array containing all items
     */
    public function toArray(): array
    {
        $result = [];
        foreach ($this->buckets as $bucket) {
            foreach ($bucket as $item) {
                $result[] = $item;
            }
        }
        return $result;
    }

    /**
     * Clears all items from the set
     */
    public function clear(): void
    {
        $this->buckets = [];
        $this->size = 0;
        $this->initializeBuckets();
    }

    /**
     * Gets the current value during iteration
     *
     * @return mixed The current value
     */
    public function current(): mixed
    {
        $bucket = $this->findNextNonEmptyBucket();
        return $bucket === null ? null : $bucket[$this->positionInBucket];
    }

    /**
     * Gets the current key during iteration
     *
     * @return int The current position
     */
    public function key(): int
    {
        return $this->currentBucket * $this->capacity + $this->positionInBucket;
    }

    /**
     * Moves to the next position in iteration
     */
    public function next(): void
    {
        $bucket = $this->findNextNonEmptyBucket();
        if ($bucket === null) {
            return;
        }

        $this->positionInBucket++;
        if ($this->positionInBucket >= count($bucket)) {
            $this->currentBucket++;
            $this->positionInBucket = 0;
        }
    }

    /**
     * Rewinds the iterator to the beginning
     */
    public function rewind(): void
    {
        $this->currentBucket = 0;
        $this->positionInBucket = 0;
    }

    /**
     * Checks if the current position is valid
     *
     * @return bool True if the position is valid
     */
    public function valid(): bool
    {
        return $this->findNextNonEmptyBucket() !== null;
    }

    /**
     * Gets the count of items in the set
     *
     * @return int The number of items
     */
    public function count(): int
    {
        return $this->size;
    }

    /**
     * Serializes the set to a string
     *
     * @return string The serialized set
     */
    public function serialize(): string
    {
        return serialize([
            'buckets' => $this->buckets,
            'loadFactor' => $this->loadFactor,
            'capacity' => $this->capacity,
            'size' => $this->size
        ]);
    }

    /**
     * Unserializes a string back into a set
     *
     * @param string $data The serialized set data
     */
    public function unserialize(string $data): void
    {
        $data = unserialize($data);
        $this->buckets = $data['buckets'];
        $this->loadFactor = $data['loadFactor'];
        $this->capacity = $data['capacity'];
        $this->size = $data['size'];
    }

    /**
     * Gets the load factor
     *
     * @return float Current load factor
     */
    public function getLoadFactor(): float
    {
        return $this->loadFactor;
    }

    /**
     * Gets the current capacity
     *
     * @return int Current capacity
     */
    public function getCapacity(): int
    {
        return $this->capacity;
    }

    /**
     * Initializes the bucket array
     */
    private function initializeBuckets(): void
    {
        $this->buckets = array_fill(0, $this->capacity, []);
    }

    /**
     * Checks if rehashing is needed
     *
     * @return bool True if rehashing is needed
     */
    private function shouldRehash(): bool
    {
        return $this->size >= $this->capacity * $this->loadFactor;
    }

    /**
     * Rehashes the set with double capacity
     */
    private function rehash(): void
    {
        $oldBuckets = $this->buckets;
        $this->capacity *= 2;
        $this->initializeBuckets();
        $this->size = 0;

        foreach ($oldBuckets as $bucket) {
            foreach ($bucket as $item) {
                $this->add($item);
            }
        }
    }

    /**
     * Gets the bucket index for a hash
     *
     * @param string $hash The hash value
     * @return int Bucket index
     */
    private function getBucketIndex(string $hash): int
    {
        return hexdec(substr($hash, 0, 8)) % $this->capacity;
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

    /**
     * Compares two items for equality
     *
     * @param mixed $a First item
     * @param mixed $b Second item
     * @return bool True if items are equal
     */
    private function equals(mixed $a, mixed $b): bool
    {
        if (is_object($a) && is_object($b)) {
            return spl_object_hash($a) === spl_object_hash($b);
        }
        return $a === $b;
    }

    /**
     * Finds the next non-empty bucket for iteration
     *
     * @return array|null The bucket or null if none found
     */
    private function findNextNonEmptyBucket(): ?array
    {
        while ($this->currentBucket < $this->capacity) {
            if (isset($this->buckets[$this->currentBucket]) &&
                !empty($this->buckets[$this->currentBucket]) &&
                $this->positionInBucket < count($this->buckets[$this->currentBucket])) {
                return $this->buckets[$this->currentBucket];
            }
            $this->currentBucket++;
            $this->positionInBucket = 0;
        }
        return null;
    }
}
