<?php

require_once __DIR__ . '/../../common.php';

/**
 * Get all adjacent values from a 2d grid given x and y.
 *
 * @param  array  $grid
 * @param  integer  $x
 * @param  integer  $y
 * @param  mixed  $default
 * @return array
 */
function grid_get_adjacent(array $grid, int $x, int $y, $default = null) : array
{
    // should we set default value if a cell is out of bounds?
    // we should set it if it was provided when the function was called.
    $setDefault = count(func_get_args()) == 4;

    // an arbitrary value to use when there is a missing value
    $missing = '###';

    $default = $setDefault ? $default : $missing;

    $adjacent = [
        $grid[$y - 1][$x - 1] ?? $default,
        $grid[$y - 1][$x    ] ?? $default,
        $grid[$y - 1][$x + 1] ?? $default,
        $grid[$y    ][$x + 1] ?? $default,
        $grid[$y + 1][$x + 1] ?? $default,
        $grid[$y + 1][$x    ] ?? $default,
        $grid[$y + 1][$x - 1] ?? $default,
        $grid[$y    ][$x - 1] ?? $default,
    ];

    if (! $setDefault) {
        $adjacent = array_filter($adjacent, function ($v) use ($missing) {
            return $v !== $missing;
        });
    }

    return $adjacent;
}

/**
 * Animate a 2d grid with given amount of frames.
 * Update cell using the provided callback.
 *
 * @param  array  $grid
 * @param  integer  $frames
 * @param  callable  $callback
 * @return array
 */
function animate_grid(array $grid, int $frames, callable $callback): array
{
    foreach (range(0, $frames - 1) as $frame) {
        $newGrid = $grid;

        foreach ($grid as $y => $row) {
            foreach ($row as $x => $cell) {

                $newGrid[$y][$x] = $callback($cell, grid_get_adjacent($grid, $x, $y), $frame, $grid);
            }
        }

        $grid = $newGrid;
    }

    return $grid;
}

/**
 * Advent of Code 2015
 * Day 18: Like a GIF For Your Yard
 * Part One
 *
 * @return integer
 * @throws \Exception
 */
function aoc2015day18part1(): int
{
    $grid = array_map('str_split', explode("\n", getInput()));

    $animated = animate_grid($grid, 100, function ($light, $adjacent, $frame, $grid) {
        $states = array_count_values($adjacent);

        if ($light == '#') { // on
            // light stays on when 2 or 3 adjacent lights are also on.
            return in_array($states['#'] ?? 0, [2, 3]) ? '#' : '.';
        }

        // light turns on if exactly 3 adjacent lights are on.
        return ($states['#'] ?? 0) == 3 ? '#' : '.';
    });

    $states = array_count_values(array_merge(...$animated));

    return $states['#'];
}

if (basename(__FILE__) == basename($_SERVER["SCRIPT_FILENAME"])) {
    $litLights = aoc2015day18part1();

    line("1. The number of lit lights is: $litLights");
}
