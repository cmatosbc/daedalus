<?php

namespace Daedalus\Tests;

use PHPUnit\Framework\TestCase;
use Daedalus\TreeSet;

class TreeSetTest extends TestCase
{
    private TreeSet $set;

    protected function setUp(): void
    {
        $this->set = new TreeSet();
    }

    /**
     * @testdox Can add elements and verify their existence in the set
     */
    public function testAddAndContains(): void
    {
        $this->assertTrue(
            $this->set->add(5),
            'Should successfully add new element'
        );
        $this->assertTrue(
            $this->set->contains(5),
            'Should find added element'
        );
        $this->assertFalse(
            $this->set->add(5),
            'Should not add duplicate element'
        );
        $this->assertEquals(1, $this->set->count());
    }

    /**
     * @testdox Maintains elements in sorted order during insertion
     */
    public function testOrderedInsertion(): void
    {
        $numbers = [5, 3, 7, 1, 9, 2, 8, 4, 6];
        foreach ($numbers as $number) {
            $this->set->add($number);
        }
        
        $expected = [1, 2, 3, 4, 5, 6, 7, 8, 9];
        $this->assertEquals($expected, $this->set->toArray());
    }

    /**
     * @testdox Can retrieve first and last elements in sorted order
     */
    public function testFirstAndLast(): void
    {
        $numbers = [5, 3, 7, 1, 9];
        foreach ($numbers as $number) {
            $this->set->add($number);
        }
        
        $this->assertEquals(1, $this->set->first());
        $this->assertEquals(9, $this->set->last());
    }

    /**
     * @testdox Can find elements lower and higher than a given value
     */
    public function testLowerAndHigher(): void
    {
        $numbers = [5, 3, 7, 1, 9];
        foreach ($numbers as $number) {
            $this->set->add($number);
        }
        
        $this->assertEquals(3, $this->set->lower(5));
        $this->assertEquals(7, $this->set->higher(5));
        $this->assertEquals(null, $this->set->lower(1));
        $this->assertEquals(null, $this->set->higher(9));
    }

    /**
     * @testdox Can remove elements while maintaining order
     */
    public function testRemove(): void
    {
        $numbers = [5, 3, 7];
        foreach ($numbers as $number) {
            $this->set->add($number);
        }
        
        $this->assertTrue($this->set->remove(3));
        $this->assertEquals([5, 7], $this->set->toArray());
        $this->assertFalse($this->set->remove(3)); // Already removed
    }

    /**
     * @testdox Can clear all elements from the set
     */
    public function testClear(): void
    {
        $numbers = [5, 3, 7];
        foreach ($numbers as $number) {
            $this->set->add($number);
        }
        
        $this->set->clear();
        $this->assertEquals(0, $this->set->count());
        $this->assertNull($this->set->first());
        $this->assertNull($this->set->last());
    }

    /**
     * @testdox Can iterate over elements in sorted order
     */
    public function testIterator(): void
    {
        $numbers = [5, 3, 7, 1, 9];
        foreach ($numbers as $number) {
            $this->set->add($number);
        }
        
        $result = [];
        foreach ($this->set as $number) {
            $result[] = $number;
        }
        
        $this->assertEquals([1, 3, 5, 7, 9], $result);
    }

    /**
     * @testdox Can maintain order with custom object comparisons
     */
    public function testObjectOrdering(): void
    {
        $obj1 = new class {
            public function __toString() { return "1"; }
        };
        $obj2 = new class {
            public function __toString() { return "2"; }
        };
        $obj3 = new class {
            public function __toString() { return "3"; }
        };
        
        $this->set->add($obj2);
        $this->set->add($obj1);
        $this->set->add($obj3);
        
        $result = array_map(fn($obj) => (string)$obj, $this->set->toArray());
        $this->assertEquals(["1", "2", "3"], $result);
    }

    /**
     * @testdox Handles operations on empty set correctly
     */
    public function testEmptySetOperations(): void
    {
        $this->assertNull($this->set->first());
        $this->assertNull($this->set->last());
        $this->assertNull($this->set->lower(5));
        $this->assertNull($this->set->higher(5));
        $this->assertEquals(0, $this->set->count());
        $this->assertEquals([], $this->set->toArray());
    }

    /**
     * @testdox Maintains balance during insertions and deletions
     */
    public function testBalancing(): void
    {
        // Test AVL tree balancing with sequential insertions
        for ($i = 1; $i <= 7; $i++) {
            $this->set->add($i);
        }
        
        // Verify order is maintained after balancing
        $this->assertEquals(range(1, 7), $this->set->toArray());
        
        // Test removal maintains balance
        $this->set->remove(4);
        $this->assertEquals([1, 2, 3, 5, 6, 7], $this->set->toArray());
    }
}
