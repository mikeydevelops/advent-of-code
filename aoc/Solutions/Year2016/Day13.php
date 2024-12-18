<?php

namespace Mike\AdventOfCode\Solutions\Year2016;

use Mike\AdventOfCode\Solutions\Solution;
use Mike\AdventOfCode\Solutions\Year2016\Day13\Layout;
use Mike\AdventOfCode\Solutions\Year2016\Day13\Location;

/**
 * @method  int  getInput()  Get the designer's favorite number.
 */
class Day13 extends Solution
{
    /**
     * The example input to be used to test the solution.
     */
    protected ?string $exampleInput = '10';

    /**
     * The result of the challenge
     *
     * @var int[]
     */
    protected array $result = [];

    /**
     * Process the input from the challenge.
     */
    public function transformInput(string $input): int
    {
        return intval($input);
    }

    /**
     * Hook before any part is run.
     *
     * @see https://en.wikipedia.org/wiki/Lee_algorithm
     */
    public function before(): void
    {
        Location::$favorite = $this->getInput();

        $target = Location::fromArray($this->testing ? [7, 4] : [31, 39]);
        $width = $target->x+2;
        $height = $target->y+2;

        $grid = grid_make($width, $height, 0);

        $queue = [[1, 1, []]];
        $shortestPath = [];
        $unique = 0;
        $limit = 50;

        do {
            [$x, $y, $history] = array_shift($queue);

            $current = $grid[$y][$x];

            $history[] = [$x, $y];

            $moves = [
                'top' => new Location($x, $y - 1),
                'left' => new Location($x - 1, $y),
                'bottom' => new Location($x, $y + 1),
                'right' => new Location($x + 1, $y),
            ];

            $moves = array_filter($moves, function (Location $loc) use ($width, $height, $grid) {
                return $loc->x > -1 && $loc->x < $width
                    && $loc->y > -1 && $loc->y < $height
                    && $grid[$loc->y][$loc->x] === 0
                    && $loc->isOpenSpace();
            });

            foreach ($moves as $move) {
                $grid[$move->y][$move->x] = $current + 1;

                if ($grid[$move->y][$move->x] <= $limit) {
                    $unique ++;
                }

                if ($target->is($move)) {
                    $shortestPath = $history;

                    break 2;
                }

                $queue[] = [$move->x, $move->y, $history];
            }
        } while (! empty($queue));

        $this->result = [$grid[$target->y][$target->x], $unique];

        // $this->renderGrid($grid, $shortestPath);
    }

    /**
     * Run the first part of the challenge.
     */
    public function part1(): int
    {
        return $this->result[0];
    }

    /**
     * Run the second part of the challenge.
     */
    public function part2(): int
    {
        return $this->result[1];
    }

    /**
     * Render the grid visually.
     */
    public function renderGrid(array $grid, array $path = []): static
    {
        $width = count($grid[0]);
        $height = count($grid);

        $l = strlen($width);
        $i = ceil($width / 10);

        $header = implode('', range(0, min($width, 9)));

        $this->io->line('<fg=gray>' . str_repeat(' ', $l+1) . substr(str_repeat($header, $i), 0, $width+1).'</>');

        foreach (range(0, $height) as $idx => $y) {
            $this->io->write('<fg=gray>' . str_pad($idx, $l, ' ', STR_PAD_LEFT) . '</> ');

            foreach (range(0, $width) as $x) {
                if (in_array([$x, $y], $path)) {
                    $this->io->write('<info>█</info>');

                    continue;
                }

                $this->io->write('<fg=gray>' . ((new Location($x, $y))->isWall() ? '*' : ' ') . '</>');
            }

            $this->io->newLine();
        }

        return $this;
    }
}
