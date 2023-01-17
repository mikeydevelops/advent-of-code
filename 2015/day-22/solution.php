<?php

require_once __DIR__ . '/../../common.php';

/**
 * Advent of Code 2015
 * Day 22: Wizard Simulator 20XX
 * Part One
 *
 * @return integer
 * @throws \Exception
 */
function aoc2015day22part1(): int
{
    return 0;
}

if (basename(__FILE__) == basename($_SERVER["SCRIPT_FILENAME"])) {
    $winCost = aoc2015day22part1();

    line("1. The least amount of mana spent is: $winCost");
}
