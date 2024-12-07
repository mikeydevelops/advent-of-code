<?php

namespace Mike\AdventOfCode\Solutions\Year2019;

use Mike\AdventOfCode\Solutions\Solution;
use PDO;

class Day01 extends Solution
{
    /**
     * The example input to be used to test the solution.
     */
    protected ?string $exampleInput = <<<TXT
    12
    14
    1969
    100756
    TXT;

    /**
     * Process the input from the challenge.
     */
    public function transformInput(string $input): array
    {
        return array_map('intval', split_lines($input));
    }

    /**
     * Calculate the fuel required for given mass.
     */
    public function calculateFuel(int $mass): int
    {
        $fuel = floor($mass / 3) - 2;

        return $fuel < 0 ? 0 : $fuel;
    }

    /**
     * Calculate the total fuel required for given mass
     * including the mass of the resulting fuel.
     */
    public function calculateTotalFuel(int $mass): int
    {
        $fuel = [];

        do {
            $fuel[] = $mass = $this->calculateFuel($mass);
        } while ($mass > 0);

        return array_sum($fuel);
    }

    /**
     * Run the first part of the challenge.
     */
    public function part1(): int
    {
        $fuel = array_map([$this, 'calculateFuel'], $this->getInput());

        return array_sum($fuel);
    }

    /**
     * Run the second part of the challenge.
     */
    public function part2(): int
    {
        $totalFuel = array_map([$this, 'calculateTotalFuel'], $this->getInput());

        return array_sum($totalFuel);
    }
}
