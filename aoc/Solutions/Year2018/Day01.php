<?php

namespace Mike\AdventOfCode\Solutions\Year2018;

use Mike\AdventOfCode\Solutions\Solution;

class Day01 extends Solution
{
    /**
     * The example input to be used to test the solution.
     */
    protected ?string $exampleInput = <<<TXT
    +1
    +1
    -1
    +1
    TXT;

    /**
     * Process the input from the challenge.
     */
    public function transformInput(string $input): mixed
    {
        return array_map('intval', split_lines($input));
    }

    /**
     * Run the first part of the challenge.
     */
    public function part1(): int
    {
        return array_sum($this->getInput());
    }

    /**
     * Run the second part of the challenge.
     */
    public function part2(): int
    {
        $sums = [];
        $i = 0;
        $changes = $this->getInput();
        $changesCount = count($changes);
        $repeats = 0;

        while (true) {
            $next = $changes[$i];
            $last = array_pop($sums);

            $sum = $last + $next;

            if (in_array($sum, $sums)) {
                $sums[] = $sum;
                break;
            }

            $sums[] = $last;
            $sums[] = $sum;
            $i++;

            if ($i >=  $changesCount) {
                $i = 0;
                $repeats ++;
            }
        }

        $this->io->info("The list had to repeat <white>$repeats</> times to find the first frequency that is reached twice.", 'v');

        return array_pop($sums);
    }
}
