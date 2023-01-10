<?php

require_once __DIR__ . '/../../common.php';

/**
 * Advent of Code 2015
 * Day 12: JSAbacusFramework.io
 * Part One
 *
 * @return integer
 * @throws \Exception
 */
function aoc2015day12part1(): int
{
    $document = getInput();

    preg_match_all('/[+-]?((\d*\.?\d+)|(\d+\.?\d*))/', $document, $matches);

    return array_sum($matches[0]);
}

if (basename(__FILE__) == basename($_SERVER["SCRIPT_FILENAME"])) {
    $sum = aoc2015day12part1();

    line("1. The total sum of all numbers is : $sum");
}
