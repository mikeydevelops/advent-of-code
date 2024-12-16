<?php

namespace Mike\AdventOfCode\Solutions\Year2024\Day12;

class Region
{
    /**
     * The label of the region.
     */
    public ?string $label = null;

    /**
     * The perimeter points of the region.
     *
     * @var array{int,int}[]
     */
    public array $perimeter = [];

    /**
     * The amount of perimeter points around this region.
     */
    public int $outsidePerimeter = 0;

    /**
     * The points that make this region.
     *
     * @var array{int,int}[]
     */
    public array $plot = [];

    /**
     * The regions inside this region.
     *
     * @var \Mike\AdventOfCode\Solutions\Year2024\Day12\Region[]
     */
    public array $children = [];

    /**
     * The parent region this region is in.
     */
    public ?Region $parent = null;

    /**
     * Create new instance of Region.
     *
     * @param  string|null  $label  The label of the region.
     */
    public function __construct(?string $label = null)
    {
        $this->label = $label;
    }

    /**
     * Get the area of the region.
     */
    public function area(): int
    {
        return count($this->plot);
    }

    /**
     * Get the number of sides this region has.
     */
    public function sides(): int
    {
        return $this->corners();
    }

    /**
     * Get a count of the corners of this region.
     */
    public function corners(): int
    {
        // min corners of any shape.
        $corners = 0;

        $rows = array_map(function ($row) {
            $x = array_column($row, 0);

            return [min($x), max($x), $x];
        }, grid_group_by($this->plot, 1));

        $minX = null;
        $maxX = null;

        // add two corners if the min or max X value changes
        foreach ($rows as $row => [$rowMin, $rowMax, $x]) {
            $corners += $rowMin !== $minX ? 2 : 0;
            $corners += $rowMax !== $maxX ? 2 : 0;

            $minX = $rowMin;
            $maxX = $rowMax;

            // if the row does not contain continuous Xs
            for ($i = $rowMin; $i < $rowMax; $i++) {
                if (in_array($i, $x)) {
                    continue;
                }

                // if the point is surrounded, the corner is ignored.
                if ($this->contains([$i, $row])) {
                    continue;
                }

                // found a gap, every continuous gap adds 2 more corners.
                $corners += 2;

                // find the rest of the gap.
                $j = $i;

                while (! in_array($j, $x)) {
                    $j ++;
                }

                // set the loop to the next x that is valid.
                $i = $j - 1;
            }
        }

        return $corners + array_sum(array_map(fn(Region $child) => $child->corners(), $this->children));
    }

    /**
     * Check if given region is inside this region.
     * Note: Checks only if the first point in the region is inside this region and not the whole region.
     *
     * @param \Mike\AdventOfCode\Solutions\Year2024\Day12\Region|array{int,int}  $region
     * @return boolean
     */
    public function contains(Region|array $region): bool
    {
        if (in_array($region, $this->children)) {
            return true;
        }

        $columns = grid_group_by($this->perimeter, 0);
        $rows = grid_group_by($this->perimeter, 1);

        [$x, $y] = is_array($region) ? $region : $region->plot[0];

        if (! isset($columns[$x]) || ! isset($rows[$y])) {
            return false;
        }

        $column = array_column($columns[$x], 1);
        $row = array_column($rows[$y], 0);

        return $x >= min($row) && $x <= max($row)
            && $y >= min($column) && $y <= max($column);
    }
}
