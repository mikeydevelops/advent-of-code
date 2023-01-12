<?php

require_once __DIR__ . '/../../common.php';

/**
 * Advent of Code 2015
 * Day 15: Science for Hungry People
 * Part One
 *
 * @return integer
 * @throws \Exception
 */
function aoc2015day15part1(): int
{
    $ingredients = explode("\n", getInput());

    return 0;
}

if (basename(__FILE__) == basename($_SERVER["SCRIPT_FILENAME"])) {
    $cookieScore = aoc2015day15part1();

    line("1. The score of the highest-scoring cookie is: $cookieScore");
}
