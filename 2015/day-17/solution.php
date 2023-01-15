<?php

require_once __DIR__ . '/../../common.php';

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
    $inventory = explode("\n", getInput());

    $combinations = [];

    return count($combinations);
}

if (basename(__FILE__) == basename($_SERVER["SCRIPT_FILENAME"])) {
    $combinations = aoc2015day17part1();

    line("1. All possible combinations of containers that fit 150 liters are: $combinations");
}
