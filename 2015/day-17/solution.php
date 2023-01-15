<?php

require_once __DIR__ . '/../../common.php';

function findCombinations(array $numbers, int $targetSum, int $depth = 0) : array
{
    if (! $targetSum) {
        return [];
    }

    $nums = $numbers;

    sort($numbers);

    $combinations = [];

    $combination = [];

    for ($i = $depth; $i < count($numbers); $i++) {
        $number = $numbers[$i];

        if ($number > $targetSum) {
            continue;
        }

        $combination[] = $number;

        $targetSum -= $number;

        if ($targetSum == 0) {
            $combinations[] = $combination;
        }

        $combinations = array_merge($combinations, findCombinations($nums, $targetSum, $i + 1));

        $targetSum += $number;
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

if (basename(__FILE__) == basename($_SERVER["SCRIPT_FILENAME"])) {
    $combinations = aoc2015day17part1();

    line("1. All possible combinations of containers that fit 150 liters are: $combinations");
}
