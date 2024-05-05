<?php

namespace Mike\AdventOfCode\Solutions\Year2016;

use Mike\AdventOfCode\Solutions\Solution;

/**
 * @method  string[][]  getInput()  Get the components for each floor.
 */
class Day11 extends Solution
{
    /**
     * The example input to be used to test the solution.
     */
    protected ?string $exampleInput = <<<TXT
    The first floor contains a hydrogen-compatible microchip and a lithium-compatible microchip.
    The second floor contains a hydrogen generator.
    The third floor contains a lithium generator.
    The fourth floor contains nothing relevant.
    TXT;

    /**
     * Process the input from the challenge.
     *
     * @return string[][]
     */
    public function transformInput(string $input): array
    {
        $floors = [];

        foreach (split_lines($input) as $floor) {
            $components = [];

            if (preg_match_all('/([a-z]+)(?:-compatible)?\s(microchip|generator)/', $floor, $parts, PREG_SET_ORDER)) {
                foreach ($parts as $part) {
                    $components[] = $part[1] . ' ' . $part[2];
                }
            }

            $floors[] = $components;
        }

        return $floors;
    }

    /**
     * Run the first part of the challenge.
     */
    public function part1(): int
    {
        return $this->findMinimumSteps($this->getInput());
    }

    /**
     * Run the second part of the challenge.
     */
    public function part2(): int
    {
        $floors = $this->getInput();
        $floors[0] = array_merge($floors[0], [
            'elerium generator',
            'elerium microchip',
            'dilithium generator',
            'dilithium microchip',
        ]);

        return $this->findMinimumSteps($floors);
    }

    /**
     * Find the minimum steps needed to lift all components to the fourth floor.
     */
    public function findMinimumSteps(array $floors): int
    {
        $steps = 0;
        $state = array_map('count', $floors);
        $total = array_sum($state);
        $slots = min($state[0], 2);
        $state[0] -= $slots;
        $level = 0;

        while ($state[3] + 1 != $total) {
            // go down
            while ($slots < 2 && $level > 0) {
                $level--;
                $taken = min($state[$level], 2 - $slots);

                if ($taken > 0) {
                    $slots += $taken;
                    $state[$level] -= $taken;
                }

                $steps++;
            }

            // go up
            while ($level < 3) {
                $level++;
                $taken = min($state[$level], 2 - $slots);

                if ($taken > 0) {
                    $slots += $taken;
                    $state[$level] -= $taken;
                }

                $steps++;
            }

            $state[3] += 1;

            $slots--;
        }

        return $steps;
    }
}
