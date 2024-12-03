<?php

namespace Mike\AdventOfCode\Solutions\Year2024;

use Mike\AdventOfCode\Solutions\Solution;

class Day03 extends Solution
{
    /**
     * The example input to be used to test the solution.
     */
    protected ?string $exampleInput = 'xmul(2,4)&mul[3,7]!^don\'t()_mul(5,5)+mul(32,64](mul(11,8)undo()?mul(8,5))';

    /**
     * Run the first part of the challenge.
     */
    public function part1(): int
    {
        preg_match_all('/mul\((\d+),(\d+)\)/', $this->getInput(), $instructions, PREG_SET_ORDER);

        $instructions = array_map(fn($ins) => [(int) $ins[1], (int) $ins[2]], $instructions);

        $products = array_map('array_product', $instructions);

        return array_sum($products);
    }

    /**
     * Run the second part of the challenge.
     */
    public function part2(): int
    {
        preg_match_all('/(?:mul\((\d+),(\d+)\))|(?:do|don\'t)\(\)/', $this->getInput(), $instructions, PREG_SET_ORDER);

        $include = true;

        foreach ($instructions as $idx => $ins) {
            if (in_array($ins[0], ['do()', "don't()"])) {
                $include = $ins[0] === 'do()';
                unset($instructions[$idx]);

                continue;
            }

            if (! $include) {
                unset($instructions[$idx]);
            }
        }

        $instructions = array_map(fn($ins) => [(int) $ins[1], (int) $ins[2]], $instructions);

        $products = array_map('array_product', $instructions);

        return array_sum($products);
    }
}
