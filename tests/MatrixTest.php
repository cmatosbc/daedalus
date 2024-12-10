<?php

namespace Daedalus\Tests;

use PHPUnit\Framework\TestCase;
use Daedalus\Matrix;

class MatrixTest extends TestCase
{
    /**
     * @test
     */
    public function testMatrixConstruction()
    {
        $data = [
            [1, 2, 3],
            [4, 5, 6],
            [7, 8, 9]
        ];
        $matrix = new Matrix($data);
        
        $this->assertEquals(3, $matrix->getRows());
        $this->assertEquals(3, $matrix->getCols());
        $this->assertEquals($data, $matrix->getData());
    }

    /**
     * @test
     */
    public function testInvalidMatrixConstruction()
    {
        $this->expectException(\InvalidArgumentException::class);
        new Matrix([
            [1, 2],
            [3, 4, 5]  // Different number of columns
        ]);
    }

    /**
     * @test
     */
    public function testMatrixAddition()
    {
        $matrix1 = new Matrix([[1, 2], [3, 4]]);
        $matrix2 = new Matrix([[5, 6], [7, 8]]);
        $result = $matrix1->add($matrix2);
        
        $expected = [
            [6, 8],
            [10, 12]
        ];
        $this->assertEquals($expected, $result->getData());
    }

    /**
     * @test
     */
    public function testMatrixSubtraction()
    {
        $matrix1 = new Matrix([[5, 6], [7, 8]]);
        $matrix2 = new Matrix([[1, 2], [3, 4]]);
        $result = $matrix1->subtract($matrix2);
        
        $expected = [
            [4, 4],
            [4, 4]
        ];
        $this->assertEquals($expected, $result->getData());
    }

    /**
     * @test
     */
    public function testMatrixMultiplication()
    {
        $matrix1 = new Matrix([[1, 2], [3, 4]]);
        $matrix2 = new Matrix([[2, 0], [1, 2]]);
        $result = $matrix1->multiply($matrix2);
        
        $expected = [
            [4, 4],
            [10, 8]
        ];
        $this->assertEquals($expected, $result->getData());
    }

    /**
     * @test
     */
    public function testMatrixScaling()
    {
        $matrix = new Matrix([[1, 2], [3, 4]]);
        $result = $matrix->scale(2);
        
        $expected = [
            [2, 4],
            [6, 8]
        ];
        $this->assertEquals($expected, $result->getData());
    }

    /**
     * @test
     */
    public function testMatrixTranspose()
    {
        $matrix = new Matrix([[1, 2, 3], [4, 5, 6]]);
        $result = $matrix->transpose();
        
        $expected = [
            [1, 4],
            [2, 5],
            [3, 6]
        ];
        $this->assertEquals($expected, $result->getData());
    }

    /**
     * @test
     */
    public function testDeterminant()
    {
        // 2x2 matrix
        $matrix1 = new Matrix([[4, 3], [2, 1]]);
        $this->assertEquals(-2, $matrix1->determinant());

        // 3x3 matrix
        $matrix2 = new Matrix([
            [1, 2, 3],
            [4, 5, 6],
            [7, 8, 9]
        ]);
        $this->assertEquals(0, $matrix2->determinant());
    }

    /**
     * @test
     */
    public function testIdentityMatrix()
    {
        $identity = Matrix::identity(3);
        $expected = [
            [1, 0, 0],
            [0, 1, 0],
            [0, 0, 1]
        ];
        $this->assertEquals($expected, $identity->getData());
    }

    /**
     * @test
     */
    public function testZeroMatrix()
    {
        $zero = Matrix::zero(2, 3);
        $expected = [
            [0, 0, 0],
            [0, 0, 0]
        ];
        $this->assertEquals($expected, $zero->getData());
    }

    /**
     * @test
     */
    public function testGetAndSet()
    {
        $matrix = new Matrix([[1, 2], [3, 4]]);
        
        // Test get
        $this->assertEquals(1, $matrix->get(0, 0));
        $this->assertEquals(4, $matrix->get(1, 1));
        
        // Test set
        $matrix->set(0, 1, 5);
        $this->assertEquals(5, $matrix->get(0, 1));
    }

    /**
     * @test
     */
    public function testInvalidGetAccess()
    {
        $matrix = new Matrix([[1, 2], [3, 4]]);
        $this->expectException(\OutOfBoundsException::class);
        $matrix->get(2, 0); // Invalid row
    }

    /**
     * @test
     */
    public function testInvalidSetAccess()
    {
        $matrix = new Matrix([[1, 2], [3, 4]]);
        $this->expectException(\OutOfBoundsException::class);
        $matrix->set(0, 2, 5); // Invalid column
    }

    /**
     * @test
     */
    public function testInvalidDeterminant()
    {
        $matrix = new Matrix([[1, 2, 3], [4, 5, 6]]); // Non-square matrix
        $this->expectException(\InvalidArgumentException::class);
        $matrix->determinant();
    }
}
