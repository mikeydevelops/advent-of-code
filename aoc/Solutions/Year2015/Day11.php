<?php

namespace Mike\AdventOfCode\Solutions\Year2015;

use Mike\AdventOfCode\Solutions\Solution;

/**
 * @method  string  getInput()  Get the initial password.
 */
class Day11 extends Solution
{
    /**
     * The example input to be used to test the solution.
     */
    protected ?string $exampleInput = 'abcdefgh';

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
        return $this->findNextPassword($this->getInput());
    }

    /**
     * Run the second part of the challenge.
     */
    public function part2(): string
    {
        return $this->findNextPassword($this->part1Result);
    }

    /**
     * Find next password for given old password.
     */
    public function findNextPassword(string $oldPassword): string
    {
        $previousPassword = $oldPassword;

        $newPassword = null;

        $forbiddenLetters = ['i', 'o', 'l'];

        do {
            $possible = string_increment($previousPassword);

            // skip iterations if forbidden characters are found.
            if (($idx = strpos_any($forbiddenLetters, $possible)) !== false) {
                $possible[$idx] = string_increment($possible[$idx]);

                // Reset following characters to a because we skipped a letter
                if ($idx < 7) {
                    foreach (range($idx + 1, 7) as $idx) {
                        $possible[$idx] = 'a';
                    }
                }
            }

            $previousPassword = $possible;

            $letters = str_split($previousPassword);

            // Rule 1: Passwords must include one increasing straight of at least three letters
            if (! string_has_consecutive_characters($possible, 2)) {
                continue;
            }

            // Rule 3: Passwords must contain at least two different, non-overlapping pairs of letters
            if (count(array_find_repeating($letters)) < 2) {
                continue;
            }

            $newPassword = $possible;

        } while (is_null($newPassword));

        return $newPassword;
    }
}
