<?php

namespace Mike\AdventOfCode\Solutions\Year2015;

use Mike\AdventOfCode\Solutions\Solution;

/**
 * @method  int[]  getInput() get the packages.
 */
class Day24 extends Solution
{
    /**
     * The example input to be used to test the solution.
     */
    protected ?string $exampleInput = "1\n2\n3\n4\n5\n7\n8\n9\n10\n11";

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
        return $this->groupPackages($this->getInput(), 3);
    }

    /**
     * Run the second part of the challenge.
     */
    public function part2(): int
    {
        return $this->groupPackages($this->getInput(), 4);
    }

    /**
     * Group santa packages into groups of equal weight.
     */
    function groupPackages(array $packages, int $totalGroups = 3): int
    {
        $sum = array_sum($packages);

        $targetSum = $sum / $totalGroups;

        $result = PHP_INT_MAX;
        $count = PHP_INT_MAX;

        foreach ($packages as $idx => $package) {
            foreach ($this->combinations($packages, $idx + 1) as $comb) {
                $s = array_sum($comb);

                if ($s != $targetSum) {
                    continue;
                }

                $qe = $this->getQuantumEntanglement($comb);

                if ($qe < $result) {

                    if (count($comb) > $count) {
                        continue;
                    }

                    $count = count($comb);
                    $result = $qe;

                    break 2;
                }
            }
        }

        return $result;
    }

    /**
     * Get the quantum entanglement for a group of weights.
     */
    public function getQuantumEntanglement(array $weights): int
    {
        return (int) array_product($weights);
    }

    /**
    * Calculate permutations of array.
    */
    public function combinations($array, $size = 3)
    {
        for ($i = 0; count($array) - $size >= $i; ++$i) {
            if ($size == 1) {
                yield [$array[$i]];

                continue;
            }

            /** @var array<int, T> $permutation */
            foreach ($this->combinations(array_slice($array, $i + 1), $size - 1) as $permutation) {
                array_unshift($permutation, $array[$i]);

                yield $permutation;
            }
        }
    }
}
