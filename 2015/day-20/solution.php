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

    // Fast but memory hog
    // $houses = [];

    // took 6g for 34M items in the array lol.
    // ini_set('memory_limit', '6G');

    // foreach (range(1, $target / 10) as $elf) {
    //     foreach (range($elf, $target, $elf) as $house) {
    //         if (! isset($houses[$house])) {
    //             $houses[$house] = 0;
    //         }

    //         $houses[$house] += $elf * 10;
    //     }
    // }

    // return min(array_keys(array_filter($houses, fn ($i) => $i >= $target)));

    // slow, really slow, but doesn't hog memory.
    // could use range(range($target / 44), $target / 10)
    // 44 seems to be the divider to find the right house number.
    foreach (range(1, $target / 10) as $house) {
        $sum = 0;

        foreach (range(1, $house) as $elf) {
            if ($house % $elf == 0) {
                $sum += $elf * 10;
            }
        }

        if ($sum >= $target) {
            $search = $house;

            break;
        }
    }

    return $search;
}


if (basename(__FILE__) == basename($_SERVER["SCRIPT_FILENAME"])) {
    $house = aoc2015day20part1();

    line("1. The number of the house is: $house");
}
