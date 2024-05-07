<?php

namespace Mike\AdventOfCode\Solutions\Year2016;

use Mike\AdventOfCode\Solutions\Solution;

/**
 * @method  string  getInput()  Get the prearranged salt.
 */
class Day14 extends Solution
{
    /**
     * The example input to be used to test the solution.
     */
    protected ?string $exampleInput =  'abc';

    /**
     * Attempt to speedup script.
     *
     * @var string[]
     */
    protected array $hashCache = [];

    /**
     * Process the input from the challenge.
     */
    public function transformInput(string $input): string
    {
        return trim($input);
    }

    /**
     * Hook before each of the parts executes.
     */
    public function beforeEach(string $part): void
    {
        $this->hashCache = [];
    }

    /**
     * Run the first part of the challenge.
     */
    public function part1(): int
    {
        $salt = $this->getInput();

        $idx = -1;
        $c = 0;

        do {
            $idx = $this->findHashIndex($salt, $idx+1);
        } while (++$c < 64);

        return $idx;
    }

    /**
     * Run the second part of the challenge.
     */
    public function part2(): int
    {
        $salt = $this->getInput();

        $idx = -1;
        $c = 0;

        do {
            $idx = $this->findHashIndex($salt, $idx+1, stretch: true);
        } while (++$c < 64);

        return $idx;
    }

    /**
     * Find the MD5 hash of the salt and index. Stretch the hashing if needed.
     */
    public function hash(string $salt, int $num = 0, bool $stretch = false): string
    {
        // php range function includes the end too
        $times = $stretch ? 2017 : 1;
        $key = $salt.$num;

        if (isset($this->hashCache[$key])) {
            return $this->hashCache[$key];
        }

        $hash = $salt.$num;

        for ($i = 0; $i < $times; $i++) {
            $hash = md5($hash);
        }

        return $this->hashCache[$key] = $hash;
    }

    /**
     * Find the index of the hash that has a repeating
     * triplet and a repeating quintuple of the same character
     * in the next 1000 hashes after the triplet.
     */
    public function findHashIndex(string $salt, int $num = 0, bool $stretch = false): int
    {
        $queue = [];

        foreach (range($num, $num+999) as $idx) {
            $queue[] = [$idx, $this->hash($salt, $idx, $stretch)];
            $num ++;
        }

        while (true) {
            $queue[] = [$num, $this->hash($salt, $num, $stretch)];
            $num++;

            [$n, $hash] = array_shift($queue);

            $consecutive = array_count_consecutive(str_split($hash));
            $consecutive = array_filter($consecutive, fn (array $result) => $result[1] >= 3);

            // get the first triplet
            $triplet = current($consecutive);

            if (! $triplet) {
                continue;
            }

            foreach ($queue as [$i, $h]) {
                if (strpos($h, str_repeat($triplet[0], 5)) !== false) {
                    return $n;
                }
            }
        }

        return -1;
    }
}
