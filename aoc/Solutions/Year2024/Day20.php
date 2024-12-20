<?php

namespace Mike\AdventOfCode\Solutions\Year2024;

use Mike\AdventOfCode\Solutions\Solution;

class Day20 extends Solution
{
    /**
     * The example input to be used to test the solution.
     */
    protected ?string $exampleInput = <<<TXT
    ###############
    #...#...#.....#
    #.#.#.#.#.###.#
    #S#...#.#.#...#
    #######.#.#.###
    #######.#.#...#
    #######.#.###.#
    ###..E#...#...#
    ###.#######.###
    #...###...#...#
    #.#####.#.###.#
    #.#...#.#.#...#
    #.#.#.#.#.#.###
    #...#...#...###
    ###############
    TXT;

    #region  Map values
    const WALL = 0;
    const EMPTY = 1;
    const START = 2;
    const END = 3;
    #endregion

    /**
     * Get the map.
     *
     * @return array<integer[]>
     */
    public function map(): array
    {
        $stream = $this->streamInput();
        $map = [];
        $keys = [
            '#' => static::WALL,
            '.' => static::EMPTY,
            'S' => static::START,
            'E' => static::END,
        ];

        while ($row = trim(fgets($stream) ?: '')) {
            $map[] = array_map(fn($c) => $keys[$c], str_split($row));
        }

        fclose($stream);

        return $map;
    }

    /**
     * Find the path in a map.
     *
     * @param  array<integer[]>  $map  The map.
     * @return array<array{int,int,int}>
     */
    protected function findPath(array $map): array
    {
        $path = [];

        $i = 0;
        $prev = null;
        $point = grid_search($map, static::END)->current();

        while ($point) {
            [$x, $y, $v] = $point;

            $path[] = [$x, $y, $i++];

            $neighbors = array_filter([
                [$x,$y-1, $map[$y-1][$x]], // top
                [$x+1,$y, $map[$y][$x+1]], // right
                [$x,$y+1, $map[$y+1][$x]], // bottom
                [$x-1,$y, $map[$y][$x-1]], // left
            ], fn($n) => ($n[2] === static::EMPTY || $n[2] === static::START) && $n !== $prev);

            $prev = $point;
            $point = reset($neighbors);
        }

        return array_reverse($path);
    }

    /**
     * Count cheats that can be made using given path in the given limit.
     * Using manhattan distance to determine which points to pair.
     *
     * @param  array<array{int,int}>  $path  The path along which to search.
     * @param  integer  $limit  The maximum perimeter to find pairs in.
     * @param  integer  $min  The minimum time saved to search for.
     * @return int  The number of cheats save at least 100 picoseconds.
     */
    protected function countCheats(array $path, int $limit, int $min): int
    {
        $cheats = 0;
        $count = count($path);

        // sort by x
        usort($path, fn ($a, $b) => $a[0] <=> $b[0]);

        // find pairs that are within the given $limit
        for ($i = 0; $i < $count; $i++) {
            $j = $i + 1;
            $first = $path[$i];

            while ($j < $count && (($second = $path[$j++])[0] - $first[0]) <= $limit) {
                $dy = abs($first[1] - $second[1]);

                if ($dy > $limit) {
                    continue;
                }

                $dx = abs($first[0] - $second[0]);
                $dist = $dx + $dy;

                if ($dist > $limit || $dist < 1) {
                    continue;
                }

                $timeDiff = abs($first[2] - $second[2]) - $dist;

                if ($timeDiff >= $min) {
                    $cheats++;
                }
            }
        }

        return $cheats;
    }

    /**
     * Run the first part of the challenge.
     */
    public function part1(): int
    {
        return $this->countCheats(
            $this->findPath($this->map()),
            limit: 2,
            min: $this->testing ? 2 : 100,
        );
    }

    /**
     * Run the second part of the challenge.
     */
    public function part2(): int
    {
        return $this->countCheats(
            $this->findPath($this->map()),
            limit: 20,
            min: $this->testing ? 50 : 100,
        );
    }
}
