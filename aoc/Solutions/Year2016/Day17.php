<?php

namespace Mike\AdventOfCode\Solutions\Year2016;

use Mike\AdventOfCode\Solutions\Solution;

/**
 * @method  string  getInput()  get the passcode
 */
class Day17 extends Solution
{
    /**
     * The example input to be used to test the solution.
     *
     * hijkl -> DUR - does not work
     *
     * more examples:
     * ihgpwlah -> DDRRRD
     * kglvqrro -> DDUDRLRRUDRD
     * ulqzkmiv -> DRURDRUDDLLDLUURRDULRLDUUDDDRR
     */
    protected ?string $exampleInput = 'hijkl';

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
        $grid = grid_make(4, 4, 0);
        $grid[0][0] = 1;
        $passcode = $this->getInput();
        $path = '';

        do {
            $directions = array_keys(array_filter($this->getDirections($passcode.$path)));

            foreach ($directions as $dir) {
                $y = $dir == 'u' ? +1 : -1;
                $x = $dir == 'r' ? +1 : -1;

                if (! isset($grid[$y][$x])) {
                    continue;
                }
            }
        } while (! $grid[3][3]);

        return '<not implemented>';
    }

    /**
     * Run the second part of the challenge.
     */
    public function part2()
    {
        //
    }

    /**
     * MD5 hashes input string and gives an array of
     * 'string' => booleans showing which direction is locked or unlocked.
     */
    public function getDirections(string $passcodeAndPreviousDirections): array
    {
        $hash = md5($passcodeAndPreviousDirections);

        return [
            'u' => hexdec($hash[0]) > 10,
            'd' => hexdec($hash[1]) > 10,
            'l' => hexdec($hash[2]) > 10,
            'r' => hexdec($hash[3]) > 10,
        ];
    }

    /**
     * Render the grid in the console as shown in the challenge.
     */
    public function renderGrid(array $grid): static
    {
        $height = count($grid);
        $width = count(current($grid));

        $wall = '#';
        $doorV = '|';
        $doorH = '-';

        $this->io->line(str_repeat($wall, $width*2+1));

        foreach ($grid as $idx => $row) {
            $row = array_map(fn($v) => $v ? 'S' : ' ', $row);
            $doors = array_fill(0, $width - 1, $doorV);

            $row = array_merge_alternating($row, $doors);

            $end = $idx < $width-1 ? $wall : '';

            $this->io->line($wall . implode('', $row) . $end);

            if ($idx < $width-1) {
                $row = array_fill(0, $width, $doorH);
                $row = array_merge_alternating($row, array_fill(0, $width - 1, $wall));

                $this->io->line($wall . implode('', $row) . $wall);
            }
        }

        $this->io->line(str_repeat($wall, $width*2-1).' V');

        return $this;
    }
}
