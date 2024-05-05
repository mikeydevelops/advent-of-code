<?php

namespace Mike\AdventOfCode\Solutions\Year2016\Day03;

class Triangle
{
    /**
     * The length of side 1.
     */
    public int $side1;

    /**
     * The length of side 2.
     */
    public int $side2;

    /**
     * The length of side 3.
     */
    public int $side3;

    /**
     * Create new instance of triangle.
     */
    public function __construct(int $side1, int $side2, int $side3)
    {
        $this->side1 = $side1;
        $this->side2 = $side2;
        $this->side3 = $side3;
    }

    /**
     * Check to see if this triangle has valid sides.
     */
    public function isValid(): bool
    {
        return ($this->side1 + $this->side2 > $this->side3)
                && ($this->side2 + $this->side3 > $this->side1)
                && ($this->side3 + $this->side1 > $this->side2);
    }
}
