<?php

require_once __DIR__ . '/../../common.php';

/**
 * Create house grid from given directions.
 *
 * @param  array  $directions
 * @return integer[][]
 */
function createHouseGrid(array $directions)
{
    $x = 0;
    $y = 0;
    $rows = 1;
    $columns = 1;
    $grid = [[1]];

    $horizontalDirections = ['<', '>'];
    $verticalDirections = ['^', 'v'];

    foreach ($directions as $direction) {
        $isHorizontal = in_array($direction, $horizontalDirections);
        $isVertical = in_array($direction, $verticalDirections);

        // increment x coordinate
        if ($isHorizontal) {
            $x += $direction == '>' ? 1 : -1;
        }

        // increment y coordinate
        if ($isVertical) {
            $y += $direction == '^' ? 1 : -1;
        }

        // add new row if it is missing from house grid.
        if (! isset($grid[$y])) {
            $grid[$y] = array_fill(min(array_keys($grid[0])), $columns, 0);
            $rows += 1;
        }

        // add new column to all rows if it is missing in current row in house grid
        if(! isset($grid[$y][$x])) {
            foreach ($grid as $idx => $row) {
                $grid[$idx][$x] = 0;
            }

            $columns += 1;
        }

        $grid[$y][$x] += 1;
    }

    // for good measure sort the grid keys
    foreach ($grid as &$row) {
        ksort($row);
    }
    ksort($grid);

    return $grid;
}

/**
 * Advent of Code 2015
 * Day 3: Perfectly Spherical Houses in a Vacuum
 *
 * @return array
 * @throws \Exception
 */
function aoc2015day3()
{
    $input = getInput();

    $houses = 0;
    $part2 = 0;

    $grid = createHouseGrid(str_split($input));

    foreach ($grid as $row) {
        $houses += count(array_filter($row));
    }

    return [$grid, $houses, $part2];
}

if (basename(__FILE__) == basename($_SERVER["SCRIPT_FILENAME"])) {
    list($grid, $houses, $part2) = aoc2015day3();

    $rows = count($grid);
    $columns = count($grid[0]);

    line("Grid contains $rows rows and $columns columns.");

    line('');

    line("1. The houses that should receive at least 1 present are $houses.");
}
