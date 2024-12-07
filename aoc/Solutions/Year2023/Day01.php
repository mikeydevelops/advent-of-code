<?php

namespace Mike\AdventOfCode\Solutions\Year2023;

use Mike\AdventOfCode\Solutions\Solution;

class Day01 extends Solution
{
    /**
     * The example input to be used to test the solution.
     */
    protected ?string $exampleInput = <<<TXT
    1abc2
    pqr3stu8vwx
    a1b2c3d4e5f
    treb7uchet
    TXT;

    /**
     * Process the input from the challenge.
     */
    public function transformInput(string $input): array
    {
        return split_lines($input);
    }

    /**
     * Run the first part of the challenge.
     */
    public function part1(): int
    {
        $lines = array_map(fn ($i) => preg_replace('/\D/','', $i), $this->getInput());
        $lines = array_map(fn ($l) => intval($l[0].substr($l, -1)), $lines);

        return array_sum($lines);
    }

    /**
     * Run the second part of the challenge.
     *
     * This part got me good. Needed to allow overlapping numbers.
     */
    public function part2(): int
    {
        // replace with this map because some numbers overlap.
        $numbers = [
            'one' => 'o1e',
            'two' => 't2o',
            'three' => 't3e',
            'four' => 'f4r',
            'five' => 'f5e',
            'six' => 's6x',
            'seven' => 's7n',
            'eight' => 'e8t',
            'nine' => 'n9e',
        ];

        $lines = $this->transformInput(str_replace(
            array_keys($numbers),
            array_values($numbers),
            $this->getRawInput(example: <<<TXT
            two1nine
            eightwothree
            abcone2threexyz
            xtwone3four
            4nineeightseven2
            zoneight234
            7pqrstsixteen
            TXT)
        ));

        $lines = array_map(fn ($i) => preg_replace('/\D/','', $i), $lines);
        $lines = array_map(fn ($l) => intval($l[0].substr($l, -1)), $lines);

        return array_sum($lines);
    }
}
