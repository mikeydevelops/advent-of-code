<?php

namespace Mike\AdventOfCode\Solutions\Year2024;

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
     * The cache.
     */
    protected array $cache = [];

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
     * @return integer|array  If $all is true, returns list of iteration values. Otherwise returns the end result.
     */
    protected function findSecret(int $seed, int $iterations = 1, bool $all = false): int|array
    {
        $secrets = [];
        $secret = $seed;

        for ($i = 0; $i < $iterations; $i++) {
            if (! isset($this->cache[$s = $secret])) {
                $secret = ($secret ^ ($secret * 64)) % 16777216;
                $secret = ($secret ^ ((int) floor($secret / 32))) % 16777216;
                $secret = ($secret ^ ($secret * 2048)) % 16777216;

                $this->cache[$s] = $secret;
            } else {
                $secret = $this->cache[$s];
            }

            if ($all) {
                $secrets[] = $secret;
            }
        }

        return $all ? $secrets : $secret;
    }

    /**
     * Find the secret given initial seed value.
     *
     * @param  integer  $seed  The initial secret value.
     * @param  integer  $iterations  The amount of iterations to generate the secret.
     * @return array{int,int}  The differences between each change of secret. The first element is the price, the second is the change from previous.
     */
    protected function getChanges(int $seed, int $iterations = 1): array
    {
        $changes = [];
        $prev = $seed % 10;
        $secrets = $this->findSecret($seed, $iterations, true);

        foreach ($secrets as $secret) {
            $one = $secret % 10;
            $changes[] = [$one, $this->cache[$k = "c$one.$prev"] ?? $this->cache[$k] = $one - $prev];
            $prev = $one;
        }

        return $changes;
    }

    /**
     * Run the first part of the challenge.
     */
    public function part1(): int
    {
        return array_sum(array_map(fn($s) => $this->findSecret($s, 2000), $this->getInput()));
    }

    /**
     * Run the second part of the challenge.
     */
    public function part2(): int
    {
        $buyers = $this->getInput(example: "1\n2\n3\n2024");

        $sequences = [];
        $values = [];

        foreach ($buyers as $buyer) {
            $changes = $this->getChanges($buyer, 2000);
            $total = count($changes) - 3;
            $seen = [];

            for ($i = 0; $i < $total; $i++) {
                $window = array_slice($changes, $i, 4);
                $sequence = array_column($window, 1);
                $id = implode(',', $sequence);

                // reading comprehension had me stumped here.
                // forgot the monkey buys the first sequence only.
                if (isset($seen[$id])) {
                    continue;
                }

                if (! isset($sequences[$id])) {
                    $sequences[$id] = $sequence;
                }

                $values[$id] = ($values[$id] ?? 0) + $window[3][0];
                $seen[$id] = true;
            }
        }

        return max($values);
    }
}
