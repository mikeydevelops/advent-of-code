<?php

/**
 * Advent of Code 2015
 * Day 25: Let It Snow
 * Part One
 *
 * @return int
 */
function aoc2015day25part1(): int
{
    $packages = getPackages();

    return groupPackages($packages, 3);
}

if (basename(__FILE__) == basename($_SERVER["SCRIPT_FILENAME"])) {
    $part1 = aoc2015day5part1();

    line("1. The code for the machine is: $part1.");
}
