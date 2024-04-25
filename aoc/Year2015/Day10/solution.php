<?php

require_once __DIR__ . '/../../common.php';

/**
 * Run the look-and-say algorithm on given string.
 *
 * @param  string  $input
 * @param integer  $repeat
 * @return string
 */
function lookAndSay(string $input) : string
{
    $output = '';

    $tokens = [];

    $lastToken = null;

    foreach (str_split($input) as $token) {
        if (!$lastToken || $lastToken[0] != $token) {
            $tokens[] = [$token, 0];

            $lastToken = &$tokens[count($tokens) - 1];
        }

        $lastToken[1] ++;
    }

    foreach ($tokens as $token) {
        $output .= $token[1] . $token[0];
    }

    return $output;
}

/**
 * Call the lookAndSay function repeatedly.
 *
 * @param  string  $input
 * @param  integer  $repeat
 * @return string
 */
function repeatLookAndSay(string $input, int $repeat) : string
{
    $output = $input;

    foreach (range(1, $repeat) as $i) {
        $output = lookAndSay($output);
    }

    return $output;
}

/**
 * Advent of Code 2015
 * Day 10: Elves Look, Elves Say
 * Part One
 *
 * @return integer
 * @throws \Exception
 */
function aoc2015day10part1(): int
{
    return strlen(repeatLookAndSay(getInput(), 40));
}

/**
 * Advent of Code 2015
 * Day 10: Elves Look, Elves Say
 * Part Two
 *
 * @return integer
 * @throws \Exception
 */
function aoc2015day10part2(): int
{
    return strlen(repeatLookAndSay(getInput(), 50));
}

if (basename(__FILE__) == basename($_SERVER["SCRIPT_FILENAME"])) {
    $length1 = aoc2015day10part1();

    // Second pass needs 2 GB of memory :O
    ini_set('memory_limit', '2G');
    $length2 = aoc2015day10part2();

    line("1. The length of the result is: $length1");
    line("2. The length of the second result is: $length2");
}
