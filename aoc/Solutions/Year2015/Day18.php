<?php

namespace Mike\AdventOfCode\Solutions\Year2015;

use Mike\AdventOfCode\Solutions\Solution;

/**
 * @method  array[]  getInput()  Get the light grid.
 */
class Day18 extends Solution
{
    /**
     * The example input to be used to test the solution.
     */
    protected ?string $exampleInput = <<<TXT
    .#.#.#
    ...##.
    #....#
    ..#...
    #.#..#
    ####..
    TXT;

    /**
     * Process the input from the challenge.
     */
    public function transformInput(string $input): array
    {
        return array_map('str_split', explode("\n", trim($input)));
    }

    /**
     * Run the first part of the challenge.
     */
    public function part1(): int
    {
        $animated = grid_animate($this->getInput(), $this->testing ? 4 : 100, [$this, 'updateLight']);

        $states = array_count_values(array_merge(...$animated));

        return $states['#'];
    }

    /**
     * Run the second part of the challenge.
     */
    public function part2(): int
    {
        $grid = $this->getInput();

        $first = 0;
        $last = count($grid) - 1;

        $before = $after = function ($grid) use ($first, $last) {
            $grid[$first][$first] = '#';
            $grid[$first][$last] = '#';
            $grid[$last][$first] = '#';
            $grid[$last][$last] = '#';

            return $grid;
        };

        $callback = function ($light, $frame, $grid) use ($first, $last) {
            if ($light['x'] == $first && $light['y'] == $first) {
                return '#';
            }

            if ($light['x'] == $first && $light['y'] == $last) {
                return '#';
            }

            if ($light['x'] == $last && $light['y'] == $first) {
                return '#';
            }

            if ($light['x'] == $last && $light['y'] == $last) {
                return '#';
            }

            return $this->updateLight($light, $frame, $grid);
        };

        $animated = grid_animate($before($grid), $this->testing ? 4 : 100, $callback, $before, $after);

        $states = array_count_values(array_merge(...$animated));

        return $states['#'];
    }

    /**
     * Update the state of a light.
     */
    function updateLight(array $light, int $frame, array $grid): string
    {
        $states = array_count_values($light['adjacent']);
        $light = $light['value'];

        if ($light == '#') { // on
            // light stays on when 2 or 3 adjacent lights are also on.
            return in_array($states['#'] ?? 0, [2, 3]) ? '#' : '.';
        }

        // light turns on if exactly 3 adjacent lights are on.
        return ($states['#'] ?? 0) == 3 ? '#' : '.';
    }
}
