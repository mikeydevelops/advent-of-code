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
 * Advent of Code 2015
 * Day 10: Elves Look, Elves Say
 * Part One
 *
 * @return integer
 * @throws \Exception
 */
function aoc2015day10part1(): int
{
    $input = getInput();
    $output = $input;

    foreach (range(1, 40) as $i) {
        $output = lookAndSay($output);
    }

    return strlen($output);
}

if (basename(__FILE__) == basename($_SERVER["SCRIPT_FILENAME"])) {
    $length = aoc2015day10part1();

    line("1. The length of the result is: $length");
}
