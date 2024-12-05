<?php

namespace Mike\AdventOfCode\Solutions\Year2018;

use Mike\AdventOfCode\Solutions\Solution;

class Day02 extends Solution
{
    /**
     * The example input to be used to test the solution.
     */
    protected ?string $exampleInput = <<<TXT
    abcdef
    bababc
    abbcde
    abcccd
    aabcdd
    abcdee
    ababab
    TXT;

    /**
     * Process the input from the challenge.
     */
    public function transformInput(string $input): mixed
    {
        return preg_split('/\r?\n/', $input);
    }

    /**
     * Run the first part of the challenge.
     */
    public function part1(): int
    {
        $boxes = array_map(fn($id) => array_count_values(str_split($id)), $this->getInput());
        $boxes = array_map(fn($counts) => array_filter($counts, fn($c) => $c === 2 || $c === 3), $boxes);

        $twoCount = $threeCount = 0;

        foreach ($boxes as $box) {
            if (in_array(2, $box))
                $twoCount ++;

            if (in_array(3, $box))
                $threeCount ++;
        }

        return $twoCount * $threeCount;
    }

    /**
     * Run the second part of the challenge.
     */
    public function part2(): string
    {
        $boxes = $this->getInput(example: <<<TXT
        abcde
        fghij
        klmno
        pqrst
        fguij
        axcye
        wvxyz
        TXT);

        foreach (array_combinations($boxes, 2) as $comb) {
            if (levenshtein($comb[0], $comb[1]) === 1) {
                return implode('', array_intersect(str_split($comb[0]), str_split($comb[1])));
            }
        }

        $this->io->error('Unable to find similar box ids with only one character difference. :(');

        return '';
    }
}
