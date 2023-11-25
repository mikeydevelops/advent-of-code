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
 * Part One
 *
 * @return int
 */
function aoc2016day2part1(): int
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

/**
 * Check to see if given coordinates result in an edge for given keypad.
 */
function isAtEdge(array $keypad, int $x, int $y): bool
{
    return !isset($keypad[$y][$x]) || $keypad[$y][$x] === 0;
}

/**
 * Advent of Code 2016
 * Day 2: Bathroom Security
 *
 * Part Two
 *
 * @return string
 */
function aoc2016day2part2(): string
{
    $code = '';

    $instructions = getInstructions();

    $keypad = [
        [0,  0,   1,   0,  0],
        [0,  2,   3,   4,  0],
        [5,  6,   7,   8,  9],
        [0, 'A', 'B', 'C', 0],
        [0,  0,  'D',  0,  0],
    ];
    $x = 0;
    $y = 2;

    foreach ($instructions as $ins) {
        foreach ($ins as $i) {
            $i == 'U' && !isAtEdge($keypad, $x, $y-1) ? ($y --) : null;
            $i == 'R' && !isAtEdge($keypad, $x+1, $y) ? ($x ++) : null;
            $i == 'D' && !isAtEdge($keypad, $x, $y+1) ? ($y ++) : null;
            $i == 'L' && !isAtEdge($keypad, $x-1, $y) ? ($x --) : null;
        }

        $code .= $keypad[$y][$x];
    }

    return $code;
}

if (basename(__FILE__) == basename($_SERVER["SCRIPT_FILENAME"])) {
    $code = aoc2016day2part1();
    $codeNewKeypad = aoc2016day2part2();

    line("1. The bathroom code is: $code.");
    line("2. The bathroom code with new keypad is: $codeNewKeypad.");
}
