<?php

namespace Daedalus\Tests;

use PHPUnit\Framework\TestCase;
use Daedalus\Matrix;

class MatrixTest extends TestCase
{
    /**
     * @testdox Can construct a valid matrix with proper dimensions
     */
    public function testMatrixConstruction()
    {
        $data = [
            [1, 2, 3],
            [4, 5, 6],
            [7, 8, 9]
        ];
        $matrix = new Matrix($data);
        
        $this->assertEquals(3, $matrix->getRows(), 'Matrix should have 3 rows');
        $this->assertEquals(3, $matrix->getCols(), 'Matrix should have 3 columns');
        $this->assertEquals($data, $matrix->getData(), 'Matrix data should match input array');
    }

    /**
     * @testdox Throws exception when constructing matrix with inconsistent dimensions
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
     * @testdox Can perform matrix addition with compatible matrices
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
        $this->assertEquals(
            $expected,
            $result->getData(),
            'Matrix addition should add corresponding elements'
        );
    }

    /**
     * @testdox Can perform matrix subtraction with compatible matrices
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
        $this->assertEquals(
            $expected,
            $result->getData(),
            'Matrix subtraction should subtract corresponding elements'
        );
    }

    /**
     * @testdox Can perform matrix multiplication with compatible matrices
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
        $this->assertEquals(
            $expected,
            $result->getData(),
            'Matrix multiplication should follow the matrix product rule'
        );
    }

    /**
     * @testdox Can scale a matrix by a scalar value
     */
    public function testMatrixScaling()
    {
        $matrix = new Matrix([[1, 2], [3, 4]]);
        $result = $matrix->scale(2);
        
        $expected = [
            [2, 4],
            [6, 8]
        ];
        $this->assertEquals(
            $expected,
            $result->getData(),
            'Matrix scaling should multiply each element by the scalar'
        );
    }

    /**
     * @testdox Can transpose a matrix by switching rows and columns
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
        $this->assertEquals(
            $expected,
            $result->getData(),
            'Matrix transpose should switch rows and columns'
        );
    }

    /**
     * @testdox Can calculate determinant for 2x2 and 3x3 matrices
     */
    public function testDeterminant()
    {
        // 2x2 matrix
        $matrix1 = new Matrix([[4, 3], [2, 1]]);
        $this->assertEquals(
            -2,
            $matrix1->determinant(),
            'Should correctly calculate 2x2 matrix determinant'
        );

        // 3x3 matrix
        $matrix2 = new Matrix([
            [1, 2, 3],
            [4, 5, 6],
            [7, 8, 9]
        ]);
        $this->assertEquals(
            0,
            $matrix2->determinant(),
            'Should correctly calculate 3x3 matrix determinant'
        );
    }

    /**
     * @testdox Can create identity matrix of specified size
     */
    public function testIdentityMatrix()
    {
        $identity = Matrix::identity(3);
        $expected = [
            [1, 0, 0],
            [0, 1, 0],
            [0, 0, 1]
        ];
        $this->assertEquals(
            $expected,
            $identity->getData(),
            'Identity matrix should have 1s on diagonal and 0s elsewhere'
        );
    }

    /**
     * @testdox Can create zero matrix of specified dimensions
     */
    public function testZeroMatrix()
    {
        $zero = Matrix::zero(2, 3);
        $expected = [
            [0, 0, 0],
            [0, 0, 0]
        ];
        $this->assertEquals(
            $expected,
            $zero->getData(),
            'Zero matrix should contain all zeros'
        );
    }

    /**
     * @testdox Can get and set individual matrix elements
     */
    public function testGetAndSet()
    {
        $matrix = new Matrix([[1, 2], [3, 4]]);
        
        $this->assertEquals(1, $matrix->get(0, 0), 'Should retrieve correct value from first element');
        $this->assertEquals(4, $matrix->get(1, 1), 'Should retrieve correct value from last element');
        
        $matrix->set(0, 1, 5);
        $this->assertEquals(5, $matrix->get(0, 1), 'Should update and retrieve modified value');
    }

    /**
     * @testdox Throws exception when accessing invalid row index
     */
    public function testInvalidGetAccess()
    {
        $matrix = new Matrix([[1, 2], [3, 4]]);
        $this->expectException(\OutOfBoundsException::class);
        $matrix->get(2, 0); // Invalid row
    }

    /**
     * @testdox Throws exception when setting value at invalid column index
     */
    public function testInvalidSetAccess()
    {
        $matrix = new Matrix([[1, 2], [3, 4]]);
        $this->expectException(\OutOfBoundsException::class);
        $matrix->set(0, 2, 5); // Invalid column
    }

    /**
     * @testdox Throws exception when calculating determinant of non-square matrix
     */
    public function testInvalidDeterminant()
    {
        $matrix = new Matrix([[1, 2, 3], [4, 5, 6]]); // Non-square matrix
        $this->expectException(\InvalidArgumentException::class);
        $matrix->determinant();
    }
}
