<?php

namespace Mike\AdventOfCode\Solutions\Year2024;

use Mike\AdventOfCode\Solutions\Solution;
use Mike\AdventOfCode\Solutions\Year2024\Day12\Region;

class Day12 extends Solution
{
    /**
     * The example input to be used to test the solution.
     */
    protected ?string $exampleInput = <<<TXT
    RRRRIICCFF
    RRRRIICCCF
    VVRRRCCFFF
    VVRCCCJFFF
    VVVVCJJCFE
    VVIVCCJJEE
    VVIIICJJEE
    MIIIIIJJEE
    MIIISIJEEE
    MMMISSJEEE
    TXT;

    /**
     * The dfs cache for visited cells.
     *
     * @var array<boolean[]>
     */
    protected array $visited = [];

    /**
     * Process the input from the challenge.
     */
    public function transformInput(string $input): array
    {
        return grid_parse($input, cellCallback: 'strval');
    }

    /**
     * Find connected regions based on given X and Y
     *
     * @param  array<string[]>  $grid  The grid.
     * @param  integer  $x  The starting X coordinate.
     * @param  integer  $y  The starting Y coordinate.
     * @param  \Mike\AdventOfCode\Solutions\Year2024\Day12\Region  The initial region info.
     * @return \Mike\AdventOfCode\Solutions\Year2024\Day12\Region
     */
    function dfsRegion(array $grid, int $x, int $y, Region $region): Region
    {
        if ($this->visited[$y][$x]) {
            return $region;
        }

        $this->visited[$y][$x] = true;

        $perimeter = array_filter(
            grid_get_adjacent_xy($grid, $x, $y, '#'),
            // remove diagonals and keep only different values.
            fn($v, $k) => strpos($k, '-') === false && $v[2] !== $region->label,
            ARRAY_FILTER_USE_BOTH
        );
        // remove the value of the point.
        $perimeter = array_map(fn ($p) => array_slice($p, 0, 2), $perimeter);
        $region->outsidePerimeter += count($perimeter);

        if (count($perimeter)) {
            $region->perimeter[] = [$x, $y];
        }

        $region->plot[] = [$x, $y];

        $neighbors = array_filter(
            grid_get_adjacent_xy($grid, $x, $y, '#'),
            // remove diagonals and keep same values.
            fn($v, $k) => strpos($k, '-') === false && $v[2] === $region->label,
            ARRAY_FILTER_USE_BOTH
        );

        foreach ($neighbors as $neighbor) {
            $region = $this->dfsRegion($grid, $neighbor[0], $neighbor[1], $region);
        }

        return $region;
    }

    /**
     * Find the regions in a grid.
     *
     * @param  array  $grid
     * @return \Mike\AdventOfCode\Solutions\Year2024\Day12\Region[]
     */
    public function regions(array $grid)
    {
        $regions = [];

        $this->visited = grid_make(count($grid[0]), count($grid), false);

        foreach (grid_walk($grid) as [$x, $y, $label]) {
            if ($this->visited[$y][$x]) {
                continue;
            }

            $region = $this->dfsRegion($grid, $x, $y, new Region($label));
            $region->perimeter = $this->isolatePerimeter($region->perimeter);

            $regions[] = $region;
        }

        foreach ($regions as $region) {
            $region->children = $this->findRegionChildren($regions, $region);
            $region->parent = $this->findRegionParent($regions, $region);
        }

        return $regions;
    }

    /**
     * Isolate the perimeter of the main shape.
     *
     * @param  array<array{int,int}>  $perimeter
     * @return array<array{int,int}>
     */
    protected function isolatePerimeter(array $perimeters): array
    {
        $visited = [];
        $directions = [
            [-1, 0], [1, 0], [0, -1], [0, 1],  // Orthogonal
            [-1, -1], [-1, 1], [1, -1], [1, 1], // Diagonal
        ];

        foreach ($perimeters as $point) {
            if (in_array($point, $visited)) {
                continue;
            }

            $connected = [];
            $queue = [$point];

            while ($queue) {
                $p = array_pop($queue);

                if (in_array($p, $visited)) {
                    continue;
                }

                $visited[] = $p;
                $connected[] = $p;

                foreach ($directions as $dir) {
                    $neighbor = [$p[0] + $dir[0], $p[1] + $dir[1]];

                    if (! in_array($neighbor, $perimeters) || in_array($neighbor, $visited)) {
                        continue;
                    }

                    $queue[] = $neighbor;
                }
            }

            return $connected;
        }

        return [];
    }

    /**
     * Find the children of the given region.
     *
     * @param  \Mike\AdventOfCode\Solutions\Year2024\Day12\Regionp[]  $regions
     * @param  \Mike\AdventOfCode\Solutions\Year2024\Day12\Region  $region
     * @return \Mike\AdventOfCode\Solutions\Year2024\Day12\Region[]
     */
    protected function findRegionChildren(array $regions, Region $region): array
    {
        return array_values(array_filter($regions, fn (Region $r) => $r !== $region && $region->contains($r)));
    }

    /**
     * Find the parent of the given region.
     *
     * @param  \Mike\AdventOfCode\Solutions\Year2024\Day12\Regionp[]  $regions
     * @param  \Mike\AdventOfCode\Solutions\Year2024\Day12\Region  $region
     * @return \Mike\AdventOfCode\Solutions\Year2024\Day12\Region|null
     */
    protected function findRegionParent(array $regions, Region $region): Region|null
    {
        $parents = array_filter($regions, fn (Region $r) => $r !== $region && $r->contains($region));

        // get the first matching parent, there should be only one parent
        // if no parents, return null
        return reset($parents) ?: null;
    }

    /**
     * Run the first part of the challenge.
     */
    public function part1(): int
    {
        $price = 0;

        foreach ($this->regions($this->getInput()) as $region) {
            $price += $region->area() * $region->outsidePerimeter;
        }

        return $price;
    }

    /**
     * Run the second part of the challenge.
     */
    public function part2()//: int
    {
        // $price = 0;

        // foreach ($this->regions($this->getInput()) as $region) {
        //     $a = $region->area();
        //     $s = $region->sides();
        //     $p = $a * $s;
        //     $price += $p;
        // }

        // return $price;
    }
}
