<?php

require_once __DIR__ . '/../../common.php';

/**
 * Advent of Code 2015
 * Day 11: Corporate Policy
 * Part One
 *
 * @return string
 * @throws \Exception
 */
function aoc2015day11part1(): string
{
    $input = explode("\n", getInput());

    return '';
}

if (basename(__FILE__) == basename($_SERVER["SCRIPT_FILENAME"])) {
    $password = aoc2015day11part1();

    line("1. Santa's next password should be: $password");
}
