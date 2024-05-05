<?php

namespace Mike\AdventOfCode\Solutions\Year2016;

use Mike\AdventOfCode\Solutions\Solution;
use Mike\AdventOfCode\Solutions\Year2016\Day07\IPv7;

/**
 * @method  \Mike\AdventOfCode\Solutions\Year2016\Day07\IPv7[]  getInput()  Get the inputs.
 */
class Day07 extends Solution
{
    /**
     * The example input to be used to test the solution.
     */
    protected ?string $exampleInput = <<<TXT
    abba[mnop]qrst
    abcd[bddb]xyyx
    aaaa[qwer]tyui
    ioxxoj[asdfgh]zxcvbn
    aba[bab]xyz
    xyx[xyx]xyx
    aaa[kek]eke
    zazbz[bzb]cdb
    TXT;

    /**
     * Process the input from the challenge.
     *
     * @return \Mike\AdventOfCode\Solutions\Year2016\Day07\IPv7[]
     */
    public function transformInput(string $input): array
    {
        return array_map(fn(string $ip) => IPv7::fromString($ip), split_lines($input));
    }

    /**
     * Run the first part of the challenge.
     */
    public function part1(): int
    {
        return count(array_filter($this->getInput(), fn (IPv7 $ip) => $ip->supportsTls()));
    }

    /**
     * Run the second part of the challenge.
     */
    public function part2(): int
    {
        return count(array_filter($this->getInput(), fn (IPv7 $ip) => $ip->supportsSsl()));
    }
}
