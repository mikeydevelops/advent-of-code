<?php

/**
 * Get the instructions for the bathroom code.
 *
 * @param  boolean  $example  Get example input, to debug code.
 * @return array<string[]>
 */
function getInstructions(bool $example = false): array
{
    $input = $example ? <<<TXT
    ULL
    RRDDD
    LURDL
    UUUUD
    TXT : getInput();

    $instructions = preg_split('/\r?\n/', $input);

    return array_map('str_split', $instructions);
}

/**
 * Advent of Code 2016
 * Day 2: Bathroom Security
 *
 * @return int
 */
function aoc2016day2(): int
{
    $code = '';

    $instructions = getInstructions();

    $keypad = [
        [1, 2, 3],
        [4, 5, 6],
        [7, 8, 9],
    ];
    $x = 1;
    $y = 1;

    foreach ($instructions as $ins) {
        foreach ($ins as $i) {
            $i == 'U' ? ($y --) : null;
            $i == 'R' ? ($x ++) : null;
            $i == 'D' ? ($y ++) : null;
            $i == 'L' ? ($x --) : null;

            $x = $x > 2 ? 2 : ($x < 0 ? 0 : $x);
            $y = $y > 2 ? 2 : ($y < 0 ? 0 : $y);
        }

        $code .= $keypad[$y][$x];
    }

    return intval($code);
}

if (basename(__FILE__) == basename($_SERVER["SCRIPT_FILENAME"])) {
    $code = aoc2016day2();

    line("1. The bathroom code is: $code.");
}
