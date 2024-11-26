<?php

namespace Daedalus\Tests;

use PHPUnit\Framework\TestCase;
use Daedalus\ImmutableArrayObject;
use Daedalus\EnhancedArrayObject;

class ImmutableArrayObjectTest extends TestCase
{
    private ImmutableArrayObject $array;

    protected function setUp(): void
    {
        $this->array = new ImmutableArrayObject([1, 2, 3]);
    }

    public function testImmutabilityOnSet()
    {
        $this->expectException(\LogicException::class);
        $this->array[] = 4;
    }

    public function testImmutabilityOnUnset()
    {
        $this->expectException(\LogicException::class);
        unset($this->array[0]);
    }

    public function testArrayAccess()
    {
        $this->assertTrue(isset($this->array[0]));
        $this->assertEquals(1, $this->array[0]);
        $this->assertEquals(2, $this->array[1]);
        $this->assertEquals(3, $this->array[2]);
    }

    public function testConversionFromEnhanced()
    {
        $enhanced = new EnhancedArrayObject([1, 2, 3]);
        $immutable = $enhanced->toImmutable();
        
        $this->assertInstanceOf(ImmutableArrayObject::class, $immutable);
        $this->assertEquals([1, 2, 3], array_values((array)$immutable));
        
        $this->expectException(\LogicException::class);
        $immutable[] = 4;
    }

    public function testTypeSafetyInheritance()
    {
        $enhanced = new EnhancedArrayObject([new \stdClass()], \stdClass::class);
        $immutable = $enhanced->toImmutable();
        
        $this->assertEquals(\stdClass::class, $immutable->getType());
    }

    public function testIteration()
    {
        $values = [];
        foreach ($this->array as $key => $value) {
            $values[$key] = $value;
        }
        
        $this->assertEquals([1, 2, 3], array_values($values));
    }

    public function testCount()
    {
        $this->assertCount(3, $this->array);
    }
}
