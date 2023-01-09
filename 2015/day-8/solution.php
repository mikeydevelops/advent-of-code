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

if (basename(__FILE__) == basename($_SERVER["SCRIPT_FILENAME"])) {
    $characters = aoc2015day8part1();

    line("1. The number of needed characters is: $characters");
}
