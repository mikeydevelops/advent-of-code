<?php

namespace Mike\AdventOfCode\Solutions\Year2016;

use Mike\AdventOfCode\Solutions\Solution;
use Mike\AdventOfCode\Solutions\Year2016\Day03\Triangle;

/**
 * @method  \Mike\AdventOfCode\Solutions\Year2016\Day03\Triangle[]  getInput()  Get the triangles.
 */
class Day03 extends Solution
{
    /**
     * The example input to be used to test the solution.
     */
    protected ?string $exampleInput = <<<TXT
    101 301 501
    102 302 502
    103 303 503
    201 401 601
    202 402 602
    203 403 603
    TXT;

    /**
     * Process the input from the challenge.
     *
     * @return \Mike\AdventOfCode\Solutions\Year2016\Day03\Triangle[]
     */
    public function transformInput(string $input): array
    {
        return array_map(function ($triangle) {
            return new Triangle(...preg_split('/\s+/', $triangle));
        }, split_lines($input));
    }

    /**
     * Run the first part of the challenge.
     */
    public function part1(): int
    {
        return $this->countValidTriangles($this->getInput());
    }

    /**
     * Run the second part of the challenge.
     */
    public function part2(): int
    {
        $triangles = $this->getInput();

        $vertical = array_merge(
            array_map(fn(Triangle $t) => $t->side1, $triangles),
            array_map(fn(Triangle $t) => $t->side2, $triangles),
            array_map(fn(Triangle $t) => $t->side3, $triangles),
        );

        $vertical = array_map(
            fn(array $sides) => new Triangle(...$sides),
            array_chunk($vertical, 3),
        );

        return $this->countValidTriangles($vertical);
    }

    /**
     * Count the valid triangles.
     *
     * @param  \Mike\AdventOfCode\Solutions\Year2016\Day03\Triangle[]  $triangles
     * @return int
     */
    public function countValidTriangles(array $triangles): int
    {
        return count(array_filter($triangles, fn (Triangle $t) => $t->isValid()));
    }
}
