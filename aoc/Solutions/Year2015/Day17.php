<?php

namespace Mike\AdventOfCode\Solutions\Year2015;

use Mike\AdventOfCode\Solutions\Solution;

/**
 * @method  int[]  getInput()  Get the inventory.
 */
class Day17 extends Solution
{
    /**
     * The example input to be used to test the solution.
     */
    protected ?string $exampleInput = "20\n15\n10\n5\n5";

    /**
     * Process the input from the challenge.
     *
     * @return int[]
     */
    public function transformInput(string $input): array
    {
        return array_map('intval', split_lines($input));
    }

    /**
     * Run the first part of the challenge.
     */
    public function part1(): int
    {
        return count($this->findCombinations($this->getInput(), $this->testing ? 25 : 150));
    }

    /**
     * Run the second part of the challenge.
     */
    public function part2(): int
    {
        $inventory = $this->getInput();

        rsort($inventory);

        $sum = 0;
        $min = 0;

        $total = $this->testing ? 25 : 150;

        foreach ($inventory as $num) {
            if ($sum >= $total) {
                break;
            }

            $sum += $num;

            $min ++;
        }

        $combinations = $this->findCombinations($inventory, $total);

        $combinations = array_filter($combinations, function ($combination) use ($min) {
            return count($combination) == $min;
        });

        return count($combinations);
    }

    /**
     * Find combinations of given numbers that sum target.
     */
    public function findCombinations(array $numbers, int $targetSum, array $part = []): array
    {
        $sum = array_sum($part);

        $combinations = [];

        if ($sum == $targetSum) {
            $combinations[] = $part;
        }

        if ($sum >= $targetSum) {
            return $combinations;
        }

        foreach ($numbers as $i => $number) {
            $remaining = array_slice($numbers, $i + 1);

            $combinations = array_merge($combinations, $this->findCombinations($remaining, $targetSum, array_merge($part, [$number])));
        }

        return $combinations;
    }
}
