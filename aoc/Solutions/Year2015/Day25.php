<?php

namespace Mike\AdventOfCode\Solutions\Year2015;

use Mike\AdventOfCode\Solutions\Solution;

/**
 * @method  int[]  getInput()  Get the location
 */
class Day25 extends Solution
{
    /**
     * The example input to be used to test the solution.
     */
    protected ?string $exampleInput = 'row 2, column 1';

    /**
     * Process the input from the challenge.
     *
     * @return int[]
     */
    public function transformInput(string $input): array
    {
        $input = trim($input);

        preg_match('/row\s+(\d+)/i', $input, $rowMatches);
        preg_match('/column\s+(\d+)/i', $input, $columnMatches);

        return [intval($columnMatches[1]), intval($rowMatches[1])];
    }

    /**
     * Run the first part of the challenge.
     */
    public function part1(): int
    {
        [$x, $y] = $this->getInput();

        return $this->findMachineCode($x, $y);
    }

    /**
     * Run the second part of the challenge.
     */
    public function part2(): string
    {
        return 'free';
    }

    /**
     * Find santa's weather machine copy protection code.
     *
     * @param  integer  $targetX
     * @param  integer  $targetY
     * @return integer
     */
    public function findMachineCode(int $targetX, int $targetY): int
    {
        $code = 20151125;

        for ($i = 1; $i <= $targetX * $targetY; $i++) {
            $row = $i;
            $col = 1;

            do {
                if ($row == $targetY && $col == $targetX) {
                    return $code;
                }

                $code = $this->generateCode($code);

                $row--;
                $col++;
            } while ($row >= 1);
        }

        return $code;
    }

    /**
     * Generate machine code based on previous code.
     *
     * @param  integer  $previous
     * @return integer
     */
    public function generateCode(int $previous): int
    {
        return $previous * 252533 % 33554393;
    }
}
