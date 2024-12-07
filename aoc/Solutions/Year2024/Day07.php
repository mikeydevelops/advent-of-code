<?php

namespace Mike\AdventOfCode\Solutions\Year2024;

use Mike\AdventOfCode\Solutions\Solution;

class Day07 extends Solution
{
    /**
     * The example input to be used to test the solution.
     */
    protected ?string $exampleInput = <<<TXT
    190: 10 19
    3267: 81 40 27
    83: 17 5
    156: 15 6
    7290: 6 8 6 15
    161011: 16 10 13
    192: 17 8 14
    21037: 9 7 18 13
    292: 11 6 16 20
    TXT;

    /**
     * Process the input from the challenge.
     */
    public function transformInput(string $input): array
    {
        $equations = split_lines($input);


        return array_map(function ($equation) {
            [$test, $numbers] = explode(': ', $equation, 2);

            return [intval($test), array_map('intval', explode(' ', $numbers))];
        }, $equations);
    }

    /**
     * Calibrate the bridge using cartesian product for each operator.
     *
     * ~ 10sec for part 2.
     *
     * @param  array{int,array>}  $equations
     * @param  array<string,callable(int $value, int $number):int>
     */
    public function calibrateBridgeCartesian(array $equations, array $operators): int
    {
        $result = 0;

        foreach ($equations as [$test, $numbers]) {
            // repeat operators as many times as there are numbers excluding the first.
            $ops = array_fill(0, count($numbers) - 1, $operators);

            // iterate over each cartesian product of the possible operators.
            foreach (array_cartesian(...$ops) as $ops) {
                $val = $numbers[0];

                foreach (array_slice($numbers, 1) as $idx => $number) {
                    $val = match($ops[$idx]) {
                        '+' => $val + $number,
                        '*' => $val * $number,
                        '||' => (int) "$val$number",
                    };
                }

                if ($val === $test) {
                    $result += $val;

                    break;
                }
            }
        }

        return $result;
    }

    /**
     * Calibrate the bridge using recursion.
     *
     * ~ 1sec for part 2
     *
     * @param  array{int,array>}  $equations
     * @param  array<string,callable(int $value, int $number):int>
     */
    public function calibrateBridgeRecurse(array $equations, array $operators): int
    {
        $result = 0;

        foreach ($equations as [$test, $numbers]) {
            $val = array_shift($numbers);

            if ($this->calibrateBridgeRecursively($test, $numbers, $operators, $val)) {
                $result += $test;
            }
        }

        return $result;
    }

    /**
     * Calibrate the bridge using recursion.
     * Adding because everyone in thesubreddit was boasting about recursion.
     *
     * @param  integer  $test  The target value.
     * @param  integer[]  $numbers  The numbers to test
     * @param  string[]  $operators The operators to use.
     * @param  integer  $val  Optional used for recursion.
     * @return boolean
     */
    public function calibrateBridgeRecursively(int $test, array $numbers, array $operators, int $val = 0): bool
    {
        if (empty($numbers)) {
            return $test === $val;
        }

        $number = $numbers[0];

        foreach ($operators as $op) {
            $newVal = match($op) {
                '+' => $val + $number,
                '*' => $val * $number,
                '||' => (int) "$val$number",
            };

            if ($newVal > $test) {
                continue;
            }

            if ($this->calibrateBridgeRecursively($test, array_slice($numbers, 1), $operators, $newVal)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Run the first part of the challenge.
     */
    public function part1(): int
    {
        return $this->calibrateBridgeRecurse($this->getInput(), ['+', '*']);
    }

    /**
     * Run the second part of the challenge.
     */
    public function part2(): int
    {
        return $this->calibrateBridgeRecurse($this->getInput(), ['+', '*', '||']);
    }
}
