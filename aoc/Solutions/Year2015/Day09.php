<?php

namespace Mike\AdventOfCode\Solutions\Year2015;

use Mike\AdventOfCode\Solutions\Solution;

use Mike\AdventOfCode\Year2015\Day09\Distance;

/**
 * @method \Mike\AdventOfCode\Solutions\Year2015\Day09\Distance[] getInput() Get the list of locations.
 */
class Day09 extends Solution
{
    /**
     * The example input to be used to test the solution.
     */
    protected ?string $exampleInput = <<<TXT
    London to Dublin = 464
    London to Belfast = 518
    Dublin to Belfast = 141
    TXT;

    /**
     * Process the input from the challenge.
     *
     * @return \Mike\AdventOfCode\Solutions\Year2015\Day09\Distance[]
     */
    public function transformInput(string $input): array
    {
        return array_map(function ($distance) {
            [$from, $to, $length] = explode(' ', str_replace(['to ', '= '], '', $distance));

            return Distance::instance($from, $to, $length);
        }, explode("\n", $input));
    }

    /**
     * Run the first part of the challenge.
     */
    public function part1(): int
    {
        $locations = $this->getLocations($this->getInput());

        $routes = $this->findRoutes($locations);

        return min($routes);
    }

    /**
     * Run the second part of the challenge.
     */
    public function part2(): int
    {
        $locations = $this->getLocations($this->getInput());

        $routes = $this->findRoutes($locations);

        return max($routes);
    }

    /**
     * Get all unique locations.
     *
     * @param  \Mike\AdventOfCode\Solutions\Year2015\Day09\Distance[]  $distances
     * @return string[]
     */
    function getLocations(array $distances) : array
    {
        $locations = [];

        foreach ($distances as $distance) {
            if (! in_array($from = $distance->getFrom(), $locations)) {
                $locations[] = $from;
            }

            if (! in_array($to = $distance->getTo(), $locations)) {
                $locations[] = $to;
            }
        }

        return $locations;
    }

    /**
     * Return possible routes.
     *
     * @param  string[]  $locations
     * @return array[]
     */
    function findRoutes(array $locations): array
    {
        $routes = [];

        $len = count($locations);

        foreach (array_permutations($locations) as $route) {

            if ($len != count($route)) {
                continue;
            }

            $total = 0;

            foreach (array_sliding($route, 2) as $distance) {
                // if a distance is invalid, the whole route is invalid.
                if (! Distance::isValid($from = $distance[0], $to = $distance[1])) {
                    continue 2;
                }

                $total += Distance::length($from, $to);
            }

            $routes[implode(' -> ', $route)] = $total;
        }

        return $routes;
    }
}
