<?php

namespace Mike\AdventOfCode\Solutions\Year2015;

use Mike\AdventOfCode\Solutions\Solution;

/**
 * @method  string  getInput()  Get the secret key.
 */
class Day04 extends Solution
{
    /**
     * The example input to be used to test the solution.
     */
    protected ?string $exampleInput = 'abcdef';

    /**
     * Process the input from the challenge.
     */
    public function transformInput(string $input): string
    {
        return trim($input);
    }

    /**
     * Run the first part of the challenge.
     */
    public function part1(): int
    {
        return $this->findHashIndex('00000', $this->getInput());
    }

    /**
     * Run the second part of the challenge.
     */
    public function part2(): int
    {
        return $this->findHashIndex('000000', $this->getInput(), $this->part1Result);
    }

    /**
     * Find the MD5 hash starting with given prefix.
     */
    public function findHashIndex(string $prefix, string $key, int $num = 0): int
    {
        // make the given number zero based.
        $num --;
        $prefixLen = strlen($prefix);

        do {
            $num ++;

            $hash = md5($key . $num);
        } while(substr($hash, 0, $prefixLen) !== $prefix);

        return $num;
    }
}
