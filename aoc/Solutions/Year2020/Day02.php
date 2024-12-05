<?php

namespace Mike\AdventOfCode\Solutions\Year2020;

use Mike\AdventOfCode\Solutions\Solution;

class Day02 extends Solution
{
    /**
     * The example input to be used to test the solution.
     */
    protected ?string $exampleInput = <<<TXT
    1-3 a: abcde
    1-3 b: cdefg
    2-9 c: ccccccccc
    TXT;

    /**
     * Process the input from the challenge.
     *
     * @return array<array{min:int,max:int,letter:string,password:string}>
     */
    public function transformInput(string $input): array
    {
        $policies = preg_split('/\r?\n/', $input);

        $policies = array_map(function ($policy) {
            preg_match('/(?<min>\d+)-(?<max>\d+)\s+(?<letter>[a-z]):\s+(?<password>[a-z]+)/', $policy, $match);

            $policy = array_filter($match, 'is_string', ARRAY_FILTER_USE_KEY);

            $policy['min'] = intval($policy['min']);
            $policy['max'] = intval($policy['max']);

            return $policy;
        }, $policies);

        return $policies;
    }

    /**
     * Run the first part of the challenge.
     */
    public function part1(): int
    {
        $valid = array_filter($this->getInput(), function ($policy) {
            $counts = array_count_values(str_split($policy['password']));
            $count = $counts[$policy['letter']] ?? 0;

            return $count >= $policy['min'] && $count <= $policy['max'];
        });

        return count($valid);
    }

    /**
     * Run the second part of the challenge.
     */
    public function part2(): int
    {
        $valid = array_filter($this->getInput(), function ($policy) {
            $p = $policy['password'];
            $x = $policy['letter'];
            $y = $policy['min'] - 1;
            $z = $policy['max'] - 1;

            return ($p[$y] == $x || $p[$z] == $x) && $p[$y] !== $p[$z];
        });

        return count($valid);
    }
}
