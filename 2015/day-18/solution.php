<?php

require_once __DIR__ . '/../../common.php';

/**
 * Advent of Code 2015
 * Day 18: Like a GIF For Your Yard
 * Part One
 *
 * @return integer
 * @throws \Exception
 */
function aoc2015day18part1(): int
{
    $strings = explode("\n", getInput());

    return 0;
}

if (basename(__FILE__) == basename($_SERVER["SCRIPT_FILENAME"])) {
    $litLights = aoc2015day18part1();

    line("1. The number of lit lights is: $litLights");
}
