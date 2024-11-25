<?php

namespace Daedalus;

/**
 * Immutable version of EnhancedArrayObject
 * 
 * This class prevents modification of the array after instantiation
 * 
 * @package Daedalus
 */
class ImmutableArrayObject extends EnhancedArrayObject
{
    /**
     * Disabled operation - throws exception when attempting to set values
     * 
     * @param mixed $key   Unused
     * @param mixed $value Unused
     * @throws \LogicException Always throws this exception
     */
    public function offsetSet($key, $value): void
    {
        throw new \LogicException("Cannot modify an immutable array");
    }

    /**
     * Disabled operation - throws exception when attempting to unset values
     * 
     * @param mixed $key Unused
     * @throws \LogicException Always throws this exception
     */
    public function offsetUnset($key): void
    {
        throw new \LogicException("Cannot modify an immutable array");
    }
}
