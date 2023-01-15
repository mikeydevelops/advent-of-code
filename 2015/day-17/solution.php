<?php

require_once __DIR__ . '/../../common.php';

/**
 * Find combinations of given numbers that sum target.
 *
 * @param  array  $numbers
 * @param  integer  $targetSum
 * @param  array  $part
 * @return array
 */
function findCombinations(array $numbers, int $targetSum, array $part = []) : array
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

        $combinations = array_merge($combinations, findCombinations($remaining, $targetSum, array_merge($part, [$number])));
    }

    return $combinations;
}

/**
 * Advent of Code 2015
 * Day 17: No Such Thing as Too Much
 * Part One
 *
 * @return integer
 * @throws \Exception
 */
function aoc2015day17part1(): int
{
    $inventory = array_map('intval', explode("\n", getInput()));

    return count(findCombinations($inventory, 150));
}


/**
 * Advent of Code 2015
 * Day 17: No Such Thing as Too Much
 * Part One
 *
 * @return integer[]
 * @throws \Exception
 */
function aoc2015day17part2(): array
{
    $inventory = array_map('intval', explode("\n", getInput()));

    rsort($inventory);

    $sum = 0;
    $min = 0;

    foreach ($inventory as $num) {
        if ($sum >= 150) {
            break;
        }

        $sum += $num;

        $min ++;
    }

    $combinations = findCombinations($inventory, 150);

    $combinations = array_filter($combinations, function ($combination) use ($min) {
        return count($combination) == $min;
    });

    return [$min, count($combinations)];
}

if (basename(__FILE__) == basename($_SERVER["SCRIPT_FILENAME"])) {
    $combinations = aoc2015day17part1();
    [$minContainers, $uniqueCombinations] = aoc2015day17part2();

    line("1. All possible combinations of containers that fit 150 liters are: $combinations");
    line("2. The minimum number of containes is: $minContainers, and the total different combinations are: $uniqueCombinations");
}
