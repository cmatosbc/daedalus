<?php

namespace Daedalus;

/**
 * DisjointSet implementation that extends Set with Union-Find operations
 *
 * Provides efficient operations for maintaining disjoint (non-overlapping) sets
 * with union by rank and path compression optimizations.
 *
 * @package Daedalus
 */
class DisjointSet extends Set
{
    /** @var array Parent pointers for each element */
    private array $parent = [];

    /** @var array Rank (approximate depth) of each tree */
    private array $rank = [];

    /**
     * Constructor
     *
     * @param array $items Initial items for the disjoint sets
     */
    public function __construct(array $items = [])
    {
        parent::__construct();
        foreach ($items as $item) {
            $this->makeSet($item);
        }
    }

    /**
     * Creates a new set containing only the specified item
     *
     * @param mixed $item The item to create a set for
     * @return bool True if the set was created, false if item already exists
     */
    public function makeSet(mixed $item): bool
    {
        $hash = $this->hash($item);
        if (isset($this->parent[$hash])) {
            return false;
        }

        if (parent::add($item)) {
            $this->parent[$hash] = $hash;
            $this->rank[$hash] = 0;
            return true;
        }

        return false;
    }

    /**
     * Finds the representative element of the set containing the item
     *
     * Implements path compression: all nodes along the path to the root
     * are made to point directly to the root.
     *
     * @param mixed $item The item to find the representative for
     * @return mixed|null The representative item, or null if item doesn't exist
     */
    public function find(mixed $item): mixed
    {
        $hash = $this->hash($item);
        if (!isset($this->parent[$hash])) {
            return null;
        }

        // Path compression: make each node point directly to the root
        if ($this->parent[$hash] !== $hash) {
            $this->parent[$hash] = $this->hash($this->find($this->getItemByHash($this->parent[$hash])));
        }

        return $this->getItemByHash($this->parent[$hash]);
    }

    /**
     * Merges the sets containing the two items
     *
     * Implements union by rank: the root with higher rank becomes the parent.
     * If ranks are equal, the second root becomes parent and its rank increases.
     *
     * @param mixed $item1 First item
     * @param mixed $item2 Second item
     * @return bool True if sets were merged, false if items don't exist or are already in same set
     */
    public function union(mixed $item1, mixed $item2): bool
    {
        $root1 = $this->hash($this->find($item1));
        $root2 = $this->hash($this->find($item2));

        if ($root1 === null || $root2 === null || $root1 === $root2) {
            return false;
        }

        // Union by rank
        if ($this->rank[$root1] > $this->rank[$root2]) {
            $this->parent[$root2] = $root1;
        } else {
            $this->parent[$root1] = $root2;
            if ($this->rank[$root1] === $this->rank[$root2]) {
                $this->rank[$root2]++;
            }
        }

        return true;
    }

    /**
     * Checks if two items are in the same set
     *
     * @param mixed $item1 First item
     * @param mixed $item2 Second item
     * @return bool True if items are in the same set, false otherwise
     */
    public function connected(mixed $item1, mixed $item2): bool
    {
        $root1 = $this->find($item1);
        $root2 = $this->find($item2);

        return $root1 !== null && $root1 === $root2;
    }

    /**
     * Gets all items in the same set as the given item
     *
     * @param mixed $item The item to get the set for
     * @return array Array of items in the same set
     */
    public function getSet(mixed $item): array
    {
        $result = [];
        $root = $this->find($item);

        if ($root === null) {
            return $result;
        }

        foreach ($this->toArray() as $element) {
            if ($this->find($element) === $root) {
                $result[] = $element;
            }
        }

        return $result;
    }

    /**
     * Gets the number of disjoint sets
     *
     * @return int Number of disjoint sets
     */
    public function countSets(): int
    {
        $count = 0;
        foreach ($this->parent as $hash => $parent) {
            if ($hash === $parent) {
                $count++;
            }
        }
        return $count;
    }

    /**
     * Clears all sets
     */
    public function clear(): void
    {
        parent::clear();
        $this->parent = [];
        $this->rank = [];
    }

    /**
     * Gets an item by its hash
     *
     * @param string $hash The hash to look up
     * @return mixed The item with the given hash
     */
    private function getItemByHash(string $hash): mixed
    {
        foreach ($this->toArray() as $item) {
            if ($this->hash($item) === $hash) {
                return $item;
            }
        }
        return null;
    }
}
