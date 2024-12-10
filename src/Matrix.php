<?php

namespace Daedalus;

use Exception;

class Matrix {
    private array $data;
    private int $rows;
    private int $cols;

    /**
     * Create a new Matrix instance
     *
     * @param array $data Two-dimensional array of numeric values
     * @throws \InvalidArgumentException If the input array is not valid
     */
    public function __construct(array $data) {
        $this->validateInput($data);
        $this->data = $data;
        $this->rows = count($data);
        $this->cols = count($data[0]);
    }

    /**
     * Validate the input array
     *
     * @param array $data
     * @throws \InvalidArgumentException
     */
    private function validateInput(array $data): void {
        if (empty($data) || !is_array($data[0])) {
            throw new \InvalidArgumentException('Matrix must be a non-empty 2D array');
        }

        $cols = count($data[0]);
        foreach ($data as $row) {
            if (!is_array($row) || count($row) !== $cols) {
                throw new \InvalidArgumentException('All rows must have the same number of columns');
            }
            foreach ($row as $value) {
                if (!is_numeric($value)) {
                    throw new \InvalidArgumentException('Matrix values must be numeric');
                }
            }
        }
    }

    /**
     * Add another matrix to this one
     *
     * @param Matrix $other
     * @return Matrix
     * @throws \InvalidArgumentException
     */
    public function add(Matrix $other): Matrix {
        if ($this->rows !== $other->rows || $this->cols !== $other->cols) {
            throw new \InvalidArgumentException('Matrices must have the same dimensions for addition');
        }

        $result = [];
        for ($i = 0; $i < $this->rows; $i++) {
            for ($j = 0; $j < $this->cols; $j++) {
                $result[$i][$j] = $this->data[$i][$j] + $other->get($i, $j);
            }
        }

        return new Matrix($result);
    }

    /**
     * Subtract another matrix from this one
     *
     * @param Matrix $other
     * @return Matrix
     * @throws \InvalidArgumentException
     */
    public function subtract(Matrix $other): Matrix {
        if ($this->rows !== $other->rows || $this->cols !== $other->cols) {
            throw new \InvalidArgumentException('Matrices must have the same dimensions for subtraction');
        }

        $result = [];
        for ($i = 0; $i < $this->rows; $i++) {
            for ($j = 0; $j < $this->cols; $j++) {
                $result[$i][$j] = $this->data[$i][$j] - $other->get($i, $j);
            }
        }

        return new Matrix($result);
    }

    /**
     * Multiply this matrix by another matrix
     *
     * @param Matrix $other
     * @return Matrix
     * @throws \InvalidArgumentException
     */
    public function multiply(Matrix $other): Matrix {
        if ($this->cols !== $other->rows) {
            throw new \InvalidArgumentException('Number of columns in first matrix must equal number of rows in second');
        }

        $result = [];
        for ($i = 0; $i < $this->rows; $i++) {
            for ($j = 0; $j < $other->cols; $j++) {
                $sum = 0;
                for ($k = 0; $k < $this->cols; $k++) {
                    $sum += $this->data[$i][$k] * $other->get($k, $j);
                }
                $result[$i][$j] = $sum;
            }
        }

        return new Matrix($result);
    }

    /**
     * Scale the matrix by a scalar value
     *
     * @param float|int $scalar
     * @return Matrix
     */
    public function scale($scalar): Matrix {
        if (!is_numeric($scalar)) {
            throw new \InvalidArgumentException('Scalar must be numeric');
        }

        $result = [];
        for ($i = 0; $i < $this->rows; $i++) {
            for ($j = 0; $j < $this->cols; $j++) {
                $result[$i][$j] = $this->data[$i][$j] * $scalar;
            }
        }

        return new Matrix($result);
    }

    /**
     * Get the transpose of this matrix
     *
     * @return Matrix
     */
    public function transpose(): Matrix {
        $result = [];
        for ($i = 0; $i < $this->cols; $i++) {
            for ($j = 0; $j < $this->rows; $j++) {
                $result[$i][$j] = $this->data[$j][$i];
            }
        }

        return new Matrix($result);
    }

