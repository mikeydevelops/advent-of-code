<?php

namespace Mike\AdventOfCode\Solutions\Year2017;

use Mike\AdventOfCode\Solutions\Solution;

class Day02 extends Solution
{
    /**
     * The example input to be used to test the solution.
     */
    protected ?string $exampleInput = <<<TXT
    5 1 9 5
    7 5 3
    2 4 6 8
    TXT;

    /**
     * Process the input from the challenge.
     *
     * @return array<integer[]>
     */
    public function transformInput(string $input): array
    {
        $lines = split_lines($input);
        $lines = array_map(fn($line) => array_map('intval', preg_split('/\s+/', $line)), $lines);

        return $lines;
    }

    /**
     * Run the first part of the challenge.
     */
    public function part1(): int
    {
        return array_sum(array_map(
            fn($line) => max($line) - min($line),
            $this->getInput()
        ));
    }

    /**
     * Run the second part of the challenge.
     */
    public function part2(): int
    {
        $lines = $this->getInput(example: <<<TXT
        5 9 2 8
        9 4 7 3
        3 8 6 5
        TXT);

        $result = 0;

        foreach ($lines as $nums) {
            foreach (combinations($nums, 2) as $comb) {
                $max = max($comb);
                $min = min($comb);

                if ($max % $min === 0) {
                    $result += $max / $min;
                    break;
                }
            }
        }

        return $result;
    }
}
