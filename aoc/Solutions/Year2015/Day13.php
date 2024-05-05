<?php

namespace Mike\AdventOfCode\Solutions\Year2015;

use Mike\AdventOfCode\Solutions\Solution;

/**
 * @method  int[][]  getInput()  Get the attendees and their scores.
 */
class Day13 extends Solution
{
    /**
     * The example input to be used to test the solution.
     */
    protected ?string $exampleInput = <<<TXT
    Alice would gain 54 happiness units by sitting next to Bob.
    Alice would lose 79 happiness units by sitting next to Carol.
    Alice would lose 2 happiness units by sitting next to David.
    Bob would gain 83 happiness units by sitting next to Alice.
    Bob would lose 7 happiness units by sitting next to Carol.
    Bob would lose 63 happiness units by sitting next to David.
    Carol would lose 62 happiness units by sitting next to Alice.
    Carol would gain 60 happiness units by sitting next to Bob.
    Carol would gain 55 happiness units by sitting next to David.
    David would gain 46 happiness units by sitting next to Alice.
    David would lose 7 happiness units by sitting next to Bob.
    David would gain 41 happiness units by sitting next to Carol.
    TXT;

    /**
     * Process the input from the challenge.
     *
     * @return int[][]
     */
    public function transformInput(string $input): array
    {
        $attendees = [];

        // remove unused strings
        $input = str_replace(
            ['would ', 'happiness units by sitting next to ', '.', 'gain ', 'lose '],
            ['', '', '', '+', '-'],
            $input
        );

        foreach (split_lines($input) as $line) {
            [$attendee, $points, $guest] = explode(' ', $line);

            $attendees[$attendee][$guest] = intval($points);
        }

        return $attendees;
    }

    /**
     * Run the first part of the challenge.
     */
    public function part1(): int
    {
        return $this->getOptimalHappiness($this->getInput());
    }

    /**
     * Run the second part of the challenge.
     */
    public function part2(): int
    {
        $me = 'Me';

        // Lazy fix :/
        ini_set('memory_limit', '800M');

        $points = $this->getInput();

        $guests = array_keys($points);

        foreach ($guests as $guest) {
            $points[$guest][$me] = 0;
        }

        $points[$me] = array_combine($guests, array_fill(0, count($guests), 0));

        return $this->getOptimalHappiness($points);
    }

    /**
     * Get optimal happiness given array with guests and points.
     *
     * @param  array[]  $points
     * @return integer
     */
    public function getOptimalHappiness(array $points): int
    {
        $guests = array_keys($points);

        $totalGuests = count($guests);
        $combinations = iterator_to_array(array_permutations($guests));

        foreach ($combinations as $cIdx => $combination) {
            $arrangement = [];

            foreach ($combination as $gIdx => $guest) {
                $next = $combination[$gIdx + 1] ?? $combination[0];
                $prev = $combination[$gIdx - 1] ?? $combination[$totalGuests - 1];

                $arrangement[$guest] = 0 + $points[$guest][$prev] + $points[$guest][$next];
            }

            $combinations[$cIdx] = array_sum($arrangement);
        }

        return max($combinations);
    }
}
