<?php

require_once __DIR__ . '/../../common.php';

/**
 * Advent of Code 2015
 * Day 13: Knights of the Dinner Table
 * Part One
 *
 * @return integer
 * @throws \Exception
 */
function aoc2015day13part1(): int
{
    $input = getInput();

    return 0;
}

if (basename(__FILE__) == basename($_SERVER["SCRIPT_FILENAME"])) {
    $happiness = aoc2015day13part1();

    line("1. The total happiness is: $happiness");
}
