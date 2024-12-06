<?php

namespace Mike\AdventOfCode\Solutions\Year2024;

use Mike\AdventOfCode\Solutions\Solution;

class Day04 extends Solution
{
    /**
     * The example input to be used to test the solution.
     */
    protected ?string $exampleInput = <<<TXT
    MMMSXXMASM
    MSAMXMSMSA
    AMXSXMAAMM
    MSAMASMSMX
    XMASAMXAMM
    XXAMMXXAMA
    SMSMSASXSS
    SAXAMASAAA
    MAMMMXMMMM
    MXMXAXMASX
    TXT;

    /**
     * Process the input from the challenge.
     */
    public function transformInput(string $input): mixed
    {
        return array_map('str_split', preg_split('/\r?\n/', $input));
    }

    public function renderGrid(array $grid, array $matches, string $search): void
    {
        $directions = [
            'top-bottom'        => [ 0,  1],
            'left-right'        => [ 1,  0],
            'right-left'        => [-1,  0],
            'bottom-top'        => [ 0, -1],
            'diag-top-left'     => [-1, -1],
            'diag-bottom-left'  => [-1,  1],
            'diag-top-right'    => [ 1, -1],
            'diag-bottom-right' => [ 1,  1],
        ];

        $colors = [
            'yellow', 'red', 'green', 'blue',
        ];

        $search = str_split($search);

        foreach ($matches as [$y, $x, $dir]) {
            foreach ($search as $idx => $char) {
                if ($idx > 0) {
                    $x += $directions[$dir][0];
                    $y += $directions[$dir][1];
                }

                if (strlen($grid[$y][$x] ?? '') == 1) {
                    $grid[$y][$x] = "<fg=$colors[$idx]>$char</>";
                }
            }
        }

        array_map([$this->io, 'line'], array_map(fn ($row) => implode('', $row), $grid));
    }

    /**
     * Run the first part of the challenge.
     */
    public function part1(): int
    {
        // word_search is in /includes/helpers.php
        $occurrences = word_search($grid = $this->getInput(), $search = 'XMAS');

        // make it pretty when using the cli when -v verbose level 1 is specified.
        if ($this->io->getOutput()->isVerbose()) {
            $this->renderGrid($grid, $occurrences, $search);
        }

        return count($occurrences);
    }

    /**
     * Run the second part of the challenge.
     */
    public function part2(): int
    {
        $count = 0;

        foreach (walk_2d_grid($grid = $this->getInput()) as [$col, $row, $char]) {
            if ($char !== 'A') {
                continue;
            }

            $diagonals = [
                $grid[$row - 1][$col - 1] ?? '', // top left
                $grid[$row - 1][$col + 1] ?? '', // top right
                $grid[$row + 1][$col - 1] ?? '', // bottom left
                $grid[$row + 1][$col + 1] ?? '', // bottom right
            ];

            $diagonals = array_filter($diagonals, fn($d) => in_array($d, ['M', 'S']));

            if (count($diagonals) != 4
                // only diagonals that have different edges
                || $diagonals[0] == $diagonals[3] || $diagonals[1] == $diagonals[2]) {
                continue;
            }

            $count ++;
        }

        return $count;
    }
}
