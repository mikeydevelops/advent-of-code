<?php

require_once __DIR__ . '/../../common.php';

/**
 * Create house grid from given directions.
 *
 * @param  array  $directions
 * @param  integer  $totalPlayers  The number of players traversing the directions.
 * @return integer[][]
 */
function createHouseGrid(array $directions, int $totalPlayers = 1)
{
    $players = array_map(function ($p) {
        return [
            'id' => $p,
            'x' => 0,
            'y' => 0,
        ];
    }, range(0, $totalPlayers));

    $rows = 1;
    $columns = 1;
    $grid = [[1]];

    $horizontalDirections = ['<', '>'];
    $verticalDirections = ['^', 'v'];

    $currentPlayer = 0;

    foreach ($directions as $direction) {
        $player = &$players[$currentPlayer];

        $x = &$player['x'];
        $y = &$player['y'];

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

        $currentPlayer += 1;

        if ($currentPlayer >= $totalPlayers) {
            $currentPlayer = 0;
        }
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

    $houses1 = 0;
    $houses2 = 0;

    $grid = createHouseGrid($directions = str_split($input));

    foreach ($grid as $row) {
        $houses1 += count(array_filter($row));
    }

    $grid2 = createHouseGrid($directions, 2);

    foreach ($grid2 as $row) {
        $houses2 += count(array_filter($row));
    }

    return [$houses1, $houses2];
}

if (basename(__FILE__) == basename($_SERVER["SCRIPT_FILENAME"])) {
    list($houses1, $houses2) = aoc2015day3();

    line("1. The houses that received at least 1 present are $houses1.");
    line("2. The houses that received at least 1 present are $houses2.");
}
