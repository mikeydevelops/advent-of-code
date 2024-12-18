<?php

namespace Mike\AdventOfCode\Solutions\Year2024;

use Mike\AdventOfCode\Solutions\Solution;
use Symfony\Component\Console\Output\ConsoleSectionOutput;

class Day16 extends Solution
{
    /**
     * The example input to be used to test the solution.
     */
    protected ?string $exampleInput = <<<TXT
    ###############
    #.......#....E#
    #.#.###.#.###.#
    #.....#.#...#.#
    #.###.#####.#.#
    #.#.#.......#.#
    #.#.#####.###.#
    #...........#.#
    ###.#.#####.#.#
    #...#.....#.#.#
    #.#.#.###.#.#.#
    #.....#...#.#.#
    #.###.#.#.#.#.#
    #S..#.....#...#
    ###############
    TXT;

    #region  Map values
    const WALL = 0;
    const EMPTY = 1;
    const START = 2;
    const END = 3;
    const NORTH = 4;
    const EAST = 5;
    const SOUTH = 6;
    const WEST = 7;
    #endregion

    /**
     * Wether or not the warehouse can be rendered.
     * It may not be rendered if there is no space in the terminal.
     *
     * @var boolean
     */
    protected bool $cantRender = false;

    /**
     * The section used to render the warehouse.
     *
     * @var \Symfony\Component\Console\Output\ConsoleSectionOutput|null
     */
    protected ?ConsoleSectionOutput $renderSection = null;

    /**
     * Get the map.
     *
     * @return array<integer[]>
     */
    public function map(): array
    {
        $stream = $this->streamInput();
        $map = [];
        $keys = [
            '#' => static::WALL,
            '.' => static::EMPTY,
            'S' => static::START,
            'E' => static::END,
        ];

        while ($row = trim(fgets($stream) ?: '')) {
            $map[] = array_map(fn($c) => $keys[$c], str_split($row));
        }

        fclose($stream);

        return $map;
    }

    /**
     * Render the space with the robots.
     *
     * @param  array<integer[]>
     * @param  string|null  $status  Additional end line to display something at the start of the grid.
     * @param  boolean  $overwrite  Overwrite the previous render.
     * @return void
     */
    protected function render(array $map, ?string $status = null, bool $overwrite = true): void
    {
        if (! $this->getIO()->getOutput()->isVerbose() || $this->cantRender) {
            return;
        }

        [$w, $h] = [count($map[0]), count($map)];
        [$tw, $th] = [$this->app()->terminal->getWidth(), $this->app()->terminal->getHeight()];

        if ($tw < $w) {
            $this->getIO()->warn("Unable to render map, needed terminal width of <white>$w</> characters. Got <white>$tw</>.");
            $this->cantRender = true;
        }

        // if ($th < $h) {
        //     $this->getIO()->warn("Unable to render map, needed terminal height of <white>$h</> lines. Got <white>$th</>.");
        //     $this->cantRender = true;
        // }

        if ($this->cantRender) {
            return;
        }

        if (! isset($this->renderSection)) {
            $this->renderSection = $this->getIO()->section();

            $this->renderSection->setMaxHeight($h+1); // +1 for status
        }

        $section = $overwrite ? $this->renderSection : $this->getIO()->getOutput();

        if ($overwrite) {
            $section->clear();
        }

        if ($status) {
            $section->writeln($status);
        }

        grid_print($map, [
            static::WALL => '#',
            static::EMPTY => '.',
            static::START => '<fg=yellow>S</>',
            static::END => '<fg=green>E</>',
            static::NORTH => '<fg=red>^</>',
            static::EAST => '<fg=red>></>',
            static::SOUTH => '<fg=red>v</>',
            static::WEST => '<fg=red><</>',
        ], fn($line) => $section->writeln($line));

        // prevent vscode terminal flickering
        usleep(1);
    }

