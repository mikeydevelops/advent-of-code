<?php

require_once __DIR__ . '/../../common.php';

/**
 * Advent of Code 2015
 * Day 8: Matchsticks
 * Part One
 *
 * @return integer
 * @throws \Exception
 */
function aoc2015day8part1(): int
{
    $strings = explode("\n", getInput());

    $lengths = array_map(function ($string) {
        return [
            'code' => strlen($string),
            'memory' => strlen(eval('return ' . $string . ';')),
        ];
    }, $strings);

    $codeTotal = array_sum(array_column($lengths, 'code'));
    $memoryTotal = array_sum(array_column($lengths, 'memory'));

    return $codeTotal - $memoryTotal;
}

/**
 * Advent of Code 2015
 * Day 8: Matchsticks
 * Part Two
 *
 * @return integer
 * @throws \Exception
 */
function aoc2015day8part2(): int
{
    $strings = explode("\n", getInput());

    $lengths = array_map(function ($string) {
        return [
            'code' => $len = strlen($string),
            'encoded' => $len + substr_count($string, '\\') + substr_count($string, '"') + 2,
        ];
    }, $strings);

    $encodedTotal = array_sum(array_column($lengths, 'encoded'));
    $codeTotal = array_sum(array_column($lengths, 'code'));

    return $encodedTotal - $codeTotal;
}

if (basename(__FILE__) == basename($_SERVER["SCRIPT_FILENAME"])) {
    $characters = aoc2015day8part1();
    $encoded = aoc2015day8part2();

    line("1. The number of needed characters is: $characters");
    line("2. The number of needed encoded characters is: $encoded");
}
