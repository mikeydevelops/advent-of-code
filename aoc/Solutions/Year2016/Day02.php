<?php

namespace Mike\AdventOfCode\Solutions\Year2016;

use Mike\AdventOfCode\Solutions\Solution;

/**
 * @method  string[][]  getInput()  Get the instructions.
 */
class Day02 extends Solution
{
    /**
     * The example input to be used to test the solution.
     */
    protected ?string $exampleInput = <<<TXT
    ULL
    RRDDD
    LURDL
    UUUUD
    TXT;

    /**
     * Process the input from the challenge.
     *
     * @return string[][]
     */
    public function transformInput(string $input): array
    {
        return array_map('str_split', split_lines($input));
    }

    /**
     * Run the first part of the challenge.
     */
    public function part1(): int
    {
        $code = '';

        $keypad = [
            [1, 2, 3],
            [4, 5, 6],
            [7, 8, 9],
        ];
        $x = 1;
        $y = 1;

        foreach ($this->getInput() as $ins) {
            foreach ($ins as $i) {
                $i == 'U' ? ($y --) : null;
                $i == 'R' ? ($x ++) : null;
                $i == 'D' ? ($y ++) : null;
                $i == 'L' ? ($x --) : null;

                $x = $x > 2 ? 2 : ($x < 0 ? 0 : $x);
                $y = $y > 2 ? 2 : ($y < 0 ? 0 : $y);
            }

            $code .= $keypad[$y][$x];
        }

        return intval($code);
    }

    /**
     * Run the second part of the challenge.
     */
    public function part2(): string
    {
        $code = '';

        $keypad = [
            [0,  0,   1,   0,  0],
            [0,  2,   3,   4,  0],
            [5,  6,   7,   8,  9],
            [0, 'A', 'B', 'C', 0],
            [0,  0,  'D',  0,  0],
        ];
        $x = 0;
        $y = 2;

        foreach ($this->getInput() as $ins) {
            foreach ($ins as $i) {
                $i == 'U' && !$this->isAtEdge($keypad, $x, $y-1) ? ($y --) : null;
                $i == 'R' && !$this->isAtEdge($keypad, $x+1, $y) ? ($x ++) : null;
                $i == 'D' && !$this->isAtEdge($keypad, $x, $y+1) ? ($y ++) : null;
                $i == 'L' && !$this->isAtEdge($keypad, $x-1, $y) ? ($x --) : null;
            }

            $code .= $keypad[$y][$x];
        }

        return $code;
    }

    /**
     * Check to see if given coordinates result in an edge for given keypad.
     */
    public function isAtEdge(array $keypad, int $x, int $y): bool
    {
        return !isset($keypad[$y][$x]) || $keypad[$y][$x] === 0;
    }
}
