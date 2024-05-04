<?php

namespace Mike\AdventOfCode\Solutions\Year2015;

use Mike\AdventOfCode\Solutions\Solution;

/**
 * @method  string  getInput()
 */
class Day10 extends Solution
{
    /**
     * The example input to be used to test the solution.
     */
    protected ?string $exampleInput = '111221';

    /**
     * Process the input from the challenge.
     */
    public function transformInput(string $input): mixed
    {
        return trim($input);
    }

    /**
     * Run the first part of the challenge.
     */
    public function part1(): int
    {
        return strlen($this->repeatLookAndSay($this->getInput(), 40));
    }

    /**
     * Run the second part of the challenge.
     */
    public function part2(): int
    {
        // Second pass needs 2 GB of memory :O
        ini_set('memory_limit', '2G');

        return strlen($this->repeatLookAndSay($this->getInput(), 50));
    }

    /**
     * Run the look-and-say algorithm on given string.
     */
    public function lookAndSay(string $input): string
    {
        $output = '';
        $tokens = [];
        $lastToken = null;

        foreach (iterate_string($input) as $token) {
            if (!$lastToken || $lastToken[0] != $token) {
                $tokens[] = [$token, 0];

                $lastToken = &$tokens[count($tokens) - 1];
            }

            $lastToken[1] ++;
        }

        foreach ($tokens as $token) {
            $output .= $token[1] . $token[0];
        }

        return $output;
    }

    /**
     * Call the lookAndSay function repeatedly.
     */
    public function repeatLookAndSay(string $input, int $repeat): string
    {
        $output = $input;

        foreach (range(1, $repeat) as $i) {
            $output = $this->lookAndSay($output);
        }

        return $output;
    }
}
