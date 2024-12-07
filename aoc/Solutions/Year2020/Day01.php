<?php

namespace Mike\AdventOfCode\Solutions\Year2020;

use Mike\AdventOfCode\Solutions\Solution;
use PDO;

class Day01 extends Solution
{
    /**
     * The example input to be used to test the solution.
     */
    protected ?string $exampleInput = <<<TXT
    1721
    979
    366
    299
    675
    1456
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
        foreach ($expenses = $this->getInput() as $expense) {
            if (in_array($second = 2020 - $expense, $expenses)) {
                break;
            }
        }

        $this->io->info("The two expenses that add up to 2020 are <white>$expense</> and <white>$second</>.", 'v');

        return $expense * $second;
    }

    /**
     * Run the second part of the challenge.
     */
    public function part2(): int
    {
        foreach (combinations($this->getInput(), 3) as $p) {
            if (array_sum($p) == 2020) {
                break;
            }
        }

        $this->io->info("The three expenses that add up to 2020 are <white>$p[0]</>, <white>$p[1]</> and <white>$p[2]</>.", 'v');

        return array_product($p);
    }
}
