<?php

namespace Mike\AdventOfCode\Solutions\Year2024;

use Generator;
use Mike\AdventOfCode\Solutions\Solution;

class Day22 extends Solution
{
    /**
     * The example input to be used to test the solution.
     */
    protected ?string $exampleInput = <<<TXT
    1
    10
    100
    2024
    TXT;

    /**
     * Process the input from the challenge.
     */
    public function transformInput(string $input): mixed
    {
        return split_lines($input, map: 'intval');
    }

    /**
     * Find the secret given initial seed value.
     *
     * @param  integer  $seed  The initial secret value.
     * @param  integer  $iterations  The amount of iterations to generate the secret.
     * @param  boolean  $all  Wether to return an array of individual iteration values.
     * @return integer|\Generator<integer>  If $all is true, returns list of iteration values. Otherwise returns the end result.
     */
    protected function findSecret(int $seed, int $iterations = 1, bool $all = false): int|Generator
    {
        $secret = $seed;

        for ($i = 0; $i < $iterations; $i++) {
            $secret = ($secret ^ ($secret * 64)) % 16777216;
            $secret = ($secret ^ ((int) floor($secret / 32))) % 16777216;
            $secret = ($secret ^ ($secret * 2048)) % 16777216;

            if ($all) {
                yield $secret;
            }
        }

        if ($all) {
            return;
        }

        yield $secret;
    }

    /**
     * Find the secret given initial seed value.
     *
     * @param  integer  $seed  The initial secret value.
     * @param  integer  $iterations  The amount of iterations to generate the secret.
     * @return \Generator<array{int,int}>  The differences between each change of secret. The first element is the price, the second is the change from previous.
     */
    protected function getChanges(int $seed, int $iterations = 1): Generator
    {
        $changes = [];
        $prev = $seed % 10;

        foreach ($this->findSecret($seed, $iterations, true) as $secret) {
            $one = $secret % 10;
            yield [$one, $one - $prev];
            $prev = $one;
        }

        return $changes;
    }

    /**
     * Run the first part of the challenge.
     */
    public function part1(): int
    {
        return array_sum(array_map(fn($s) => $this->findSecret($s, 2000)->current(), $this->getInput()));
    }

    /**
     * Run the second part of the challenge.
     */
    public function part2(): int
    {
        $buyers = $this->getInput(example: "1\n2\n3\n2024");

        $sequences = [];
        $values = [];
        $max = 0;

        foreach ($buyers as $buyer) {
            $seen = [];
            $changes = $this->getChanges($buyer, 2000);
            $window = array_map(function () use ($changes) {
                $result = $changes->current();
                $changes->next();
                return $result;
            }, array_fill(0, 4, 0));

            while ($changes->valid()) {
                $sequence = array_column($window, 1);
                $id = implode(',', $sequence);

                // reading comprehension had me stumped here.
                // forgot the monkey buys the first sequence only.
                if (! isset($seen[$id])) {
                    $seen[$id] = true;

                    if (! isset($sequences[$id])) {
                        $sequences[$id] = $sequence;
                        $values[$id] = 0;
                    }

                    $values[$id] += $window[3][0];

                    if ($max < $values[$id]) {
                        $max = $values[$id];
                    }
                }

                $window[] = $changes->current();
                $changes->next();
                array_shift($window);
            }
        }

        return $max;
    }
}
