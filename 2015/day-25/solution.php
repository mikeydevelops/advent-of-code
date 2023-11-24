<?php

/**
 * Get coordinates for machine code.
 *
 * @return integer[]
 * @throws \Exception
 */
function getCodeLocation(): array
{
    $input = getInput();

    preg_match('/row\s+(\d+)/i', $input, $rowMatches);
    preg_match('/column\s+(\d+)/i', $input, $columnMatches);

    return [intval($columnMatches[1]), intval($rowMatches[1])];
}

/**
 * Generate machine code based on previous code.
 *
 * @param  integer  $previous
 * @return integer
 */
function generateCode(int $previous): int
{
    return $previous * 252533 % 33554393;
}

/**
 * Find santa's weather machine copy protection code.
 *
 * @param  integer  $targetX
 * @param  integer  $targetY
 * @return integer
 */
function findMachineCode(int $targetX, int $targetY): int
{
    $code = 20151125;

    for ($i = 1; $i <= $targetX * $targetY; $i++) {
        $row = $i;
        $col = 1;

        do {
            if ($row == $targetY && $col == $targetX) {
                return $code;
            }

            $code = generateCode($code);

            $row--;
            $col++;
        } while ($row >= 1);
    }

    return $code;
}

/**
 * Advent of Code 2015
 * Day 25: Let It Snow
 * Part One
 *
 * @return int
 */
function aoc2015day25part1(): int
{
    [$x, $y] = getCodeLocation();

    return findMachineCode($x, $y);
}

if (basename(__FILE__) == basename($_SERVER["SCRIPT_FILENAME"])) {
    $part1 = aoc2015day25part1();

    line("1. The code for the machine is: $part1.");
}
