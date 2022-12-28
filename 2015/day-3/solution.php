<?php

require_once __DIR__ . '/../../common.php';

/**
 * Advent of Code 2015
 * Day 3: Perfectly Spherical Houses in a Vacuum
 *
 * @return integer[]
 * @throws \Exception
 */
function aoc2015day3()
{
    $input = getInput();

    $houses = 0;
    $part2 = 0;

    return [$houses, $part2];
}

if (basename(__FILE__) == basename($_SERVER["SCRIPT_FILENAME"])) {
    list($houses, $part2) = aoc2015day3();

    line("1. The houses that should receive at least 1 present are $houses.");
}
