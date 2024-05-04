<?php

namespace Mike\AdventOfCode\Solutions\Year2015;

use Mike\AdventOfCode\Solutions\Solution;

/**
 * @method  int  getInput()  Get target house count.
 */
class Day20 extends Solution
{
    /**
     * The example input to be used to test the solution.
     */
    protected ?string $exampleInput = '70';

    /**
     * Process the input from the challenge.
     */
    public function transformInput(string $input): mixed
    {
        return intval(trim($input));
    }

    /**
     * Run the first part of the challenge.
     */
    public function part1(): int
    {
        $target = $this->getInput();

        $end = round($target / 10);

        $houses = array_fill(1, $end, 10);

        for($e = 2; $e < $end; $e++) {
            for($h = $e; $h < $end; $h += $e) {
                $houses[$h] += $e * 10;
            }
        }

        return min(array_keys(array_filter($houses, fn ($i) => $i >= $target)));
    }

    /**
     * Run the second part of the challenge.
     */
    public function part2(): int
    {
        $target = $this->getInput();

        $end = round($target / 11);

        $houses = array_fill(1, $end, 10);

        for($e = 2; $e < $end; $e++) {
            for($h = $e, $k = 0; $h < $end && $k < 50; $h += $e, $k++) {
                $houses[$h] += $e * 11;
            }
        }

        return min(array_keys(array_filter($houses, fn ($i) => $i >= $target)));
    }
}
