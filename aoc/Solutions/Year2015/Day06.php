<?php

namespace Mike\AdventOfCode\Solutions\Year2015;

use Mike\AdventOfCode\Solutions\Solution;

use Mike\AdventOfCode\Solutions\Year2015\Day06\Instruction;
use Mike\AdventOfCode\Solutions\Year2015\Day06\Position;

/**
 * @method  \Mike\AdventOfCode\Solutions\Year2015\Day06\Instruction[]  getInput()  Get the ideal lightning configuration.
 */
class Day06 extends Solution
{
    /**
     * The example input to be used to test the solution.
     */
    protected ?string $exampleInput = 'turn on 0,0 through 999,999';

    /**
     * Process the input from the challenge.
     *
     * @return \Mike\AdventOfCode\Solutions\Year2015\Day06\Instruction[]
     */
    public function transformInput(string $input): array
    {
        preg_match_all('/^(.*?)\s*(\d{1,3}),(\d{1,3})\s+through\s+(\d{1,3}),(\d{1,3})\s*$/m', $input, $matches, PREG_SET_ORDER);

        return array_map(function (array $match): Instruction {
            return (new Instruction($match[1]))
                ->setFrom(new Position($match[2], $match[3]))
                ->setTo(new Position($match[4], $match[5]));
        }, $matches);
    }

    /**
     * Run the first part of the challenge.
     */
    public function part1(): int
    {
        $grid = array_2d_grid(1000, 1000, 0);

        $lit = 0;

        foreach ($this->getInput() as $ins) {
            for ($y = $ins->from->y; $y <= $ins->to->y; $y++) {
                for ($x = $ins->from->x; $x <= $ins->to->x; $x++) {
                    $prev = $grid[$y][$x];
                    $value = $prev;

                    if ($ins->command == 'toggle') {
                        $value = $prev ? 0 : 1;
                    }

                    if ($ins->command == 'turn on') {
                        $value = 1;
                    }

                    if ($ins->command == 'turn off') {
                        $value = 0;
                    }

                    $grid[$y][$x] = $value;

                    if ($prev == 1 && $value == 0) {
                        $lit--;
                    }

                    if ($prev == 0 && $value == 1) {
                        $lit ++;
                    }
                }
            }
        }

        return $lit;
    }

    /**
     * Run the second part of the challenge.
     */
    public function part2(): int
    {
        $grid = array_2d_grid(1000, 1000, 0);

        foreach ($this->getInput() as $ins) {
            for ($y = $ins->from->y; $y <= $ins->to->y; $y++) {
                for ($x = $ins->from->x; $x <= $ins->to->x; $x++) {
                    $value = $grid[$y][$x];

                    if ($ins->command == 'toggle') {
                        $value += 2;
                    }

                    if ($ins->command == 'turn on') {
                        $value += 1;
                    }

                    if ($ins->command == 'turn off') {
                        $value -= 1;
                    }

                    if ($value < 0) {
                        $value = 0;
                    }

                    $grid[$y][$x] = $value;
                }
            }
        }

        return array_sum(array_map('array_sum', $grid));
    }
}
