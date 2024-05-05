<?php

namespace Mike\AdventOfCode\Solutions\Year2016;

use Mike\AdventOfCode\Solutions\Solution;

/**
 * @method  string  getInput()  Get the door id.
 */
class Day05 extends Solution
{
    /**
     * The example input to be used to test the solution.
     */
    protected ?string $exampleInput = 'abc';

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
    public function part1(): string
    {
        $doorId = $this->getInput();

        $password = '';

        $i = 0;
        while (strlen($password) < 8) {
            $candidate = md5($doorId.$i);
            $i++;

            if (substr($candidate, 0, 5) !== '00000') {
                continue;
            }

            $password .= $candidate[5];
        }

        return $password;
    }

    /**
     * Run the second part of the challenge.
     */
    public function part2(): string
    {
        $doorId = $this->getInput();

        $password = [];

        $i = 0;
        while (count($password) < 8) {
            $candidate = md5($doorId.$i);
            $i++;

            if (substr($candidate, 0, 5) !== '00000') {
                continue;
            }

            $position = is_numeric($candidate[5]) ? intval($candidate[5]) : null;

            if ($position === null || $position > 7 || isset($password[$position])) {
                continue;
            }

            $password[$position] = $candidate[6];
        }

        ksort($password);

        return implode('', $password);
    }
}
