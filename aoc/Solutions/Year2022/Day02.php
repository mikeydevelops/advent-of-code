<?php

namespace Mike\AdventOfCode\Solutions\Year2022;

use Mike\AdventOfCode\Solutions\Solution;

class Day02 extends Solution
{
    /**
     * The example input to be used to test the solution.
     */
    protected ?string $exampleInput = <<<TXT
    A Y
    B X
    C Z
    TXT;

    /**
     * Process the input from the challenge.
     */
    public function transformInput(string $input): array
    {
        return array_map(fn($round) => explode(' ', $round), preg_split('/\r?\n/', $input));
    }

    /**
     * Run the first part of the challenge.
     */
    public function part1(): int
    {
        $scores = [];
        $order = [
            'r' => 's',
            'p' => 'r',
            's' => 'p',
        ];
        $moves = [
            'A' => 'r', 'B' => 'p', 'C' => 's',
            'X' => 'r', 'Y' => 'p', 'Z' => 's',
        ];

        foreach ($this->getInput() as $round) {
            $m1 = $moves[$round[0]];
            $m2 = $moves[$round[1]];
            $moveScore = array_search($round[1], array_keys($order)) + 1;

            if ($m1 === $m2) {
                $scores[] = 3 + $moveScore;
                continue;
            }

            if ($order[$m2] === $m1) {
                $scores[] = 6 + $moveScore;
                continue;
            }

            $scores[] = $moveScore;
        }

        return array_sum($scores);
    }

    /**
     * Run the second part of the challenge.
     */
    public function part2(): int
    {
        $scores = [];
        $order = [
            'r' => 's',
            'p' => 'r',
            's' => 'p',
        ];
        $moves = [
            'A' => 'r', 'B' => 'p', 'C' => 's',
            'X' => 'r', 'Y' => 'p', 'Z' => 's',
        ];

        foreach ($this->getInput() as $round) {
            $m1 = $moves[$round[0]];
            $m2 = $round[1] == 'Y' ? $m1 : ($round[1] == 'X' ? $order[$m1] : array_search($m1, $order));
            $moveScore = array_search($m2, array_keys($order)) + 1;

            if ($m1 === $m2) {
                $scores[] = 3 + $moveScore;
                continue;
            }

            if ($order[$m2] === $m1) {
                $scores[] = 6 + $moveScore;
                continue;
            }

            $scores[] = $moveScore;
        }

        return array_sum($scores);
    }
}
