<?php

namespace Mike\AdventOfCode\Solutions\Year2024;

use Mike\AdventOfCode\Solutions\Solution;

class Day11 extends Solution
{
    /**
     * The example input to be used to test the solution.
     */
    protected ?string $exampleInput = "125 17";

    /**
     * The stone cache.
     *
     * @var array
     */
    protected $cache = [];

    /**
     * Get all the stones.
     *
     * @return \Generator<integer>
     */
    public function stones(): \Generator
    {
        $stream = $this->streamInput();
        $stone = '';

        while (! feof($stream)) {
            $s = fread($stream, 1);

            if ($s === ' ') {
                yield intval($stone);
                $stone = '';

                continue;
            }

            $stone .= $s;
        }

        // the last stone.
        yield intval($stone);
    }

    /**
     * Change the given stone.
     *
     * @param  integer  $stone
     * @return integer[]
     */
    public function engrave(int $stone): array
    {
        if ($stone === 0) {
            return [1];
        }

        $s = strval($stone);
        $len = strlen($s);

        if ($len % 2 === 0) {
            $mid = $len / 2;

            return [
                intval(substr($s, 0, $mid)),
                intval(substr($s, $mid)),
            ];
        }

        return [$stone * 2024];
    }

    /**
     * Change the given stones.
     *
     * @param  iterable  $stones
     * @return integer  The total amount of stones.
     */
    public function blink(int $stone, int $amount): int
    {
        if ($amount === 0) {
            return 1;
        }

        $key = "$stone:$amount";

        if (! isset($this->cache[$key])) {
            $sum = 0;

            foreach ($this->engrave($stone) as $r) {
                $sum += $this->blink($r, $amount - 1);
            }

            $this->cache[$key] = $sum;
        }

        return $this->cache[$key];
    }

    /**
     * Run the first part of the challenge.
     */
    public function part1(): int
    {
        $sum = 0;

        foreach ($this->stones() as $stone) {
            $sum += $this->blink($stone, 25);
        }

        return $sum;
    }

    /**
     * Run the second part of the challenge.
     */
    public function part2(): int
    {
        $sum = 0;

        foreach ($this->stones() as $stone) {
            $sum += $this->blink($stone, 75);
        }

        return $sum;
    }
}
