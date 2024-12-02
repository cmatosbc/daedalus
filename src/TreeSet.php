<?php

namespace Daedalus;

use Iterator;
use Countable;
use Serializable;
use Daedalus\TreeNode;

/**
 * TreeSet implementation using a self-balancing binary search tree
 *
 * Provides ordered storage and logarithmic-time performance for basic operations.
 * Elements must be comparable (implement __toString or be scalar types).
 *
 * @package Daedalus
 */
class TreeSet implements Iterator, Countable, Serializable
{
    /** @var TreeNode|null Root node of the tree */
    private ?TreeNode $root = null;
    
    /** @var int Number of elements in the set */
    private int $size = 0;

    /** @var array Cache for iteration */
    private array $iterationCache = [];

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
        if ($this->contains($item)) {
            return false;
        }

        $this->root = $this->insertNode($this->root, $item);
        $this->size++;
        $this->refreshIterationCache();
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
        if (!$this->contains($item)) {
            return false;
        }

        $this->root = $this->removeNode($this->root, $item);
        $this->size--;
        $this->refreshIterationCache();
        return true;
    }

    /**
     * Checks if an item exists in the set
     *
     * @param mixed $item The item to check
     * @return bool True if the item exists, false otherwise
     */
    public function contains(mixed $item): bool
    {
        return $this->findNode($this->root, $item) !== null;
    }

    /**
     * Gets the first (smallest) item in the set
     *
     * @return mixed|null The first item, or null if set is empty
     */
    public function first(): mixed
    {
        if ($this->root === null) {
            return null;
        }

        $node = $this->root;
        while ($node->left !== null) {
            $node = $node->left;
        }
        return $node->value;
    }

    /**
     * Gets the last (largest) item in the set
     *
     * @return mixed|null The last item, or null if set is empty
     */
    public function last(): mixed
    {
        if ($this->root === null) {
            return null;
        }

        $node = $this->root;
        while ($node->right !== null) {
            $node = $node->right;
        }
        return $node->value;
    }

    /**
     * Gets the item less than the given item
     *
     * @param mixed $item The reference item
     * @return mixed|null The lower item, or null if none exists
     */
    public function lower(mixed $item): mixed
    {
        $result = null;
        $node = $this->root;

        while ($node !== null) {
            $cmp = $this->compare($item, $node->value);
            if ($cmp <= 0) {
                $node = $node->left;
            } else {
                $result = $node->value;
                $node = $node->right;
            }
        }

        return $result;
    }

    /**
     * Gets the item greater than the given item
     *
     * @param mixed $item The reference item
     * @return mixed|null The higher item, or null if none exists
     */
    public function higher(mixed $item): mixed
    {
        $result = null;
        $node = $this->root;

        while ($node !== null) {
            $cmp = $this->compare($item, $node->value);
            if ($cmp >= 0) {
                $node = $node->right;
            } else {
                $result = $node->value;
                $node = $node->left;
            }
        }

        return $result;
    }

    /**
     * Converts the set to an array
     *
     * @return array Sorted array of all items
     */
    public function toArray(): array
    {
        return $this->iterationCache;
    }

    /**
     * Clears all items from the set
     */
    public function clear(): void
    {
        $this->root = null;
        $this->size = 0;
        $this->refreshIterationCache();
    }

    /**
     * Gets the current value during iteration
     *
     * @return mixed The current value
     */
    public function current(): mixed
    {
        return $this->iterationCache[$this->position];
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
        $this->position++;
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
        return isset($this->iterationCache[$this->position]);
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
            'items' => $this->toArray()
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
        $this->clear();
        foreach ($data['items'] as $item) {
            $this->add($item);
        }
    }

    /**
     * Compares two items
     *
     * @param mixed $a First item
     * @param mixed $b Second item
     * @return int Negative if a < b, 0 if equal, positive if a > b
     */
    private function compare(mixed $a, mixed $b): int
    {
        if (is_object($a) && is_object($b)) {
            return strcmp((string)$a, (string)$b);
        }
        return $a <=> $b;
    }

    /**
     * Inserts a node into the tree
     *
     * @param TreeNode|null $node Current node
     * @param mixed $item Item to insert
     * @return TreeNode New or updated node
     */
    private function insertNode(?TreeNode $node, mixed $item): TreeNode
    {
        if ($node === null) {
            return new TreeNode($item);
        }

        $cmp = $this->compare($item, $node->value);
        if ($cmp < 0) {
            $node->left = $this->insertNode($node->left, $item);
        } elseif ($cmp > 0) {
            $node->right = $this->insertNode($node->right, $item);
        }

        return $this->balance($node);
    }

    /**
     * Removes a node from the tree
     *
     * @param TreeNode|null $node Current node
     * @param mixed $item Item to remove
     * @return TreeNode|null New or updated node
     */
    private function removeNode(?TreeNode $node, mixed $item): ?TreeNode
    {
        if ($node === null) {
            return null;
        }

        $cmp = $this->compare($item, $node->value);
        if ($cmp < 0) {
            $node->left = $this->removeNode($node->left, $item);
        } elseif ($cmp > 0) {
            $node->right = $this->removeNode($node->right, $item);
        } else {
            if ($node->left === null) {
                return $node->right;
            } elseif ($node->right === null) {
                return $node->left;
            }

            $successor = $this->findMin($node->right);
            $node->value = $successor->value;
            $node->right = $this->removeNode($node->right, $successor->value);
        }

        return $this->balance($node);
    }

    /**
     * Finds a node in the tree
     *
     * @param TreeNode|null $node Current node
     * @param mixed $item Item to find
     * @return TreeNode|null Found node or null
     */
    private function findNode(?TreeNode $node, mixed $item): ?TreeNode
    {
        if ($node === null) {
            return null;
        }

        $cmp = $this->compare($item, $node->value);
        if ($cmp < 0) {
            return $this->findNode($node->left, $item);
        } elseif ($cmp > 0) {
            return $this->findNode($node->right, $item);
        }
        return $node;
    }

    /**
     * Finds the minimum node in a subtree
     *
     * @param TreeNode $node Root of subtree
     * @return TreeNode Minimum node
     */
    private function findMin(TreeNode $node): TreeNode
    {
        while ($node->left !== null) {
            $node = $node->left;
        }
        return $node;
    }

    /**
     * Gets the height of a node
     *
     * @param TreeNode|null $node The node
     * @return int Height of the node
     */
    private function height(?TreeNode $node): int
    {
        return $node === null ? 0 : $node->height;
    }

    /**
     * Updates the height of a node
     *
     * @param TreeNode $node The node to update
     */
    private function updateHeight(TreeNode $node): void
    {
        $node->height = max($this->height($node->left), $this->height($node->right)) + 1;
    }

    /**
     * Gets the balance factor of a node
     *
     * @param TreeNode $node The node
     * @return int Balance factor
     */
    private function getBalance(TreeNode $node): int
    {
        return $this->height($node->left) - $this->height($node->right);
    }

    /**
     * Performs a right rotation
     *
     * @param TreeNode $y Root node
     * @return TreeNode New root after rotation
     */
    private function rotateRight(TreeNode $y): TreeNode
    {
        $x = $y->left;
        $T2 = $x->right;

        $x->right = $y;
        $y->left = $T2;

        $this->updateHeight($y);
        $this->updateHeight($x);

        return $x;
    }

    /**
     * Performs a left rotation
     *
     * @param TreeNode $x Root node
     * @return TreeNode New root after rotation
     */
    private function rotateLeft(TreeNode $x): TreeNode
    {
        $y = $x->right;
        $T2 = $y->left;

        $y->left = $x;
        $x->right = $T2;

        $this->updateHeight($x);
        $this->updateHeight($y);

        return $y;
    }

    /**
     * Balances a node
     *
     * @param TreeNode $node Node to balance
     * @return TreeNode Balanced node
     */
    private function balance(TreeNode $node): TreeNode
    {
        $this->updateHeight($node);
        $balance = $this->getBalance($node);

        // Left Heavy
        if ($balance > 1) {
            if ($this->getBalance($node->left) < 0) {
                $node->left = $this->rotateLeft($node->left);
            }
            return $this->rotateRight($node);
        }

        // Right Heavy
        if ($balance < -1) {
            if ($this->getBalance($node->right) > 0) {
                $node->right = $this->rotateRight($node->right);
            }
            return $this->rotateLeft($node);
        }

        return $node;
    }

    /**
     * Refreshes the iteration cache
     */
    private function refreshIterationCache(): void
    {
        $this->iterationCache = [];
        $this->inorderTraversal($this->root);
    }

    /**
     * Performs an inorder traversal of the tree
     *
     * @param TreeNode|null $node Current node
     */
    private function inorderTraversal(?TreeNode $node): void
    {
        if ($node !== null) {
            $this->inorderTraversal($node->left);
            $this->iterationCache[] = $node->value;
            $this->inorderTraversal($node->right);
        }
    }
}
