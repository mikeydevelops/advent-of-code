<?php

namespace Mike\AdventOfCode\Solutions\Year2021;

use Mike\AdventOfCode\Solutions\Solution;

class Day01 extends Solution
{
    /**
     * The example input to be used to test the solution.
     */
    protected ?string $exampleInput = <<<TXT
    199
    200
    208
    210
    200
    207
    240
    269
    260
    263
    TXT;

    /**
     * Process the input from the challenge.
     */
    public function transformInput(string $input): array
    {
        return array_map('intval', preg_split('/\r?\n/',$input));
    }

    /**
     * Run the first part of the challenge.
     */
    public function part1(): int
    {
        $increased = 0;

        $measurements = $this->getInput();

        for ($i = 1; $i < count($measurements); $i ++) {
            $prev = $measurements[$i - 1];

            if ($measurements[$i] > $prev) {
                $increased ++;
            }
        }

        return $increased;
    }

    /**
     * Run the second part of the challenge.
     */
    public function part2(): int
    {
        $increased = 0;

        $measurements = $this->getInput();

        $windows = array_map('array_sum', array_sliding($measurements, 3));

        for ($i = 1; $i < count($windows); $i ++) {
            $prev = $windows[$i - 1];

            if ($windows[$i] > $prev) {
                $increased ++;
            }
        }

        return $increased;
    }
}
