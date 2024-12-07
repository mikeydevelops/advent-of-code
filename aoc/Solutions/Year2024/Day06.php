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
        $values = [
            '.' => '0 ',
            '#' => '-1 ',
            '^' => '1 ',
            '>' => '2 ',
            'v' => '3 ',
            '<' => '4 ',
        ];

        $input = str_replace(array_keys($values), array_values($values), $input);

        $grid = preg_split('/\r?\n/', $input);
        $grid = array_map(fn($row) => array_map('intval', explode(' ', trim($row))), $grid);

        [$x, $y] = array_search_2d([1, 2, 3, 4], $grid, true);

        $dir = $grid[$y][$x] - 1;
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
        $state = $floyd = $prevState = $initialState;

        while (! $this->hasLeftMap($grid, $state)) {
            $prevState = $state;

            $grid[$state[1]][$state[0]] ++;

            $state = $this->move($grid, $state);

            $floyd = $this->move($grid, $floyd);

            if (! $this->hasLeftMap($grid, $floyd)) {
                $floyd = $this->move($grid, $floyd);
            }

            if ($state === $floyd) {
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
        [$grid, $state] = $this->getInput();
        [$grid, $state] = $this->simulate($grid, $state);

        return count(array_filter(array_flat($grid), fn($v) => $v > 0));
    }

    /**
     * Run the second part of the challenge.
     */
    public function part2(): int
    {
        $loops = 0;

        [$grid, $state] = $this->getInput();

        $empty = [];

        foreach (walk_2d_grid($grid) as [$x, $y, $v]) {
            if ($v === 0) {
                $empty[] = [$x, $y];
            }
        }

        $bar = $this->io->getOutput()->withProgress($empty, function ($pos, $grid, $state) use (&$loops) {
            $grid[$pos[1]][$pos[0]] = -2;

            if ($this->simulate($grid, $state) === false) {
                $loops++;
            }
        }, $grid, $state);

        $bar->clear();

        return $loops;
    }
}
