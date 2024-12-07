<?php

namespace Mike\AdventOfCode\Solutions\Year2024;

use Mike\AdventOfCode\Solutions\Solution;

class Day06 extends Solution
{
    /**
     * The example input to be used to test the solution.
     */
    protected ?string $exampleInput = <<<TXT
    ....#.....
    .........#
    ..........
    ..#.......
    .......#..
    ..........
    .#..^.....
    ........#.
    #.........
    ......#...
    TXT;

    /**
     * Process the input from the challenge.
     */
    public function transformInput(string $input): array
    {
        // map to replace initial grid with usable values.
        // every value below 0 is an obstacle, so the guard cannot go there.
        // 0 means a free space. above 1 holds the starting direction of the guard.
        $values = [
            '.' => '0 ',
            '#' => '-1 ',
            '^' => '1 ',
            // not necessary, but just for completeness.
            '>' => '2 ',
            'v' => '3 ',
            '<' => '4 ',
        ];

        $input = str_replace(array_keys($values), array_values($values), $input);

        $grid = split_lines($input);
        // split each line on space and cast to integer.
        $grid = array_map(fn($row) => array_map('intval', explode(' ', trim($row))), $grid);

        // find the initial x and y of the guard.
        [$x, $y] = array_search_2d([1, 2, 3, 4], $grid, true);

        // direction is zero based
        $dir = $grid[$y][$x] - 1;
        // reset to 1, from now on, the value will be how
        // many times the guard has passed this position.
        $grid[$y][$x] = 1;

        return [$grid, [$x, $y, $dir]];
    }

    /**
     * Move the player forward/
     *
     * @template T
     *
     * @param array $grid
     * @param  T $state
     * @return T|false  false if move is impossible.
     */
    public function move(array $grid, array $state): array|false
    {
        [$x, $y, $direction] = $state;

        $directions = [
            [ 0, -1], // top
            [ 1,  0], // right
            [ 0,  1], // bottom
            [-1,  0], // left
        ];

        $dir = $directions[$direction];

        $newX = $x + $dir[0];
        $newY = $y + $dir[1];

        if (! isset($grid[$newY][$newX])) {
            return [$newX, $newY, $direction];
        }

        $v = $grid[$newY][$newX];

        // obstacle, turn
        if ($v < 0) {
            $direction = ($direction + 1) % 4;

            return $this->move($grid, [$x, $y, $direction]);
        }

        return [$newX, $newY, $direction];
    }

    /**
     * Determine if given state has left the map.
     */
    public function hasLeftMap(array $grid, array $state): bool
    {
        return ! isset($grid[$state[1]][$state[0]]);
    }

    /**
     * Simulate guard patrol route.
     *
     * @param  array{int,int,int}  $initialState  The initial state of the guard. X, Y, Facing Direction
     * @return array|false False when the guard loops.
     */
    public function simulate(array $grid, array $initialState): array|false
    {
        $guard = $hare = $prevState = $initialState;

        while (! $this->hasLeftMap($grid, $guard)) {
            $prevState = $guard;

            $grid[$guard[1]][$guard[0]] ++;

            $guard = $this->move($grid, $guard);

            // the hare moves twice per round
            $hare = $this->move($grid, $hare);
            if (! $this->hasLeftMap($grid, $hare)) {
                $hare = $this->move($grid, $hare);
            }

            // according to Floyd's tortoise and hare, when the guard and the hare have exact same
            // x, y, and direction, it means the guard has made a loop, so we quit
            if ($guard === $hare) {
                return false;
            }
        }

        return [$grid, $prevState];
    }

    /**
     * Run the first part of the challenge.
     */
    public function part1(): int
    {
        [$grid, $guard] = $this->getInput();
        [$grid, $guard] = $this->simulate($grid, $guard);

        // flatten the grid and count all values which the guard has passed.
        return count(array_filter(array_flat($grid), fn($v) => $v > 0));
    }

    /**
     * Run the second part of the challenge.
     *
     * just plain brute force, takes around a 1min to complete
     */
    public function part2(): int
    {
        $loops = 0;

        [$grid, $guard] = $this->getInput();

        $empty = [];

        foreach (walk_2d_grid($grid) as [$x, $y, $v]) {
            if ($v === 0) {
                $empty[] = [$x, $y];
            }
        }

        $bar = $this->io->getOutput()->withProgress($empty, function ($pos, $grid, $guard) use (&$loops) {
            $grid[$pos[1]][$pos[0]] = -2;

            if ($this->simulate($grid, $guard) === false) {
                $loops++;
            }
        }, $grid, $guard);

        $bar->clear();

        return $loops;
    }
}
