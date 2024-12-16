<?php

namespace Daedalus\Tests;

use Daedalus\SortedMap;
use PHPUnit\Framework\TestCase;

class SortedMapTest extends TestCase
{
    private SortedMap $map;

    protected function setUp(): void
    {
        $this->map = new SortedMap();
    }

    /**
     * @testdox Can add and retrieve values maintaining key-value associations
     */
    public function testPutAndGet(): void
    {
        $this->map->put('b', 2);
        $this->map->put('a', 1);
        $this->map->put('c', 3);

        $this->assertEquals(1, $this->map->get('a'), 'Should retrieve correct value for key "a"');
        $this->assertEquals(2, $this->map->get('b'), 'Should retrieve correct value for key "b"');
        $this->assertEquals(3, $this->map->get('c'), 'Should retrieve correct value for key "c"');
    }

    /**
     * @testdox Keys are automatically maintained in natural sorted order
     */
    public function testKeyOrder(): void
    {
        $this->map->put('b', 2);
        $this->map->put('a', 1);
        $this->map->put('c', 3);

        $this->assertEquals(
            ['a', 'b', 'c'],
            $this->map->keys(),
            'Keys should be returned in natural sorted order'
        );
    }

    /**
     * @testdox Supports custom comparison function for key ordering
     */
    public function testCustomComparator(): void
    {
        $map = new SortedMap(fn($a, $b) => -strcmp($a, $b)); // reverse order
        $map->put('b', 2);
        $map->put('a', 1);
        $map->put('c', 3);

        $this->assertEquals(
            ['c', 'b', 'a'],
            $map->keys(),
            'Keys should be returned in reverse alphabetical order with custom comparator'
        );
    }

    /**
     * @testdox Can retrieve first and last keys in sorted order
     */
    public function testFirstAndLastKey(): void
    {
        $this->map->put('b', 2);
        $this->map->put('a', 1);
        $this->map->put('c', 3);

        $this->assertEquals('a', $this->map->firstKey(), 'First key should be the smallest in sorted order');
        $this->assertEquals('c', $this->map->lastKey(), 'Last key should be the largest in sorted order');
    }

    /**
     * @testdox Can remove key-value pairs from the map
     */
    public function testRemove(): void
    {
        $this->map->put('a', 1);
        $this->map->put('b', 2);
        
        $this->map->remove('a');
        $this->assertNull($this->map->get('a'), 'Removed key should return null when accessed');
        $this->assertEquals(1, $this->map->count(), 'Map size should decrease after removal');
    }

    /**
     * @testdox Can clear all entries from the map
     */
    public function testClear(): void
    {
        $this->map->put('a', 1);
        $this->map->put('b', 2);
        
        $this->map->clear();
        $this->assertEquals(0, $this->map->count(), 'Cleared map should have size 0');
        $this->assertEquals([], $this->map->keys(), 'Cleared map should have no keys');
    }
}
