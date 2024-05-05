<?php

namespace Mike\AdventOfCode\Solutions\Year2016\Day01;

class Instruction
{
    /**
     * The direction to face.
     */
    public string $direction;

    /**
     * The number of blocks to walk.
     */
    public string $blocks;

    /**
     * Create new instruction instance.
     */
    public function __construct(string $direction, int $blocks)
    {
        $this->direction = $direction;
        $this->blocks = $blocks;
    }
}
