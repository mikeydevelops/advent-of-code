<?php

namespace Mike\AdventOfCode\Solutions\Year2015;

use Mike\AdventOfCode\Solutions\Solution;
use Mike\AdventOfCode\Solutions\Year2015\Day02\Box;

/**
 * @method \Mike\AdventOfCode\Solutions\Year2015\Day02\Box[] getInput()
 */
class Day02 extends Solution
{
    /**
     * The example input to be used to test the solution.
     */
    protected ?string $exampleInput = "2x3x4\n1x1x10";

    /**
     * Process the input from the challenge.
     *
     * @return \Mike\AdventOfCode\Solutions\Year2015\Day02\Box[]
     */
    public function transformInput(string $input): array
    {
        $input = explode_trim("\n", $input);

        return array_map([$this, 'parseBoxSize'], $input);
    }

    /**
     * Run the first part of the challenge.
     */
    public function part1(): int
    {
        $paper = 0;

        foreach ($this->getInput() as $box) {
            $paper += $box->area() + min($box->sides());
        }

        return $paper;
    }

    /**
     * Run the second part of the challenge.
     */
    public function part2(): int
    {
        $ribbon = 0;

        foreach ($this->getInput() as $box) {
            $ribbon += min($box->perimeters()) + $box->volume();
        }

        return $ribbon;
    }

    /**
     * Parse the size of the given box.
     */
    public function parseBoxSize(string $size) : Box
    {
        list($length, $width, $height) = array_map('intval', explode('x', $size));

        return Box::fromArray(compact('length', 'width', 'height'));
    }
}
