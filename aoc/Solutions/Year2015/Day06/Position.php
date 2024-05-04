<?php

namespace Mike\AdventOfCode\Solutions\Year2015\Day06;

class Position
{
    /**
     * The X coordinate.
     */
    public int $x = 0;

    /**
     * The Y coordinate.
     */
    public int $y = 0;

    /**
     * Create new instance of Instruction.
     */
    public function __construct(int $x = 0, int $y = 0)
    {
        $this->x = $x;
        $this->y = $y;
    }
}
