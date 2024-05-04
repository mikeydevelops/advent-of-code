<?php

namespace Mike\AdventOfCode\Solutions\Year2015;

use Mike\AdventOfCode\Solutions\Solution;

class Day03 extends Solution
{
    /**
     * The example input to be used to test the solution.
     */
    protected ?string $exampleInput = '^v^v^v^v^v';

    /**
     * Process the input from the challenge.
     *
     * @return string[]
     */
    public function transformInput(string $input): array
    {
        return str_split($input);
    }

    /**
     * Run the first part of the challenge.
     */
    public function part1(): int
    {
        $houses = 0;

        foreach ($this->createHouseGrid($this->getInput()) as $row) {
            $houses += count(array_filter($row));
        }

        return $houses;
    }

    /**
     * Run the second part of the challenge.
     */
    public function part2(): int
    {
        $houses = 0;

        foreach ($this->createHouseGrid($this->getInput(), 2) as $row) {
            $houses += count(array_filter($row));
        }

        return $houses;
    }

    /**
     * Create house grid from given directions.
     *
     * @return int[][]
     */
    public function createHouseGrid(array $directions, int $totalPlayers = 1): array
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
}
