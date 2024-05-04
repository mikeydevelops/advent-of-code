<?php

namespace Mike\AdventOfCode\Solutions\Year2015;

use Mike\AdventOfCode\Solutions\Solution;

class Day01 extends Solution
{
    /**
     * The example input to be used to test the solution.
     */
    protected ?string $exampleInput = '()())';

    /**
     * Process the input from the challenge.
     *
     * @return string[]
     */
    public function transformInput(string $input): array
    {
        return str_split($input);
    }

    /**
     * Run the first part of the challenge.
     */
    public function part1(): int
    {
        [$floor, $_] = $this->run();

        return $floor;
    }

    /**
     * Run the second part of the challenge.
     */
    public function part2(): int
    {
        [$_, $basementPosition] = $this->run();

        return $basementPosition;
    }

    /**
     * Run santa's instructions.
     */
    protected function run(): array
    {
        $floor = 0;
        $basementPosition = 0;

        foreach ($this->getInput() as $idx => $instruction) {
            if ($instruction == '(') {
                $floor ++;
            }

            if ($instruction == ')') {
                $floor --;
            }

            if ($basementPosition === 0 && $floor == -1) {
                $basementPosition = $idx + 1;
            }
        }

        return [$floor, $basementPosition];
    }
}
