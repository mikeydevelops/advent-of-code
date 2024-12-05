<?php

namespace Mike\AdventOfCode\Solutions\Year2019;

use Mike\AdventOfCode\Solutions\Solution;
use Mike\AdventOfCode\Solutions\Year2019\Day02\IntCode;

class Day02 extends Solution
{
    /**
     * The example input to be used to test the solution.
     */
    protected ?string $exampleInput = '1,9,10,3,2,3,11,0,99,30,40,50';

    /**
     * Process the input from the challenge.
     *
     * @return integer[]
     */
    public function transformInput(string $input): array
    {
        return array_map('intval', preg_split('/,\s*/', $input));
    }

    /**
     * Run the first part of the challenge.
     */
    public function part1(): int
    {
        $program = $this->getInput();

        if (! $this->testing) {
            $program[1] = 12;
            $program[2] = 2;
        }

        return (new IntCode)->load($program)->run();
    }

    /**
     * Run the second part of the challenge.
     */
    public function part2(): int
    {
        $program = $this->getInput();

        $inputs = $this->testing
            ? [[9, 10]]
            : array_combinations(range(0, 99), 2);

        foreach ($inputs as [$noun, $verb]) {
            $p = $program;
            $p[1] = $noun;
            $p[2] = $verb;

            $out = (new IntCode)->load($p)->run();

            if ($out === ($this->testing ? 3500 : 19690720)) {
                break;
            }
        }

        return 100 * $noun + $verb;
    }
}
