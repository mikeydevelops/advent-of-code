<?php

namespace Mike\AdventOfCode\Solutions\Year2024;

use Mike\AdventOfCode\Solutions\Solution;

class Day01 extends Solution
{
    /**
     * The example input to be used to test the solution.
     */
    protected ?string $exampleInput = <<<TXT
    3   4
    4   3
    2   5
    1   3
    3   9
    3   3
    TXT;

    /**
     * Process the input from the challenge.
     */
    public function transformInput(string $input): array
    {
        $lists = array_filter(preg_split('/\r?\n/', $input));

        $lists = array_map(function ($row) {
            $row = preg_split('/\s+/', trim($row));

            return array_map('intval', $row);
        }, $lists);

        return [array_column($lists, 0), array_column($lists, 1)];;
    }

    /**
     * Run the first part of the challenge.
     */
    public function part1(): int
    {
        $lists = $this->getInput();

        sort($lists[0]); sort($lists[1]);

        $distance = 0;

        for ($i = 0; $i < count($lists[0]); $i++) {
            $distance += abs($lists[0][$i] - $lists[1][$i]);
        }

        return $distance;
    }

    /**
     * Run the second part of the challenge.
     */
    public function part2(): int
    {
        $lists = $this->getInput();

        $similarity = 0;

        $vals = array_count_values($lists[1]);

        for ($i = 0; $i < count($lists[0]); $i++) {
            $id = $lists[0][$i];
            $similarity += $id * ($vals[$id] ?? 0);
        }

        return $similarity;
    }
}
