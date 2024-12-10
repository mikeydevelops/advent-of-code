<?php

namespace Mike\AdventOfCode\Solutions\Year2024;

use Mike\AdventOfCode\Solutions\Solution;

class Day10 extends Solution
{
    /**
     * The example input to be used to test the solution.
     */
    protected ?string $exampleInput = <<<TXT
    89010123
    78121874
    87430965
    96549874
    45678903
    32019012
    01329801
    10456732
    TXT;

    /**
     * Process the input from the challenge.
     */
    public function transformInput(string $input): array
    {
        return grid_parse($input);
    }

    /**
     * Find all trails from starting point to a point that reaches specified target value.
     *
     * @param  array  $grid
     * @param  integer  $startX  The horizontal, X, starting position.
     * @param  integer  $startY  The vertical, Y, starting position.
     * @param  integer  $target  The target value to reach.
     * @return array<array<array{int,int}>>
     */
    public function findTrails(array $grid, int $startX, int $startY, int $target): array
    {
        $trails = [
            [
                [$startX, $startY, $grid[$startY][$startX]]
            ],
        ];

        while (true) {
            foreach ($trails as $idx => $trail) {
                $last = array_pop($trail);

                $adj = array_filter(
                    grid_get_adjacent_xy($grid, $last[0], $last[1]),
                    // check to see if adjacent value increments and it is not a diagonal.
                    fn($cell, $k) => $cell[2] === $last[2] + 1 && strpos($k, '-') === false,
                    ARRAY_FILTER_USE_BOTH
                );

                if (empty($adj)) {
                    unset($trails[$idx]);
                    continue;
                }

                $trails[$idx][] = array_pop($adj);


                foreach ($adj as $leg) {
                    $trails[] = array_merge($trail, [$last, $leg]);
                }
            }

            // check to see if all trails reached the target value.
            // if any of them didn't start over.
            // if all of them did, end the while loop.
            foreach ($trails as $t) {
                if (end($t)[2] !== $target) {
                    continue 2;
                }
            }

            // should be reached only when all trails have reached the target value.
            break;
        }

        return $trails;
    }

    /**
     * Find all trail heads that can reach the end of the trails.
     *
     * @param  array  $grid
     * @return array<string,array<array<array{int,int}>>>
     */
    public function findTrailHeads(array $grid): array
    {
        $trailHeads = [];

        foreach (grid_search($grid, 0, true) as [$x, $y]) {
            $trailHeads["$x,$y"] = $this->findTrails($grid, $x, $y, 9);
        }

        return $trailHeads;
    }

    /**
     * Calculate the trail head's score based on how many trails end.
     *
     * @param  array  $trailHead
     * @return integer
     */
    public function trailHeadScore(array $trailHead): int
    {
        $ends = [];

        foreach ($trailHead as $trail) {
            [$x, $y] = array_pop($trail);

            $ends[] = "$x,$y";
        }

        return count(array_unique($ends));
    }

    /**
     * Calculate the trail head's rating based on how many unique trails it has.
     *
     * @param  array  $trailHead
     * @return integer
     */
    public function trailHeadRating(array $trailHead): int
    {
        return count($trailHead);
    }

    /**
     * Run the first part of the challenge.
     */
    public function part1(): int
    {
        return array_sum(array_map([$this, 'trailHeadScore'], $this->findTrailHeads($this->getInput())));
    }

    /**
     * Run the second part of the challenge.
     */
    public function part2()
    {
        return array_sum(array_map([$this, 'trailHeadRating'], $this->findTrailHeads($this->getInput())));
    }
}
