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
 * @return array
 */
function aoc2016day1(): array
{
    $directions = getDirections();

    $facing = 0;
    $x = 0;
    $y = 0;

    $first = null;
    $visits = [];

    foreach ($directions as $dir) {
        $facing += $dir['direction'] == 'left' ? -1 : 1;

        $facing = $facing > 3 ? 0 : ($facing < 0 ? 3 : $facing);

        foreach (range(1, $dir['blocks']) as $_) {
            $facing == 0 ? ($y ++) : null;
            $facing == 1 ? ($x ++) : null;
            $facing == 2 ? ($y --) : null;
            $facing == 3 ? ($x --) : null;

            if (isset($first)) {
                continue;
            }

            $loc = "$x,$y";

            if (! isset($visits[$loc])) {
                $visits[$loc] = 0;
            }

            $visits[$loc] ++;

            if ($visits[$loc] == 2) {
                $first = abs($x) + abs($y);
            }
        }

    }

    return [abs($x) + abs($y), $first];
}

if (basename(__FILE__) == basename($_SERVER["SCRIPT_FILENAME"])) {
    [$blocks, $first] = aoc2016day1();

    line("1. The Easter Bunny HQ is [$blocks] blocks away.");
    line("2. The first location visited twice is [$first] blocks away.");
}
