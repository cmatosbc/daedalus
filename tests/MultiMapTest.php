<?php

namespace Daedalus\Tests;

use Daedalus\MultiMap;
use PHPUnit\Framework\TestCase;

class MultiMapTest extends TestCase
{
    private MultiMap $map;

    protected function setUp(): void
    {
        $this->map = new MultiMap();
    }

    /**
     * @testdox Can store multiple values for a single key
     */
    public function testPutAndGet(): void
    {
        $this->map->put('key1', 'value1');
        $this->map->put('key1', 'value2');

        $this->assertEquals(
            ['value1', 'value2'],
            $this->map->get('key1'),
            'Should store and retrieve multiple values for the same key'
        );
    }

    /**
     * @testdox Can prevent duplicate values for the same key when configured
     */
    public function testDuplicateValues(): void
    {
        $map = new MultiMap(false); // don't allow duplicates
        
        $this->assertTrue(
            $map->put('key1', 'value1'),
            'Should successfully add first occurrence of a value'
        );
        $this->assertTrue(
            $map->put('key1', 'value2'),
            'Should successfully add different value for same key'
        );
        $this->assertFalse(
            $map->put('key1', 'value1'),
            'Should prevent adding duplicate value when duplicates are disabled'
        );

        $this->assertEquals(
            ['value1', 'value2'],
            $map->get('key1'),
            'Should maintain unique values for key when duplicates are disabled'
        );
    }

    /**
     * @testdox Can remove individual values from a key's collection
     */
    public function testRemoveValue(): void
    {
        $this->map->put('key1', 'value1');
        $this->map->put('key1', 'value2');

        $this->assertTrue(
            $this->map->removeValue('key1', 'value1'),
            'Should successfully remove existing value'
        );
        $this->assertEquals(
            ['value2'],
            $this->map->get('key1'),
            'Should maintain remaining values after removal'
        );
    }

    /**
     * @testdox Can remove all values associated with a key
     */
    public function testRemoveAll(): void
    {
        $this->map->put('key1', 'value1');
        $this->map->put('key1', 'value2');

        $removed = $this->map->removeAll('key1');
        $this->assertEquals(
            ['value1', 'value2'],
            $removed,
            'Should return all removed values'
        );
        $this->assertEquals(
            [],
            $this->map->get('key1'),
            'Should remove all values for the key'
        );
    }

    /**
     * @testdox Can check if a value exists in any key's collection
     */
    public function testContainsValue(): void
    {
        $this->map->put('key1', 'value1');
        $this->map->put('key2', 'value2');

        $this->assertTrue(
            $this->map->containsValue('value1'),
            'Should find existing value'
        );
        $this->assertFalse(
            $this->map->containsValue('nonexistent'),
            'Should not find non-existent value'
        );
    }

    /**
     * @testdox Can count total values and unique keys separately
     */
    public function testCount(): void
    {
        $this->map->put('key1', 'value1');
        $this->map->put('key1', 'value2');
        $this->map->put('key2', 'value3');

        $this->assertEquals(
            3,
            $this->map->count(),
            'Total count should include all values across all keys'
        );
        $this->assertEquals(
            2,
            $this->map->keyCount(),
            'Key count should only include unique keys'
        );
    }

    /**
     * @testdox Can clear all entries from the multimap
     */
    public function testClear(): void
    {
        $this->map->put('key1', 'value1');
        $this->map->put('key2', 'value2');

        $this->map->clear();
        $this->assertEquals(
            0,
            $this->map->count(),
            'Cleared map should have no values'
        );
        $this->assertEquals(
            [],
            $this->map->keys(),
            'Cleared map should have no keys'
        );
    }
}
