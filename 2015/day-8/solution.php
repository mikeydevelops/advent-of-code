<?php

require_once __DIR__ . '/../../common.php';

/**
 * Advent of Code 2015
 * Day 8: Matchsticks
 * Part One
 *
 * @return integer
 * @throws \Exception
 */
function aoc2015day8part1(): int
{
    $input = getInput();

    $result = 0;

    return $result;
}

if (basename(__FILE__) == basename($_SERVER["SCRIPT_FILENAME"])) {
    $litLights = aoc2015day8part1();

    line("1. The number of lit lights is: $litLights");
    line("2. Total brightness of all lights is: $totalBrightness");
}
