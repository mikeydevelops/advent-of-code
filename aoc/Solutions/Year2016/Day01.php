<?php

namespace Mike\AdventOfCode\Solutions\Year2016;

use Mike\AdventOfCode\Solutions\Solution;
use Mike\AdventOfCode\Solutions\Year2016\Day01\Instruction;

/**
 * @method  \Mike\AdventOfCode\Solutions\Year2016\Day01\Instruction[]  getInput()  Get the directions.
 */
class Day01 extends Solution
{
    /**
     * The example input to be used to test the solution.
     */
    protected ?string $exampleInput = 'R8, R4, R4, R8';

    /**
     * The result of the solution.
     *
     * @var int[]
     */
    protected array $result = [];

    /**
     * Process the input from the challenge.
     *
     * @return \Mike\AdventOfCode\Solutions\Year2016\Day01\Instruction[]
     */
    public function transformInput(string $input): array
    {
        $directions = explode(', ', trim($input));

        return array_map(function ($direction) {
            $blocks = intval(substr($direction, 1));
            $direction = substr($direction, 0, 1) == 'R' ? 'right' : 'left';

            return new Instruction($direction, $blocks);
        }, $directions);
    }

    /**
     * Hook before all parts are run.
     */
    public function before(): void
    {
        $this->io->info('Finding Easter Bunny HQ...');
        $facing = 0;
        $x = 0;
        $y = 0;

        $first = null;
        $visits = [];

        foreach ($this->getInput() as $ins) {
            $facing += $ins->direction == 'left' ? -1 : 1;

            $facing = $facing > 3 ? 0 : ($facing < 0 ? 3 : $facing);

            foreach (range(1, $ins->blocks) as $_) {
                $facing == 0 ? ($y ++) : null;
                $facing == 1 ? ($x ++) : null;
                $facing == 2 ? ($y --) : null;
                $facing == 3 ? ($x --) : null;

                if (isset($first)) {
                    continue;
                }

                $loc = "$x,$y";

                if (! isset($visits[$loc])) {
                    $visits[$loc] = 0;
                }

                $visits[$loc] ++;

                if ($visits[$loc] == 2) {
                    $first = abs($x) + abs($y);
                }
            }

        }

        $this->result = [abs($x) + abs($y), (int) $first];
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
}
