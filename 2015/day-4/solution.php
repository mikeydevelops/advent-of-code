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

    $part1Found = false;
    $num = -1;
    $part1 = -1;
    $part2 = -1;

    do {
        $num ++;

        $hash = md5($secretKey . $num);

        if (! $part1Found && substr($hash, 0, 5) === '00000') {
            $part1 = $num;
            $part1Found = true;
        }

    } while(substr($hash, 0, 6) !== '000000');

    $part2 = $num;

    return [$part1, $part2];
}

if (basename(__FILE__) == basename($_SERVER["SCRIPT_FILENAME"])) {
    list($part1, $part2) = aoc2015day4();

    line("1. Lowest positive number for given secret key starting with 5 zeroes is: $part1");
    line("2. Lowest positive number for given secret key starting with 6 zeroes is: $part2");
}
