<?php

require_once __DIR__ . '/../../common.php';
require_once __DIR__ . '/Distance.php';

/**
 * Parse the raw distance string.
 *
 * @param  string[]  $distances
 * @return \Distance[]
 */
function parseDistances(array $distances) : array
{
    return array_map(function ($distance) {
        [$from, $_, $to, $_, $length] = explode(' ', $distance);

        return Distance::instance($from, $to, $length);
    }, $distances);
}

/**
 * Get all unique locations.
 *
 * @param  \Distance[]  $distances
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
function findRoutes(array $locations) : array
{
    $routes = [];

    $len = count($locations);

    foreach (combinations($locations, $len) as $route) {
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

/**
 * Advent of Code 2015
 * Day 9: All in a Single Night
 * Part One
 *
 * @return integer
 * @throws \Exception
 */
function aoc2015day9part1(): int
{
    $distances = parseDistances(explode("\n", getInput()));

    $locations = getLocations($distances);

    $routes = findRoutes($locations);

    return min($routes);
}

if (basename(__FILE__) == basename($_SERVER["SCRIPT_FILENAME"])) {
    $distance = aoc2015day9part1();

    line("1. The shortest distance is: $distance");
}
