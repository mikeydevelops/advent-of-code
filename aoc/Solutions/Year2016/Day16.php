<?php

namespace Mike\AdventOfCode\Solutions\Year2016;

use Mike\AdventOfCode\Solutions\Solution;

/**
 * @method  string  getInput()  Get the data .
 */
class Day16 extends Solution
{
    /**
     * The example input to be used to test the solution.
     */
    protected ?string $exampleInput = '10000';

    /**
     * Process the input from the challenge.
     */
    public function transformInput(string $input): string
    {
        return trim($input);
    }

    /**
     * Run the first part of the challenge.
     */
    public function part1(): string
    {
        $data = $this->getInput();

        $length = $this->testing ? 20 : 272;

        $data = strlen($data) < $length
            ? $this->dragonDeese($data, $length)
            : $data;

        $data = substr($data, 0, $length);

        return $this->checksum($data);
    }

    /**
     * Run the second part of the challenge.
     */
    public function part2()
    {
        $length = 35651584;

        // Lazy fix, it would be better if I used generators
        // TODO: Use generators instead
        ini_set('memory_limit', '2G');

        $data = $this->dragonDeese($this->getInput(), $length);

        $data = substr($data, 0, $length);

        return $this->checksum($data);
    }

    /**
     * Elongate given input using the modified dragon curve algorithm.
     *
     * Sorry for the function name :P (not really sorry, it's funny)
     */
    public function dragonDeese(string $nuts, int $minLength): string
    {
        while (strlen($nuts) < $minLength) {
            $nuts .= '0'.implode('', array_map(fn ($c) => $c ? '0' : '1', array_reverse(str_split($nuts))));
        }

        return $nuts;
    }

    /**
     * Generate checksum on given data.
     */
    public function checksum(string $data): string
    {
        $sum = '';

        do {
            $sum = '';

            foreach (str_split($data, 2) as $pair) {
                $sum .= $pair === '00' || $pair === '11' ? '1' : '0';
            }

            // reset for the next round
            $data = $sum;
        } while (strlen($sum) % 2 == 0);

        return $sum;
    }
}
