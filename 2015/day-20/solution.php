<?php

require_once __DIR__ . '/../../common.php';

/**
 * Advent of Code 2015
 * Day 20: Infinite Elves and Infinite Houses
 * Part One
 *
 * @return integer
 * @throws \Exception
 */
function aoc2015day20part1(): int
{
    $target = intval(getInput());
    $search = 0;

    $end = round($target / 10);

    $houses = array_fill(1, $end, 10);

    for($e = 2; $e < $end; $e++) {
        for($h = $e; $h < $end; $h += $e) {
            $houses[$h] += $e * 10;
        }
    }

    return min(array_keys(array_filter($houses, fn ($i) => $i >= $target)));
}


/**
 * Advent of Code 2015
 * Day 20: Infinite Elves and Infinite Houses
 * Part Two
 *
 * @return integer
 * @throws \Exception
 */
function aoc2015day20part2(): int
{
    $target = intval(getInput());

    $end = round($target / 11);

    $houses = array_fill(1, $end, 10);

    for($e = 2; $e < $end; $e++) {
        for($h = $e, $k = 0; $h < $end && $k < 50; $h += $e, $k++) {
            $houses[$h] += $e * 11;
        }
    }

    return min(array_keys(array_filter($houses, fn ($i) => $i >= $target)));
}

if (basename(__FILE__) == basename($_SERVER["SCRIPT_FILENAME"])) {
    $house = aoc2015day20part1();
    line("1. The number of the house is: $house");

    $house = aoc2015day20part2();
    line("2. The number of the house is: $house");
}
