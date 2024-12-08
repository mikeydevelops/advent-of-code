<?php

namespace Mike\AdventOfCode\Solutions\Year2024;

use Mike\AdventOfCode\Solutions\Solution;

class Day08 extends Solution
{
    /**
     * The example input to be used to test the solution.
     */
    protected ?string $exampleInput = <<<TXT
    ............
    ........0...
    .....0......
    .......0....
    ....0.......
    ......A.....
    ............
    ............
    ........A...
    .........A..
    ............
    ............
    TXT;

    /**
     * Process the input from the challenge.
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
        $map = $this->getInput();

        $antinodes = grid_make(count($map), count($map[0]), '.');

        $frequencies = grid_count_values($map);
        // remove empty spaces.
        unset($frequencies['.']);

        // find all the locations for each frequency
        $frequencies = array_map(
            fn($freq) => iterator_to_array(grid_search($map, $freq)),
            $keys = array_keys($frequencies)
        );
        $frequencies = array_combine($keys, $frequencies);

        foreach ($frequencies as $freq => $locations) {
            foreach (combinations($locations, 2) as [$start, $end]) {
                // I wasted so much time on the words
                // "perfectly in line with two antennas of the same frequency"
                // but you just had to get the distance between two antennas
                // and add it to the ends of each frequency
                // $line = grid_line($map, $start, $end, false);

                // something is blocking line of sight
                // if (count(array_filter($line, fn($cell) => $cell[2] === '.')) !== count($line)) {
                //     continue;
                // }

                $xDiff = $start[0] - $end[0];
                $yDiff = $start[1] - $end[1];

                // first antinode
                $newX = $start[0] + $xDiff;
                $newY = $start[1] + $yDiff;

                // skip out of bounds
                if (($antinodes[$newY][$newX] ?? null) === '.') {
                    $antinodes[$newY][$newX] = '#';
                }

                // second antinode
                $newX = $end[0] - $xDiff;
                $newY = $end[1] - $yDiff;

                // skip out of bounds
                if (($antinodes[$newY][$newX] ?? null) === '.') {
                    $antinodes[$newY][$newX] = '#';
                }
            }
        }

        $this->io->getOutput()->isVerbose() && grid_print($antinodes);

        return grid_count_values($antinodes, '#');
    }

    /**
     * Run the second part of the challenge.
     */
    public function part2(): int
    {
        $map = $this->getInput();

        $antinodes = grid_make(count($map), count($map[0]), '.');

        $frequencies = grid_count_values($map);
        // remove empty spaces.
        unset($frequencies['.']);

        // find all the locations for each frequency
        $frequencies = array_map(
            fn($freq) => iterator_to_array(grid_search($map, $freq)),
            $keys = array_keys($frequencies)
        );
        $frequencies = array_combine($keys, $frequencies);

        foreach ($frequencies as $freq => $locations) {
            foreach (combinations($locations, 2) as [$start, $end]) {
                // I wasted so much time on the words
                // "perfectly in line with two antennas of the same frequency"
                // but you just had to get the distance between two antennas
                // and add it to the ends of each frequency
                // $line = grid_line($map, $start, $end, false);

                // something is blocking line of sight
                // if (count(array_filter($line, fn($cell) => $cell[2] === '.')) !== count($line)) {
                //     continue;
                // }

                $antinodes[$start[1]][$start[0]] = '#';
                $antinodes[$end[1]][$end[0]] = '#';

                $xDiff = $start[0] - $end[0];
                $yDiff = $start[1] - $end[1];

                $newX = $start[0];
                $newY = $start[1];

                // repeat the antinodes until the start of the map.
                do {
                    $newX += $xDiff;
                    $newY += $yDiff;

                    // skip out of bounds
                    if (($antinodes[$newY][$newX] ?? null) === '.') {
                        $antinodes[$newY][$newX] = '#';
                    }
                } while (isset($antinodes[$newY][$newX]));

                $newX = $end[0];
                $newY = $end[1];

                // repeat the antinodes until the end of the map.
                do {
                    $newX -= $xDiff;
                    $newY -= $yDiff;

                    // skip out of bounds
                    if (($antinodes[$newY][$newX] ?? null) === '.') {
                        $antinodes[$newY][$newX] = '#';
                    }
                } while (isset($antinodes[$newY][$newX]));
            }
        }

        $this->io->getOutput()->isVerbose() && grid_print($antinodes);

        return grid_count_values($antinodes, '#');
    }
}
