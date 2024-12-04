<?php

namespace Mike\AdventOfCode\Solutions\Year2017;

use Mike\AdventOfCode\Solutions\Solution;

class Day01 extends Solution
{
    /**
     * The example input to be used to test the solution.
     */
    protected ?string $exampleInput = '91212129';

    /**
     * Process the input from the challenge.
     */
    public function transformInput(string $input): array
    {
        return array_map('intval', str_split($input));
    }

    /**
     * Run the first part of the challenge.
     */
    public function part1(): int
    {
        $matches = [];

        foreach ($digits = $this->getInput() as $idx => $digit) {
            $next = $digits[$idx + 1] ?? $digits[0];

            if ($digit === $next) {
                $matches[] = $digit;
            }
        }

        return array_sum($matches);
    }

    /**
     * Run the second part of the challenge.
     */
    public function part2(): int
    {
        $matches = [];

        $digits = $this->getInput();
        $digitCount = count($digits);
        $half = floor($digitCount / 2);

        foreach ($digits as $idx => $digit) {
            $next = $idx + $half;
            $next = $next >= $digitCount ? $next - $digitCount : $next;
            $next = $digits[$next];

            if ($digit === $next) {
                $matches[] = $digit;
            }
        }

        return array_sum($matches);
    }
}
