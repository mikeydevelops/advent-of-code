<?php

require_once __DIR__ . '/../../common.php';

/**
 * Advent of Code 2015
 * Day 16: Aunt Sue
 * Part One
 *
 * @return integer
 * @throws \Exception
 */
function aoc2015day16part1(): int
{
    $aunts = explode("\n", getInput());

    return 0;
}

if (basename(__FILE__) == basename($_SERVER["SCRIPT_FILENAME"])) {
    $auntNo = aoc2015day16part1();

    line("1. The aunt that gave me the gift is aunt #$auntNo");
}
