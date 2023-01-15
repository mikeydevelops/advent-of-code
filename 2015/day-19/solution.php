<?php

require_once __DIR__ . '/../../common.php';

/**
 * Advent of Code 2015
 * Day 19: Medicine for Rudolph
 * Part One
 *
 * @return integer
 * @throws \Exception
 */
function aoc2015day19part1(): int
{
    $strings = explode("\n", getInput());

    return 0;
}

if (basename(__FILE__) == basename($_SERVER["SCRIPT_FILENAME"])) {
    $molecules = aoc2015day19part1();

    line("1. The total number of unique molecules is: $molecules");
}
