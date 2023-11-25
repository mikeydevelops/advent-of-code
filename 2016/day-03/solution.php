<?php

/**
 * Get the impossible triangles.
 *
 * @return array<array<integer>>
 */
function getTriangles(): array
{
    $triangles = explode("\n", getInput());

    return array_map(function ($triangle) {
        return array_map('intval', preg_split('/\s+/', trim($triangle)));
    }, $triangles);
}

/**
 * Advent of Code 2016
 * Day 3: Squares With Three Sides
 *
 * Part One
 *
 * @return int
 */
function aoc2016day3part1(): int
{
    $triangles = getTriangles();

    $possibilities = 0;

    foreach ($triangles as $triangle) {
        $pairs = [
            $triangle[0] + $triangle[1] > $triangle[2],
            $triangle[1] + $triangle[2] > $triangle[0],
            $triangle[2] + $triangle[0] > $triangle[1],
        ];

        if (count(array_unique($pairs)) == 1 && $pairs[0] === true) {
            $possibilities ++;
        }
    }

    return $possibilities;
}

if (basename(__FILE__) == basename($_SERVER["SCRIPT_FILENAME"])) {
    $code = aoc2016day3part1();

    line("1. The number of possible triangles is: $code.");
}
