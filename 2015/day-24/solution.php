<?php

/**
 * Get the packages Santa will deliver to children!
 *
 * @return integer[]
 * @throws \Exception
 */
function getPackages(): array
{
    return array_map('intval', explode("\n", getInput()));
}


/**
 * Group santa packages into groups of equal weight.
 *
 * @param  array  $packages
 * @param  integer  $totalGroups
 * @return integer
 */
function groupPackages(array $packages, int $totalGroups = 3): int
{
    $sum = array_sum($packages);

    $targetSum = $sum / $totalGroups;

    $result = PHP_INT_MAX;
    $count = PHP_INT_MAX;

    foreach ($packages as $idx => $package) {
        dump($idx + 1);

        foreach (combinations($packages, $idx + 1) as $comb) {
            $s = array_sum($comb);

            if ($s != $targetSum) {
                continue;
            }

            $qe = getQuantumEntanglement($comb);

            if ($qe < $result) {

                if (count($comb) > $count) {
                    continue;
                }

                $count = count($comb);
                $result = $qe;

                dump($qe);
            }
        }
    }

    return $result;
}

/**
 * Get the quantum entanglement for a group of weights.
 *
 * @param  integer[]  $weights
 * @return integer
 */
function getQuantumEntanglement(array $weights): int
{
    return array_product($weights);
}

/**
 * Advent of Code 2015
 * Day 24: It Hangs in the Balance
 *
 * @return int
 */
function aoc2015day24(): int
{
    $packages = getPackages();

    return groupPackages($packages, 3);
}


if (basename(__FILE__) == basename($_SERVER["SCRIPT_FILENAME"])) {
    $part1 = aoc2015day24();

    line("1. The quantum entanglement in the first group is: $part1.");
}