    /**
     * Calculate the determinant of a square matrix
     *
     * @return float|int
     * @throws \InvalidArgumentException If matrix is not square
     */
    public function determinant() {
        if ($this->rows !== $this->cols) {
            throw new \InvalidArgumentException('Determinant can only be calculated for square matrices');
        }

        if ($this->rows === 1) {
            return $this->data[0][0];
        }

        if ($this->rows === 2) {
            return $this->data[0][0] * $this->data[1][1] - $this->data[0][1] * $this->data[1][0];
        }

        $det = 0;
        for ($j = 0; $j < $this->cols; $j++) {
            $det += $this->data[0][$j] * $this->cofactor(0, $j);
        }

        return $det;
    }

    /**
     * Calculate the cofactor for a given position
     *
     * @param int $row
     * @param int $col
     * @return float|int
     */
    private function cofactor(int $row, int $col) {
        return (($row + $col) % 2 ? -1 : 1) * $this->minor($row, $col);
    }

    /**
     * Calculate the minor for a given position
     *
     * @param int $row
     * @param int $col
     * @return float|int
     */
    private function minor(int $row, int $col) {
        $minor = [];
        $m = 0;
        for ($i = 0; $i < $this->rows; $i++) {
            if ($i === $row) continue;
            $n = 0;
            for ($j = 0; $j < $this->cols; $j++) {
                if ($j === $col) continue;
                $minor[$m][$n] = $this->data[$i][$j];
                $n++;
            }
            $m++;
        }
        $minorMatrix = new Matrix($minor);
        return $minorMatrix->determinant();
    }

    /**
     * Get a value at the specified position
     *
     * @param int $row
     * @param int $col
     * @return mixed
     * @throws \OutOfBoundsException
     */
    public function get(int $row, int $col) {
        if ($row < 0 || $row >= $this->rows || $col < 0 || $col >= $this->cols) {
            throw new \OutOfBoundsException('Matrix position out of bounds');
        }
        return $this->data[$row][$col];
    }

    /**
     * Set a value at the specified position
     *
     * @param int $row
     * @param int $col
     * @param float|int $value
     * @throws \OutOfBoundsException
     */
    public function set(int $row, int $col, $value): void {
        if ($row < 0 || $row >= $this->rows || $col < 0 || $col >= $this->cols) {
            throw new \OutOfBoundsException('Matrix position out of bounds');
        }
        if (!is_numeric($value)) {
            throw new \InvalidArgumentException('Matrix values must be numeric');
        }
        $this->data[$row][$col] = $value;
    }

    /**
     * Get the number of rows
     *
     * @return int
     */
    public function getRows(): int {
        return $this->rows;
    }

    /**
     * Get the number of columns
     *
     * @return int
     */
    public function getCols(): int {
        return $this->cols;
    }

    /**
     * Get the matrix data
     *
     * @return array
     */
    public function getData(): array {
        return $this->data;
    }

    /**
     * Create an identity matrix of the specified size
     *
     * @param int $size
     * @return Matrix
     * @throws \InvalidArgumentException
     */
    public static function identity(int $size): Matrix {
        if ($size <= 0) {
            throw new \InvalidArgumentException('Size must be positive');
        }

        $data = array_fill(0, $size, array_fill(0, $size, 0));
        for ($i = 0; $i < $size; $i++) {
            $data[$i][$i] = 1;
        }

        return new Matrix($data);
    }

    /**
     * Create a zero matrix of the specified dimensions
     *
     * @param int $rows
     * @param int $cols
     * @return Matrix
     * @throws \InvalidArgumentException
     */
    public static function zero(int $rows, int $cols): Matrix {
        if ($rows <= 0 || $cols <= 0) {
            throw new \InvalidArgumentException('Dimensions must be positive');
        }

        return new Matrix(array_fill(0, $rows, array_fill(0, $cols, 0)));
    }
}
