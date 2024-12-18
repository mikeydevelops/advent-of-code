<?php

namespace Mike\AdventOfCode\Solutions\Year2024;

use Generator;
use Mike\AdventOfCode\Solutions\Solution;
use Mike\AdventOfCode\Solutions\Year2024\Day14\Robot;
use Symfony\Component\Console\Output\ConsoleSectionOutput;

class Day14 extends Solution
{
    /**
     * The example input to be used to test the solution.
     */
    protected ?string $exampleInput = <<<TXT
    p=0,4 v=3,-3
    p=6,3 v=-1,-3
    p=10,3 v=-1,2
    p=2,0 v=2,-1
    p=0,0 v=1,3
    p=3,0 v=-2,-2
    p=7,6 v=-1,-3
    p=3,0 v=-1,-2
    p=9,3 v=2,3
    p=7,3 v=-1,2
    p=2,4 v=2,-3
    p=9,5 v=-3,-3
    TXT;

    /**
     * Wether or not the robot space can be rendered.
     * It may not be rendered if there is no space in the terminal.
     *
     * @var boolean
     */
    protected bool $cantRender = false;

    /**
     * The section used to render the images from the robots.
     *
     * @var \Symfony\Component\Console\Output\ConsoleSectionOutput|null
     */
    protected ?ConsoleSectionOutput $renderSection = null;

    /**
     * Read the robots from the input.
     *
     * @return \Generator<\Mike\AdventOfCode\Solutions\Year2024\Day14\Robot>
     */
    public function transformInput(string $input): array
    {
        $robots = array_map(function (string $line) {
            $params = array_map(
                fn($p) => array_map('intval', explode(',', substr($p, 2))),
                explode(' ', $line)
            );

            return new Robot($params[0], $params[1]);
        }, split_lines($input));

        return $robots;
    }

    /**
     * Count the amount of robots in quadrants of the space they are in.
     *
     * @param  \Mike\AdventOfCode\Solutions\Year2024\Day14\Robot[]  $robots
     * @param  array{int,int}  $space  The width and the height of the space.
     * @return array{int,int,int,int}
     */
    protected function robotsInQuadrants(array $robots, array $space): array
    {
        $q1 = $q2 = $q3 = $q4 = 0;

        $xmid = floor($space[0] / 2);
        $ymid = floor($space[1] / 2);

        foreach ($robots as $robot) {
            if ($robot->x == $xmid || $robot->y == $ymid) {
                continue;
            }

            if ($robot->y < $ymid) {
                $quad = $robot->x < $xmid ? 'q1' : 'q2';
            }

            if ($robot->y > $ymid) {
                $quad = $robot->x < $xmid ? 'q3' : 'q4';
            }

            // using this magic, we can increment the variable corresponding to the value of $quad.
            $$quad ++;
        }

        return [$q1, $q2, $q3, $q4];
    }

    /**
     * Render the space with the robots.
     *
     * @param  \Mike\AdventOfCode\Solutions\Year2024\Day14\Robot[]  $robots
     * @param  array{int,int}  $space
     * @param  string|null  $status  Additional end line to display something at the end of the grid.
     * @return void
     */
    protected function render(array $robots, array $space, ?string $status = null): void
    {
        if (! $this->getIO()->getOutput()->isVerbose() || $this->cantRender) {
            return;
        }

        [$w, $h] = $space;
        [$tw, $th] = [$this->app()->terminal->getWidth(), $this->app()->terminal->getHeight()];

        if ($tw < $w) {
            $this->getIO()->warn("Unable to render robots, needed terminal width of <white>$w</> characters. Got <white>$tw</>.");
            $this->cantRender = true;
        }

        // if ($th < $h) {
        //     $this->getIO()->warn("Unable to render robots, needed terminal height of <white>$h</> lines. Got <white>$th</>.");
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

        $grid = grid_make($w, $h, 0);

        foreach ($robots as $robot) {
            $grid[$robot->y][$robot->x] ++;
        }

        $section->clear();

        grid_print($grid, [' ', ...array_fill(0, count($robots), '█')], fn($line) => $section->writeln($line));

        if ($status) {
            $section->writeln($status);
        }

        usleep(150 * 1000);
    }

    /**
     * Calculate the safety factor.
     *
     * @param  \Mike\AdventOfCode\Solutions\Year2024\Day14\Robot[]  $robots
     * @param  array{int,int}  $space
     * @return integer
     */
    protected function safetyFactor(array $robots, array $space): int
    {
        return array_product($this->robotsInQuadrants($robots, $space));
    }

    /**
     * Run the first part of the challenge.
     */
    public function part1(): int
    {
        $space = $this->testing ? [11, 7] : [101, 103];

        /** @var \Mike\AdventOfCode\Solutions\Year2024\Day14\Robot[] */
        $robots = $this->getInput();

        foreach ($robots as $robot) {
            $robot->move($space, 100);
        }

        return $this->safetyFactor($robots, $space);
    }

    /**
     * Run the second part of the challenge.
     */
    public function part2(): int
    {
        $space = $this->testing ? [11, 7] : [101, 103];

        /** @var \Mike\AdventOfCode\Solutions\Year2024\Day14\Robot[] */
        $robots = $this->getInput();

        $total = array_product($space); // after those seconds, robots repeat position.

        // find lines containing at least 15 robots next to each other.
        $continuous = function ($row) {
            $continuous = 0;

            $row = array_map(fn($robot) => $robot->x, $row);
            sort($row);

            $prev = 0;
            foreach ($row as $x) {
                if ($x - $prev == 1) {
                    $continuous++;
                } else {
                    $continuous = 0;
                }

                $prev = $x;
            }

            return $continuous >= 15;
        };

        $result = null;

        for ($i = 0; $i <= $total; $i++) {
            foreach ($robots as $robot) {
                $robot->move($space);
            }

            $rows = array_group_by($robots, fn(Robot $r) => $r->y);
            $rows = array_filter($rows, $continuous);

            if (count($rows)) {
                $this->render($robots, $space, "$i seconds.");

                $result = $result ?? ($i + 1);
            }
        }

        return $result ?? 0;
    }
}
