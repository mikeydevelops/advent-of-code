<?php

namespace Mike\AdventOfCode\Solutions\Year2016;

use Mike\AdventOfCode\Solutions\Solution;
use Mike\AdventOfCode\Solutions\Year2016\Day08\Command;

/**
 * @method  \Mike\AdventOfCode\Solutions\Year2016\Day08\Command[]  getInput()  Get the commands.
 */
class Day08 extends Solution
{
    /**
     * The example input to be used to test the solution.
     */
    protected ?string $exampleInput = <<<TXT
    rect 3x2
    rotate column x=1 by 1
    rotate row y=0 by 4
    rotate column x=1 by 1
    TXT;

    /**
     * Process the input from the challenge.
     *
     * @return \Mike\AdventOfCode\Solutions\Year2016\Day08\Command[]
     */
    public function transformInput(string $input): array
    {
        preg_match_all('/^([a-z]+)\s+(.*?)$/im', $input, $matches, PREG_SET_ORDER);

        return array_map(fn(array $match) => Command::fromString($match[1], $match[2]), $matches);
    }

    /**
     * Run the first part of the challenge.
     */
    public function part1(): int
    {
        $display =  $this->testing
            ? grid_make(7, 3, 0)
            : grid_make(50, 6, 0);

        $display = $this->computeImage($display, $this->getInput());

        return array_sum(array_map('array_sum', $display));
    }

    /**
     * Run the second part of the challenge.
     */
    public function part2(): string
    {
        $display = $this->testing
            ? grid_make(7, 3, 0)
            : grid_make(50, 6, 0);

        $display = $this->computeImage($display, $this->getInput());

        $this->renderDisplay($display, [' ', '█']);

        return $this->readDisplay($display);
    }

    /**
     * Compute image from given instructions.
     *
     * @param  int[][]  $display
     * @param  \Mike\AdventOfCode\Solutions\Year2016\Day08\Command[]  $commands
     * @return int[][]
     */
    public function computeImage(array $display, array $commands): array
    {
        foreach ($commands as $command) {
            $display = $command->updateDisplay($display);
        }

        return $display;
    }

    /**
     * Debug function to view contents of a display.
     */
    public function renderDisplay(array $display, array $replacers = null): static
    {
        $replacers = $replacers ?? [ '.', '#', ];

        foreach ($display as $row) {
            $line = '';

            foreach ($row as $pixel) {
                $line .= $replacers[$pixel] ?? $pixel;
            }

            $this->io->line($line);
        }

        return $this;
    }

    /**
     * Try to read the given display.
     */
    public function readDisplay(array $display): string
    {
        $result = '';
        $display = array_flip_row_column($display);

        $letters = array_sliding($display, 5, 5);

        $alphabet = [
            'A' => [5, 2, 2, 5, 0],
            'B' => [6, 3, 3, 3, 0],
            'C' => [4, 2, 2, 2, 0],
            'D' => [0, 0, 0, 0, 0],
            'E' => [6, 3, 3, 2, 0],
            'F' => [6, 2, 2, 1, 0],
            'G' => [4, 2, 3, 4, 0],
            'H' => [6, 1, 1, 6, 0],
            'I' => [0, 2, 6, 2, 0],
            'J' => [1, 1, 2, 5, 0],
            'K' => [6, 1, 3, 2, 0],
            'L' => [6, 1, 1, 1, 0],
            'M' => [0, 0, 0, 0, 0],
            'N' => [0, 0, 0, 0, 0],
            'O' => [4, 2, 2, 4, 0],
            'P' => [6, 2, 2, 2, 0],
            'Q' => [0, 0, 0, 0, 0],
            'R' => [6, 2, 3, 3, 0],
            'S' => [3, 3, 3, 2, 0],
            'T' => [0, 0, 0, 0, 0],
            'U' => [5, 1, 1, 5, 0],
            'V' => [0, 0, 0, 0, 0],
            'W' => [0, 0, 0, 0, 0],
            'X' => [0, 0, 0, 0, 0],
            'Y' => [2, 1, 3, 1, 2],
            'Z' => [3, 3, 3, 3, 0],
        ];

        foreach ($letters as $letter) {
            array_flip_row_column($letter);

            $pattern = array_map('array_sum', $letter);

            $letter = array_search($pattern, $alphabet) ?: '?';

            $result .= $letter;

            // unknown letter, show pattern
            if ($letter === '?') {
                dump(implode(', ', $pattern));
            }
        }

        return $result;
    }
}
