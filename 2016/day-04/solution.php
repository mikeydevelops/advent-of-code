<?php

/**
 * Advent of Code 2016
 * Day 4: Security Through Obscurity
 *
 * Part One
 *
 * @return int
 */
function aoc2016day4part1(): int
{
    return countPossibleTriangles(getTriangles());
}


if (basename(__FILE__) == basename($_SERVER["SCRIPT_FILENAME"])) {
    $real = aoc2016day4part1();

    line("1. The real rooms are: $real.");
}
