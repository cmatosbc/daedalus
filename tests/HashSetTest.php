<?php

namespace Daedalus\Tests;

use PHPUnit\Framework\TestCase;
use Daedalus\HashSet;

class HashSetTest extends TestCase
{
    private HashSet $set;

    protected function setUp(): void
    {
        $this->set = new HashSet();
    }

    /**
     * @testdox Can add elements and check for their existence
     */
    public function testAddAndContains(): void
    {
        $this->assertTrue($this->set->add("apple"), "Should successfully add new element");
        $this->assertTrue($this->set->contains("apple"), "Should find added element");
        $this->assertFalse($this->set->add("apple"), "Should not add duplicate element");
        $this->assertEquals(1, $this->set->count());
    }

    /**
     * @testdox Can remove elements from the set
     */
    public function testRemove(): void
    {
        $this->set->add("apple");
        $this->set->add("banana");
        
        $this->assertTrue($this->set->remove("apple"), "Should successfully remove existing element");
        $this->assertFalse($this->set->contains("apple"), "Should not find removed element");
        $this->assertEquals(1, $this->set->count());
        
        $this->assertFalse($this->set->remove("nonexistent"), "Should return false when removing non-existent element");
    }

    /**
     * @testdox Can clear all elements from the set
     */
    public function testClear(): void
    {
        $this->set->add("apple");
        $this->set->add("banana");
        
        $this->set->clear();
        $this->assertEquals(0, $this->set->count(), "Should have no elements after clearing");
        $this->assertFalse($this->set->contains("apple"), "Should not find cleared element");
    }

    /**
     * @testdox Can perform bulk operations with arrays
     */
    public function testBulkOperations(): void
    {
        $this->set->add("apple");
        $this->set->add("banana");
        
        $otherSet = new HashSet(["banana", "cherry"]);
        
        // Test addAll
        $this->assertTrue($this->set->addAll($otherSet), "Should add all elements from other set");
        $this->assertEquals(3, $this->set->count(), "Should have correct count after adding all");
        $this->assertTrue($this->set->contains("cherry"), "Should contain added element");
        
        // Test removeAll
        $this->assertTrue($this->set->removeAll($otherSet), "Should remove all elements from other set");
        $this->assertEquals(1, $this->set->count(), "Should have correct count after removing all");
        $this->assertTrue($this->set->contains("apple"), "Should still contain non-removed element");
        
        // Test retainAll
        $this->set->add("banana");
        $this->assertTrue($this->set->retainAll($otherSet), "Should retain all elements from other set");
        $this->assertEquals(1, $this->set->count(), "Should have correct count after retaining all");
        $this->assertTrue($this->set->contains("banana"), "Should contain retained element");
    }

    /**
     * @testdox Can customize load factor and initial capacity
     */
    public function testCustomLoadFactorAndCapacity(): void
    {
        $customSet = new HashSet([], 0.75, 32);
        
        for ($i = 0; $i < 24; $i++) { // Fill up to load factor
            $customSet->add("item$i");
        }
        
        $this->assertEquals(24, $customSet->count(), "Should have correct count with custom load factor and capacity");
        $this->assertTrue($customSet->contains("item0"), "Should contain added element with custom load factor and capacity");
        $this->assertTrue($customSet->contains("item23"), "Should contain added element with custom load factor and capacity");
    }

    /**
     * @testdox Can iterate over set elements
     */
    public function testIterator(): void
    {
        $items = ["apple", "banana", "cherry"];
        foreach ($items as $item) {
            $this->set->add($item);
        }
        
        $collectedItems = [];
        foreach ($this->set as $item) {
            $collectedItems[] = $item;
        }
        
        sort($collectedItems); // Sort since HashSet doesn't guarantee order
        $this->assertEquals($items, $collectedItems, "Iterator should return all elements");
    }

    /**
     * @testdox Can convert set to array
     */
    public function testToArray(): void
    {
        $items = ["apple", "banana", "cherry"];
        foreach ($items as $item) {
            $this->set->add($item);
        }
        
        $array = $this->set->toArray();
        sort($array); // Sort since HashSet doesn't guarantee order
        $this->assertEquals($items, $array, "Array conversion should contain all elements");
    }

    /**
     * @testdox Can store and retrieve object elements
     */
    public function testObjectStorage(): void
    {
        $obj1 = new \stdClass();
        $obj1->id = 1;
        $obj2 = new \stdClass();
        $obj2->id = 2;
        
        $this->set->add($obj1);
        $this->set->add($obj2);
        
        $this->assertEquals(2, $this->set->count(), "Should maintain correct count with objects");
        $this->assertTrue($this->set->contains($obj1), "Should store and find object references");
        $this->assertTrue($this->set->contains($obj2), "Should store and find object references");
    }
}
