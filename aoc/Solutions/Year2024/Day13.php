<?php

namespace Mike\AdventOfCode\Solutions\Year2024;

use Generator;
use Mike\AdventOfCode\Solutions\Solution;

class Day13 extends Solution
{
    /**
     * The example input to be used to test the solution.
     */
    protected ?string $exampleInput = <<<TXT
    Button A: X+94, Y+34
    Button B: X+22, Y+67
    Prize: X=8400, Y=5400

    Button A: X+26, Y+66
    Button B: X+67, Y+21
    Prize: X=12748, Y=12176

    Button A: X+17, Y+86
    Button B: X+84, Y+37
    Prize: X=7870, Y=6450

    Button A: X+69, Y+23
    Button B: X+27, Y+71
    Prize: X=18641, Y=10279
    TXT;

    /**
     * Generate a machine from the input.
     *
     * @return \Generator<array{array{int,int},array{int,int},array{int,int}}>
     */
    public function machines(): Generator
    {
        $stream = $this->streamInput();

        while (! feof($stream)) {
            $a = $this->parseLine(fgets($stream));
            $b = $this->parseLine(fgets($stream));
            $prize = $this->parseLine(fgets($stream));

            yield [$a, $b, $prize];

            fgets($stream); // read empty line.
        }
    }

    /**
     * Parse a button or a prize line from an input line.
     *
     * @param  string  $line
     * @return array{int,int}
     */
    protected function parseLine(string $line): array
    {
        $line = substr(trim($line), strpos($line, ':') + 2);
        $line = array_map(fn($p) => intval(substr($p, 2)), explode(', ', $line));

        return $line;
    }

    /**
     * Solve the least amount of button presses to reach the prize.
     *
     * @param  array{array{int,int},array{int,int},array{int,int}}  $machine
     * @return integer
     */
    protected function findTokens(array $machine): int
    {
        [$a, $b, $prize] = $machine;

        $x = $b[0] * $prize[1] - $b[1] * $prize[0];
        $x /= $a[1] * $b[0] - $a[0] * $b[1];
        $y = ($prize[0] - $a[0] * $x) / $b[0];

        $xErr = abs($x - round($x));
        $yErr = abs($y - round($y));
        $t = 0.0001;

        if ($xErr < $t && $yErr < $t) {
            return $x * 3 + $y;
        }

        return 0;
    }

    /**
     * Run the first part of the challenge.
     */
    public function part1(): int
    {
        $sum = 0;

        foreach ($this->machines() as $machine) {
            $sum += $this->findTokens($machine);
        }

        return $sum;
    }

    /**
     * Run the second part of the challenge.
     */
    public function part2(): int
    {
        $sum = 0;
        $error = 10000000000000;

        foreach ($this->machines() as $machine) {
            [$x, $y] = $machine[2];
            $machine[2] = [$x + $error, $y + $error];

            $sum += $this->findTokens($machine);
        }

        return $sum;
    }
}
