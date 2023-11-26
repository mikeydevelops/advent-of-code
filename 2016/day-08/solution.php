<?php

/**
 * Parse a string instruction into command and arguments.
 *
 * @param  string  $instruction
 * @return array
 */
function parseInstruction(string $instruction): array
{
    preg_match('/^([a-z]+)\s+(.*?)$/i', $instruction, $matches);

    $argParsers = [
        '___default' => fn($params) => $params,
        'rect' => fn($params) => array_map('intval', explode('x', $params)),
        'rotate' => function ($params) {
            preg_match('/^(row|column)\s+(?:x|y)\=(\d+)\s+by\s+(\d+)$/i', $params, $matches);

            return [$matches[1], intval($matches[2]), intval($matches[3])];
        }
    ];

    $argParser = $argParsers[$matches[1]] ?? $argParsers['___default'];

    return [
        'command' => $matches[1],
        'args' => $argParser($matches[2]),
    ];
}

/**
 * Get the display instructions.
 *
 * @param  boolean  $example  Get the example input.
 * @return array
 */
function getInstructions(bool $example = false): array
{
    $instructions = explode("\n", $example ? <<<TXT
    rect 3x2
    rotate column x=1 by 1
    rotate row y=0 by 4
    rotate column x=1 by 1
    TXT : getInput());

    $instructions = array_map('parseInstruction', $instructions);

    return $instructions;
}

/**
 * Debug function to view contents of a display.
 *
 * @param  array  $display
 * @return void
 */
function renderDisplay(array $display, array $replacers = null): void
{
    $replacers = $replacers ?? [ '.', '#', ];

    foreach ($display as $row) {
        $line = '';

        foreach ($row as $pixel) {
            $line .= $replacers[$pixel] ?? $pixel;
        }

        line($line);
    }
}

/**
 * Compute image from given instructions.
 *
 * @param  array  $display
 * @param  array  $instructions
 * @return array
 */
function computeImage(array $display, array $instructions): array
{
    foreach ($instructions as $i) {
        $cmd = $i['command'];
        $args = $i['args'];

        if ($cmd == 'rect') {
            $display = array_replace_recursive($display, makeGrid($args[1], $args[0], 1));
        }

        if ($cmd == 'rotate') {
            $amount = $args[2];

            if ($args[0] == 'row') {
                $y = $args[1];
                $display[$y] = array_rotate($display[$y], $amount);

                continue;
            }

            $x = $args[1];
            $column = array_rotate(array_column($display, $x), $amount);

            foreach ($display as $idx => $row) {
                $display[$idx][$x] = $column[$idx];
            }
        }
    }

    return $display;
}

/**
 * Try to read the given display.
 *
 * @param  array  $display
 * @return string
 */
function readDisplay(array $display): string
{
    $result = '';
    $display = array_flip_row_column($display);

    $letters = array_sliding($display, 5, 5);

    $alphabet = [
        'A' => [5, 2, 2, 5, 0],
        'B' => [6, 3, 3, 3, 0],
        'F' => [6, 2, 2, 1, 0],
        'J' => [1, 1, 2, 5, 0],
        'U' => [5, 1, 1, 5, 0],
        'P' => [6, 2, 2, 2, 0],
        'S' => [3, 3, 3, 2, 0],
        'Z' => [3, 3, 3, 3, 0],
    ];

    foreach ($letters as $letter) {
        array_flip_row_column($letter);

        $pattern = array_map('array_sum', $letter);

        $result .= array_search($pattern, $alphabet) ?: '?';
    }

    return $result;
}

/**
 * Advent of Code 2016
 * Day 8: Two-Factor Authentication
 *
 * Part One
 *
 * @return int
 */
function aoc2016day8part1(): int
{
    $instructions = getInstructions(example: false);

    $display = makeGrid(6, 50, 0);

    $display = computeImage($display, $instructions);

    return array_sum(array_map('array_sum', $display));
}

/**
 * Advent of Code 2016
 * Day 8: Two-Factor Authentication
 *
 * Part Two
 *
 * @return string
 */
function aoc2016day8part2(): string
{
    $instructions = getInstructions(example: false);

    $display = makeGrid(6, 50, 0);

    $display = computeImage($display, $instructions);

    renderDisplay($display, [' ', '#']);

    return readDisplay($display);
}

if (basename(__FILE__) == basename($_SERVER["SCRIPT_FILENAME"])) {
    $litPixels = aoc2016day8part1();

    $code = aoc2016day8part2();
    line('');

    line("1. The lit pixels are: $litPixels.");
    line("2. The code is: $code.");
}
