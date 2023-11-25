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
 * Count possible triangles from given list of triangle sides.
 *
 * @param  array  $triangles
 * @return integer
 */
function countPossibleTriangles(array $triangles): int
{
    $possibilities = 0;

    foreach ($triangles as $triangle) {
        $p = [
            $triangle[0] + $triangle[1] > $triangle[2],
            $triangle[1] + $triangle[2] > $triangle[0],
            $triangle[2] + $triangle[0] > $triangle[1],
        ];

        if ($p[0] && $p[1] && $p[2]) {
            $possibilities ++;
        }
    }

    return $possibilities;
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
    return countPossibleTriangles(getTriangles());
}

/**
 * Advent of Code 2016
 * Day 3: Squares With Three Sides
 *
 * Part Two
 *
 * @return int
 */
function aoc2016day3part2(): int
{
    $triangles = getTriangles();

    $vertical = array_merge(
        array_column($triangles, 0),
        array_column($triangles, 1),
        array_column($triangles, 2),
    );

    $vertical = array_chunk($vertical, 3);

    return countPossibleTriangles($vertical);
}

if (basename(__FILE__) == basename($_SERVER["SCRIPT_FILENAME"])) {
    $possibilities = aoc2016day3part1();
    $vertical = aoc2016day3part2();

    line("1. The number of possible triangles is: $possibilities.");
    line("2. The number of possible vertical triangles is: $vertical.");
}
