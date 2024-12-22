<?php

namespace Mike\AdventOfCode\Solutions\Year2024;

use Mike\AdventOfCode\Solutions\Solution;

class Day22 extends Solution
{
    /**
     * The example input to be used to test the solution.
     */
    protected ?string $exampleInput = "1\n10\n100\n2024";

    /**
     * Run the first part of the challenge.
     */
    public function part1(): int
    {
        $sum = 0;

        foreach ($this->streamLines() as $secret) {
            for ($i = 0; $i < 2000; $i++) {
                $secret ^= ($secret << 6) & 0xFFFFFF; // 16777215
                $secret ^= $secret >> 5;
                $secret ^= ($secret << 11) & 0xFFFFFF;
            }

            $sum += $secret;
        }

        return $sum;
    }

    /**
     * Run the second part of the challenge.
     */
    public function part2(): int
    {
        $buyers = $this->streamLines(example: "1\n2\n3\n2024", map: 'intval');

        $values = [];
        $max = 0;

        foreach ($buyers as $buyer) {
            $seen = [];
            $prev = $buyer % 10;
            $a = $b = $c = $d = 0;

            for ($i = 0; $i < 2000; $i++) {
                $buyer ^= ($buyer << 6) & 0xFFFFFF; // 16777215
                $buyer ^= $buyer >> 5;
                $buyer ^= ($buyer << 11) & 0xFFFFFF;

                $v = $buyer % 10;
                $a = $b; $b = $c; $c = $d; $d = $v - $prev;
                $prev = $v;

                if ($i < 3) {
                    continue;
                }

                //  19^3 * a + 19^2 * b + 19^1 * c + d
                $id = 6859 * $a + 361 * $b + 19 * $c + $d;

                // reading comprehension had me stumped here.
                // forgot the monkey buys the first sequence only.
                if (! isset($seen[$id])) {
                    $seen[$id] = 1;

                    if (! isset($values[$id])) {
                        $values[$id] = 0;
                    }

                    $values[$id] += $v;

                    if ($max < $values[$id]) {
                        $max = $values[$id];
                    }
                }
            }
        }

        return $max;
    }
}
