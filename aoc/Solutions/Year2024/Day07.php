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
     * Calibrate the bridge.
     *
     * @param  array{int,array>}  $equations
     * @param  array<string,callable(int $value, int $number):int>
     */
    public function calibrateBridge(array $equations, array $operators): int
    {
        $result = 0;

        foreach ($equations as [$test, $numbers]) {
            $ops = array_fill(0, count($numbers) - 1, array_keys($operators));

            foreach (array_cartesian(...$ops) as $ops) {
                $val = $numbers[0];

                foreach (array_slice($numbers, 1) as $idx => $number) {
                    $val = $operators[$ops[$idx]]($val, $number);
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
     * Run the first part of the challenge.
     */
    public function part1(): int
    {
        $operators = [
            '+' => fn($v, $num) => $v + $num,
            '*' => fn($v, $num) => $v * $num,
        ];

        return $this->calibrateBridge($this->getInput(), $operators);
    }

    /**
     * Run the second part of the challenge.
     */
    public function part2(): int
    {
        $operators = [
            '+' => fn($v, $num) => $v + $num,
            '*' => fn($v, $num) => $v * $num,
            '||' => fn($v, $num) => intval(strval($v).strval($num)),
        ];

        return $this->calibrateBridge($this->getInput(), $operators);
    }
}
