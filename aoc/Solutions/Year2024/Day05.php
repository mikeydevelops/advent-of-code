<?php

namespace Mike\AdventOfCode\Solutions\Year2024;

use Mike\AdventOfCode\Solutions\Solution;

class Day05 extends Solution
{
    /**
     * The example input to be used to test the solution.
     */
    protected ?string $exampleInput = <<<TXT
    47|53
    97|13
    97|61
    97|47
    75|29
    61|13
    75|53
    29|13
    97|29
    53|29
    61|53
    97|53
    61|29
    47|13
    75|47
    97|75
    47|61
    75|61
    47|29
    75|13
    53|13

    75,47,61,53,29
    97,61,53,29,13
    75,29,13
    75,97,47,61,53
    61,13,29
    97,13,75,29,47
    TXT;

    /**
     * Process the input from the challenge.
     *
     * @return array{array<array{integer,integer}>,array<integer[]>}
     */
    public function transformInput(string $input): array
    {
        [$rules, $updates] = preg_split('/[\r?\n]{2}/', $input);

        $parse = fn($data, $separator) => array_map('intval', explode($separator, $data));

        $rules = array_map(fn($rule) => $parse($rule, '|'), split_lines($rules));

        $updates = array_map(fn($update) => $parse($update, ','), split_lines($updates));

        return [$rules, $updates];
    }

    /**
     * Determine if a given update is correctly sorted by the specified rules.
     *
     * @param  integer[]  $update
     * @param  array<array{integer,integer}>  $rules
     */
    public function updateSorted(array $update, array $rules): bool
    {
        // filter out the rules that wont be needed for this update.
        $rules = array_filter($rules, fn($rule) => in_array($rule[0], $update));

        foreach ($rules as [$first, $second]) {
            $firstPos = array_search($first, $update, true);
            $secondPos = array_search($second, $update, true);

            if ($firstPos === false || $secondPos === false) {
                continue;
            }

            if ($firstPos >= $secondPos) {
                return false;
            }
        }

        return true;
    }

    /**
     * Sort given update with specified rules.
     *
     * @param  integer[]  $update
     * @param  array<array{integer,integer}>  $rules
     * @return integer[]
     */
    public function sortUpdate(array $update, array $rules): array
    {
        // filter out the rules that wont be needed for this update.
        $rules = array_filter($rules, fn($rule) => in_array($rule[0], $update));

        // group by for easier access.
        $grouped = [];
        foreach ($rules as $rule) {
            $grouped[$rule[0]][$rule[1]] = $rule;
        }

        $sorted = [];

        foreach ($update as $num) {
            $minPos = count($sorted);

            if (! isset($grouped[$num])) {
                $sorted[] = $num;

                continue;
            }

            foreach ($grouped[$num] as [$num, $second]) {
                $pos = array_search($second, $sorted, true);

                if ($pos === false) continue;

                if ($pos < $minPos) $minPos = $pos;
            }

            array_splice($sorted, $minPos, 0, $num);
        }

        return $sorted;
    }

    /**
     * Run the first part of the challenge.
     */
    public function part1(): int
    {
        [$rules, $updates] = $this->getInput();

        // select only correctly sorted updates
        $updates = array_filter($updates, fn($update) => $this->updateSorted($update, $rules));

        // select the middle value
        $updates = array_map(fn($update) => $update[floor(count($update) / 2)], $updates);

        return array_sum($updates);
    }

    /**
     * Run the second part of the challenge.
     */
    public function part2()
    {
        [$rules, $updates] = $this->getInput();

        // select only updates not correctly sorted
        $updates = array_filter($updates, fn($update) => ! $this->updateSorted($update, $rules));

        // sort those updates
        $updates = array_map(fn($update) => $this->sortUpdate($update, $rules), $updates);

        // get the middle value
        $updates = array_map(fn($update) => $update[floor(count($update) / 2)], $updates);

        return array_sum($updates);
    }
}
