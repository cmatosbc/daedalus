<?php

namespace Daedalus;

/**
 * Internal node class for binary search tree implementations
 * 
 * Used by TreeSet and other tree-based data structures to store values
 * and maintain tree structure.
 * 
 * @package Daedalus
 * @internal This class is not part of the public API
 */
class TreeNode
{
    /** @var mixed Value stored in the node */
    public mixed $value;

    /** @var TreeNode|null Left child node */
    public ?TreeNode $left = null;

    /** @var TreeNode|null Right child node */
    public ?TreeNode $right = null;

    /** @var int Height of the node for AVL balancing */
    public int $height = 1;

    /**
     * Constructor
     * 
     * @param mixed $value Value to store in the node
     */
    public function __construct(mixed $value)
    {
        $this->value = $value;
    }
}
