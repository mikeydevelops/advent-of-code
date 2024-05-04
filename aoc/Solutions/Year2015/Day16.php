<?php

namespace Mike\AdventOfCode\Solutions\Year2015;

use Mike\AdventOfCode\Solutions\Solution;

/**
 * @method  int[][]  getInput()  Get the aunts.
 */
class Day16 extends Solution
{
    /**
     * The example input to be used to test the solution.
     */
    protected ?string $exampleInput = null;

    /**
     * The results of the simulation.
     */
    protected array $result = [];

    /**
     * Process the input from the challenge.
     *
     * @return int[][]
     */
    public function transformInput(string $input): array
    {
        $aunts = [];

        foreach (explode("\n", trim($input)) as $line) {
            [$name, $compounds] = explode(': ', $line, 2);

            $compounds = array_sliding(explode(' ', str_replace([':', ','], '', $compounds)), 2, 2);
            $compounds = array_combine(array_column($compounds, 0), array_column($compounds, 1));
            $compounds = array_map('intval', $compounds);

            ksort($compounds);

            $aunts[$name] = $compounds;
        }

        return $aunts;
    }

    /**
     * Hook before all parts are run.
     */
    protected function before(): void
    {
        $aunts = $this->getInput();

        $tickerTape = [
            'children' => 3,
            'cats' => 7,
            'samoyeds' => 2,
            'pomeranians' => 3,
            'akitas' => 0,
            'vizslas' => 0,
            'goldfish' => 5,
            'trees' => 3,
            'cars' => 2,
            'perfumes' => 1,
        ];

        ksort($tickerTape);

        $impostorAuntNo = 0;
        $realAuntNo = 0;

        foreach ($aunts as $aunt => $compounds) {
            $match = array_intersect_key($tickerTape, $compounds);

            if ($match == $compounds) {
                $impostorAuntNo = intval(substr($aunt, strpos($aunt, ' ')));
            }

            if ($this->check($match, $compounds)) {
                $realAuntNo = intval(substr($aunt, strpos($aunt, ' ')));
            }
        }

        $this->result = [$impostorAuntNo, $realAuntNo];
    }

    /**
     * Run the first part of the challenge.
     */
    public function part1(): int
    {
        return $this->result[0];
    }

    /**
     * Run the second part of the challenge.
     */
    public function part2(): int
    {
        return $this->result[1];
    }

    /**
     * Do a check for part two.
     *
     * @param  integer[]  $match
     * @param  integer[]  $compounds
     * @return boolean
     */
    public function check(array $match, array $compounds): bool
    {
        foreach ($match as $prop => $value) {
            if (in_array($prop, ['cats', 'trees'])) {
                if ($value > $compounds[$prop]) {
                    return false;
                }

                continue;
            }

            if (in_array($prop, ['pomeranians', 'goldfish'])) {
                if ($value < $compounds[$prop]) {
                    return false;
                }

                continue;
            }

            if ($compounds[$prop] != $value) {
                return false;
            }
        }

        return true;
    }
}
