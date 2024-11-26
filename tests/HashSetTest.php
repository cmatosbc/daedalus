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

    public function testAddAndContains(): void
    {
        $this->assertTrue($this->set->add("apple"));
        $this->assertTrue($this->set->contains("apple"));
        $this->assertFalse($this->set->add("apple")); // Adding duplicate
        $this->assertEquals(1, $this->set->count());
    }

    public function testRemove(): void
    {
        $this->set->add("apple");
        $this->set->add("banana");
        
        $this->assertTrue($this->set->remove("apple"));
        $this->assertFalse($this->set->contains("apple"));
        $this->assertEquals(1, $this->set->count());
        
        $this->assertFalse($this->set->remove("nonexistent"));
    }

    public function testClear(): void
    {
        $this->set->add("apple");
        $this->set->add("banana");
        
        $this->set->clear();
        $this->assertEquals(0, $this->set->count());
        $this->assertFalse($this->set->contains("apple"));
    }

    public function testBulkOperations(): void
    {
        $this->set->add("apple");
        $this->set->add("banana");
        
        $otherSet = new HashSet(["banana", "cherry"]);
        
        // Test addAll
        $this->assertTrue($this->set->addAll($otherSet));
        $this->assertEquals(3, $this->set->count());
        $this->assertTrue($this->set->contains("cherry"));
        
        // Test removeAll
        $this->assertTrue($this->set->removeAll($otherSet));
        $this->assertEquals(1, $this->set->count());
        $this->assertTrue($this->set->contains("apple"));
        
        // Test retainAll
        $this->set->add("banana");
        $this->assertTrue($this->set->retainAll($otherSet));
        $this->assertEquals(1, $this->set->count());
        $this->assertTrue($this->set->contains("banana"));
    }

    public function testCustomLoadFactorAndCapacity(): void
    {
        $customSet = new HashSet([], 0.75, 32);
        
        for ($i = 0; $i < 24; $i++) { // Fill up to load factor
            $customSet->add("item$i");
        }
        
        $this->assertEquals(24, $customSet->count());
        $this->assertTrue($customSet->contains("item0"));
        $this->assertTrue($customSet->contains("item23"));
    }

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
        $this->assertEquals($items, $collectedItems);
    }

    public function testToArray(): void
    {
        $items = ["apple", "banana", "cherry"];
        foreach ($items as $item) {
            $this->set->add($item);
        }
        
        $array = $this->set->toArray();
        sort($array); // Sort since HashSet doesn't guarantee order
        $this->assertEquals($items, $array);
    }

    public function testObjectStorage(): void
    {
        $obj1 = new \stdClass();
        $obj1->id = 1;
        $obj2 = new \stdClass();
        $obj2->id = 2;
        
        $this->set->add($obj1);
        $this->set->add($obj2);
        
        $this->assertEquals(2, $this->set->count());
        $this->assertTrue($this->set->contains($obj1));
        $this->assertTrue($this->set->contains($obj2));
    }
}