    /**
     * Find the lowest cost path to from start to end.
     *
     * @template T
     * @param  array<T[]>  $grid  The grid.
     * @param  mixed[]|mixed  $valid  The valid values for the grid that can be moved to.
     * @param  array{int,int}  $start The start point.
     * @param  array{int,int}  $end The end point.
     * @param  integer  $limit  The maximum paths that reach the end.
     * @return array<array{int,array<array{int,int}>}>|false  The resulting aStarCheapestPath path points or false if cannot find a valid path.
     */
    protected function aStarCheapestPath(array $grid, $valid, array $start, array $end, int $limit = 0): array|false
    {
        $directions = [
            [0, -1, static::NORTH],
            [1, 0, static::EAST],
            [0, 1, static::SOUTH],
            [-1, 0, static::WEST],
        ];

        $forwardCost = 1;
        $moveCost = 1000;

        $startDir = static::EAST; // initial direction

        // using manhattan algorithm to score the moves
        $manhattan = fn($score, $a, $b) => $score + abs($a[0] - $b[0]) + abs($a[1] - $b[1]);

        $stack = [[
            $manhattan(0, $start, $end),
            0,
            $start,
            $startDir,
            [[...$start, $startDir]]
        ]];
        $visited = [];
        $valid = is_array($valid) ? $valid : [$valid];
        $back = [
            static::NORTH => static::SOUTH,
            static::EAST => static::WEST,
            static::SOUTH => static::NORTH,
            static::WEST => static::EAST,
        ];

        $best = [];

        while (! empty($stack)) {
            // sort descending so we can use array_pop
            usort($stack, function ($a, $b) {
                return $b[0] <=> $a[0];
            });

            [$totalCost, $cost, $pos, $dir, $path] = array_pop($stack);
            [$x, $y] = $pos;

            if ($pos == $end) {
                $best[] = [$cost, $path];

                if ($limit && count($best) === $limit) {
                    return $best;
                }

                continue;
            }

            if (in_array([$x, $y, $dir], $visited)) {
                continue;
            }

            $visited[] = [$x, $y, $dir];

            foreach ($directions as [$dx, $dy, $d]) {
                if ($d === $back[$dir]) { // cannot turn backwards, only left or right
                    continue;
                }

                if ($dir !== $d) { // turn if not facing the same way
                    $stack[] = [
                        $manhattan($cost + $moveCost, $pos, $end),
                        $cost + $moveCost,
                        $pos,
                        $d,
                        array_merge($path, [[...$pos, $d]]),
                    ];

                    continue;
                }

                [$nx, $ny] = $npos = [$x + $dx, $y + $dy];

                if (isset($grid[$ny][$nx]) && in_array($grid[$ny][$nx], $valid)) {
                    $stack[] = [
                        $manhattan($cost + $forwardCost, $npos, $end),
                        $cost + $forwardCost,
                        $npos,
                        $dir,
                        array_merge($path, [[...$npos, $dir]]),
                    ];
                }
            }
        }

        return empty($best) ? false : $best;
    }

    /**
     * Run the first part of the challenge.
     */
    public function part1(): int
    {
        $map = $this->map();
        $start = grid_search($map, static::START, false)->current();
        $end = grid_search($map, static::END, false)->current();

        $cheapest = $this->aStarCheapestPath($map, [static::EMPTY, static::END], $start, $end, 1);

        if ($cheapest === false) {
            $this->render($map);
            $this->getIO()->error("Unable to find cheapest path for map.");

            return 0;
        }

        $cheapest = $cheapest[0]; // get the first route

        $this->render(grid_set($map, $cheapest[1], function (array $point, int $current) {
            return in_array($current, [static::START, static::END]) ? $current : $point[2];
        }));

        return $cheapest[0];
    }

    /**
     * Run the second part of the challenge.
     */
    public function part2(): int
    {
        $map = $this->map();
        $start = grid_search($map, static::START, false)->current();
        $end = grid_search($map, static::END, false)->current();

        $cheapest = $this->aStarCheapestPath($map, [static::EMPTY, static::END], $start, $end);

        if ($cheapest === false) {
            $this->render($map);
            $this->getIO()->error("Unable to find cheapest path for map.");

            return 0;
        }

        foreach ($cheapest as $route) {
            $this->render(grid_set($map, $route[1], function (array $point, int $current) {
                return in_array($current, [static::START, static::END]) ? $current : $point[2];
            }), "Score: $route[0]", overwrite: false);
        }

        return 0;
    }
}
