<?php

require_once __DIR__ . '/../../common.php';

/**
 * Advent of Code 2015
 * Day 4: The Ideal Stocking Stuffer
 *
 * @return integer[]
 * @throws \Exception
 */
function aoc2015day4()
{
    $secretKey = trim(getInput());

    $part1 = -1;
    $part2 = -1;

    do {
        $part1 ++;

        $hash = md5($secretKey . $part1);
    } while(substr($hash, 0, 5) !== '00000');

    return [$part1, $part2];
}

if (basename(__FILE__) == basename($_SERVER["SCRIPT_FILENAME"])) {
    list($part1, $part2) = aoc2015day4();

    line("1. Lowest positive number for given secret key starting with 5 zeroes is: $part1");
}
