<?php

namespace Mike\AdventOfCode\Solutions\Year2016\Day08;

use Mike\AdventOfCode\Solutions\Year2016\Day08\Command;

class Rectangle extends Command
{
    /**
     * The width of the rectangle.
     */
    public int $width;

    /**
     * The height of the rectangle.
     */
    public int $height;

    /**
     * Create new instance of rectangle command.
     */
    public function __construct(int $width, int $height)
    {
        parent::__construct('rect', func_get_args());

        $this->width = $width;
        $this->height = $height;
    }

    /**
     * Update the given display.
     *
     * @param  int[][]  $display
     * @return int[][]
     */
    public function updateDisplay(array $display): array
    {
        return array_replace_recursive($display, grid_make($this->height, $this->width, 1));
    }
}
