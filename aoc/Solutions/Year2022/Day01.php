<?php

namespace Mike\AdventOfCode\Solutions\Year2022;

use Mike\AdventOfCode\Solutions\Solution;

class Day01 extends Solution
{
    /**
     * The example input to be used to test the solution.
     */
    protected ?string $exampleInput = <<<TXT
    1000
    2000
    3000

    4000

    5000
    6000

    7000
    8000
    9000

    10000
    TXT;

    /**
     * Process the input from the challenge.
     */
    public function transformInput(string $input): array
    {
        $inventories = preg_split('/[\r?\n]{2}/', $input);

        return array_map(fn($i) => array_map('intval', preg_split('/\r?\n/', $i)), $inventories);
    }

    /**
     * Run the first part of the challenge.
     */
    public function part1(): int
    {
        return max(array_map('array_sum', $this->getInput()));
    }

    /**
     * Run the second part of the challenge.
     */
    public function part2(): int
    {
        $calories = array_map('array_sum', $this->getInput());

        rsort($calories);

        return array_sum(array_slice($calories, 0, 3));
    }
}
