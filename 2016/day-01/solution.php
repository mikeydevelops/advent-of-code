<?php

/**
 * Parse the directions from the puzzle input.
 *
 * @return array[]
 */
function getDirections(): array
{
    $directions = explode(', ', getInput());

    return array_map(function ($direction) {
        $blocks = intval(substr($direction, 1));
        $direction = substr($direction, 0, 1) == 'R' ? 'right' : 'left';

        return compact('blocks', 'direction');
    }, $directions);
}

/**
 * Advent of Code 2016
 * Day 1: No Time for a Taxicab
 *
 * Part One
 *
 * @return int
 */
function aoc2016day1part1(): int
{
    $directions = getDirections();

    $facing = 0;
    $x = 0;
    $y = 0;

    foreach ($directions as $dir) {
        $facing += $dir['direction'] == 'left' ? -1 : 1;

        $facing = $facing > 3 ? 0 : ($facing < 0 ? 3 : $facing);

        $blocks = ($facing > 1 ? -1 : 1) * $dir['blocks'];

        if ($facing == 0 || $facing == 2) {
            $y += $blocks;

            continue;
        }

        $x += $blocks;
    }

    return abs($x) + abs($y);
}

if (basename(__FILE__) == basename($_SERVER["SCRIPT_FILENAME"])) {
    $part1 = aoc2016day1part1();

    line("1. The Easter Bunny HQ is [$part1] blocks away.");
}
