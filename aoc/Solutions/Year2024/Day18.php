<?php

namespace Mike\AdventOfCode\Solutions\Year2024;

use Mike\AdventOfCode\Solutions\Solution;
use Symfony\Component\Console\Output\ConsoleSectionOutput;

class Day18 extends Solution
{
    /**
     * The example input to be used to test the solution.
     */
    protected ?string $exampleInput = <<<TXT
    5,4
    4,2
    4,5
    3,0
    2,1
    6,3
    2,4
    1,5
    0,6
    3,3
    2,6
    5,1
    1,2
    5,5
    2,5
    6,5
    1,4
    0,4
    6,4
    1,1
    6,1
    1,0
    0,5
    1,6
    2,0
    TXT;

    #region  Memory values
    const EMPTY = 0;
    const CORRUPTED = 1;
    const PATH = 2;
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
     * Process the input from the challenge.
     */
    public function transformInput(string $input): array
    {
        return array_map(fn($line) => array_map('intval', explode(',', $line)), split_lines($input));
    }

    /**
     * Get the map.
     *
     * @param  integer  $limit  The max corrupted bytes to add to the memory grid.
     * @return array<integer[]>
     */
    public function memory(int $limit = 0): array
    {
        [$w, $h] = $this->testing ? [7, 7] : [71, 71];

        $coords = array_slice($this->getInput(), 0, $limit ?: null);

        return grid_make($w, $h, fn ($x, $y) => in_array([$x, $y], $coords) ? static::CORRUPTED : static::EMPTY);
    }

    /**
     * Render the memory.
     *
     * @param  array<integer[]>  $memory
     * @param  string|null  $status  Additional end line to display something at the start of the grid.
     * @return void
     */
    protected function render(array $memory, ?string $status = null): void
    {
        if (! $this->getIO()->getOutput()->isVerbose() || $this->cantRender) {
            return;
        }

        [$w, $h] = [count($memory[0]), count($memory)];
        [$tw, $th] = [$this->app()->terminal->getWidth(), $this->app()->terminal->getHeight()];

        if ($tw < $w) {
            $this->getIO()->warn("Unable to render memory, needed terminal width of <white>$w</> characters. Got <white>$tw</>.");
            $this->cantRender = true;
        }

        // if ($th < $h) {
        //     $this->getIO()->warn("Unable to render memory, needed terminal height of <white>$h</> lines. Got <white>$th</>.");
        //     $this->cantRender = true;
        // }

        if ($this->cantRender) {
            return;
        }

        if (! isset($this->renderSection)) {
            $this->renderSection = $this->getIO()->section();

            $this->renderSection->setMaxHeight($h+1); // +1 for status
        }

        $section = $this->renderSection;

        $section->clear();

        if ($status) {
            $section->writeln($status);
        }

        grid_print($memory, [
            static::CORRUPTED => '#',
            static::EMPTY => '.',
            static::PATH => '<fg=red>O</>',
        ], fn($line) => $section->writeln($line));

        // prevent vscode terminal flickering
        usleep(1);
    }

    /**
     * Find the shortest path of two points in 2d grid using the BFS algorithm.
     *
     * @template T
     * @param  array<T[]>  $grid  The grid.
     * @param  mixed[]|mixed  $valid  The valid values for the grid that can be moved to.
     * @param  array{int,int}  $start The start point.
     * @param  array{int,int}  $end The end point.
     * @return array<array{int,int}>|false  The resulting shortest path points or false if cannot find a valid path.
     */
    protected function bfsShortestPath(array $grid, $valid, array $start, array $end): array|false
    {
        $directions = [
            // top,  right,  bottom, left
            [0, -1], [1, 0], [0, 1], [-1, 0],
        ];

        $stack = [[$start, [$start]]];
        $visited = [$start];
        $valid = is_array($valid) ? $valid : [$valid];

        while (! empty($stack)) {
            [$pos, $path] = array_shift($stack);
            [$x, $y] = $pos;

            if ($pos == $end) {
                return $path;
            }

            foreach ($directions as [$dx, $dy]) {
                [$nx, $ny] = $npos = [$x + $dx, $y + $dy];

                if (isset($grid[$ny][$nx]) && in_array($grid[$ny][$nx], $valid) && !in_array($npos, $visited)) {
                    $stack[] = [$npos, array_merge($path, [$npos])];
                    $visited[] = $npos;
                }
            }
        }

        return false;
    }

    /**
     * Run the first part of the challenge.
     */
    public function part1(): int
    {
        $memory = $this->memory($this->testing ? 12 : 1024);
        $end = $this->testing ? [6,6] : [70,70];

        $shortest = $this->bfsShortestPath($memory, static::EMPTY, [0,0], $end);

        if ($shortest === false) {
            $this->render($memory);
            $this->getIO()->error("Unable to find shortest path for memory.");

            return 0;
        }

        $this->render(grid_set($memory, $shortest, static::PATH));

        return count($shortest) - 1; // -1 for start position
    }

    /**
     * Run the second part of the challenge.
     */
    public function part2(): string
    {
        $start = $this->testing ? 12 : 1024;
        $max = count($memory = $this->getInput());
        $end = $this->testing ? [6,6] : [70,70];

        $i = $start;

        $shortest = $this->bfsShortestPath($this->memory($start), static::EMPTY, [0,0], $end);

        while ($shortest !== false || $i == $max) {
            $next = $memory[$i++];

            if (! in_array($next, $shortest)) {
                continue;
            }

            $shortest = $this->bfsShortestPath($this->memory($i), static::EMPTY, [0,0], $end);
        }

        if ($shortest !== false) {
            $this->getIO()->error("Unable to find the first byte that blocks the exit.");

            return '';
        }

        return implode(',', $memory[$i-1]);
    }
}
