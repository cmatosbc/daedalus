<?php

namespace Daedalus\Tests;

use PHPUnit\Framework\TestCase;
use Daedalus\EnhancedArrayObject;

class EnhancedArrayObjectTest extends TestCase
{
    private EnhancedArrayObject $array;

    protected function setUp(): void
    {
        $this->array = new EnhancedArrayObject([1, 2, 3]);
    }

    public function testBasicArrayOperations()
    {
        $this->assertCount(3, $this->array);
        $this->assertEquals(1, $this->array[0]);
        
        $this->array[] = 4;
        $this->assertCount(4, $this->array);
        $this->assertEquals(4, $this->array[3]);
        
        unset($this->array[0]);
        $this->assertCount(3, $this->array);
        $this->assertFalse(isset($this->array[0]));
    }

    public function testTypeSafety()
    {
        $typed = new EnhancedArrayObject([], \stdClass::class);
        
        $obj = new \stdClass();
        $typed[] = $obj;
        $this->assertCount(1, $typed);
        
        $this->expectException(\InvalidArgumentException::class);
        $typed[] = "not an object";
    }

    public function testEventHandling()
    {
        $count = 0;
        $this->array->addEventListener('add', function($key, $value) use (&$count) {
            $count++;
        });
        $this->array->addEventListener('remove', function($key) use (&$count) {
            $count++;
        });
        $this->array->addEventListener('modify', function($key, $oldValue, $newValue) use (&$count) {
            $count++;
        });

        $this->array['test1'] = 'value1'; // add event
        $this->array['test1'] = 'value2'; // modify event
        unset($this->array['test1']); // remove event

        $this->assertEquals(3, $count);
    }

    public function testRemoveEventListener()
    {
        $triggered = false;
        $listener = function() use (&$triggered) {
            $triggered = true;
        };

        $this->array->addEventListener('set', $listener);
        $this->array->removeEventListener('set', $listener);
        
        $this->array[] = 4;
        $this->assertFalse($triggered);
    }

    public function testMap()
    {
        $result = $this->array->map(fn($n) => $n * 2);
        $this->assertInstanceOf(EnhancedArrayObject::class, $result);
        $this->assertEquals([2, 4, 6], array_values((array)$result));
        
        // Original should be unchanged
        $this->assertEquals([1, 2, 3], array_values((array)$this->array));
    }

    public function testFilter()
    {
        $result = $this->array->filter(fn($n) => $n > 1);
        $this->assertInstanceOf(EnhancedArrayObject::class, $result);
        $this->assertEquals([2, 3], array_values((array)$result));
        
        // Original should be unchanged
        $this->assertEquals([1, 2, 3], array_values((array)$this->array));
    }

    public function testReduce()
    {
        $sum = $this->array->reduce(fn($carry, $n) => $carry + $n, 0);
        $this->assertEquals(6, $sum);
    }

    public function testIsEmpty()
    {
        $this->assertFalse($this->array->isEmpty());
        
        $empty = new EnhancedArrayObject();
        $this->assertTrue($empty->isEmpty());
    }

    public function testMerge()
    {
        $other = new EnhancedArrayObject([4, 5]);
        $result = $this->array->merge($other);
        
        $this->assertInstanceOf(EnhancedArrayObject::class, $result);
        $this->assertEquals([1, 2, 3, 4, 5], array_values((array)$result));
        
        // Test type safety
        $typed = new EnhancedArrayObject([new \stdClass()], \stdClass::class);
        $this->expectException(\InvalidArgumentException::class);
        $typed->merge([1, 2, 3]);
    }

    public function testCloneDeep()
    {
        $obj1 = new \stdClass();
        $obj1->value = 'test1';
        $obj2 = new \stdClass();
        $obj2->value = 'test2';
        
        $this->array['key1'] = $obj1;
        $this->array['key2'] = $obj2;
        
        $clone = clone $this->array;
        
        // Test that the cloned array has different object instances
        $this->assertNotSame($this->array['key1'], $clone['key1']);
        $this->assertNotSame($this->array['key2'], $clone['key2']);
        
        // Test that the values are still equal
        $this->assertEquals($this->array['key1']->value, $clone['key1']->value);
        $this->assertEquals($this->array['key2']->value, $clone['key2']->value);
    }
}
