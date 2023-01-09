<?php

require_once __DIR__ . '/../../common.php';

/**
 * Advent of Code 2015
 * Day 9: All in a Single Night
 * Part One
 *
 * @return integer
 * @throws \Exception
 */
function aoc2015day9part1(): int
{
    $input = getInput();

    $distance = 0;

    return $distance;
}

if (basename(__FILE__) == basename($_SERVER["SCRIPT_FILENAME"])) {
    $distance = aoc2015day9part1();

    line("1. The shortest distance is: $distance");
}
