<?php

require_once __DIR__ . '/../../common.php';

/**
 * Get the ingredients for the cookie.
 *
 * @return array[]
 * @throws \Exception
 */
function getIngredients() : array
{
    $ingredients = [];

    foreach (explode("\n", getInput()) as $line) {
        [$name, $properties] = explode(': ', $line);

        $properties = array_sliding(explode(' ', str_replace(',', '', $properties)), 2, 2);
        $properties = array_combine(array_column($properties, 0), array_column($properties, 1));
        $properties = array_map('intval', $properties);

        $ingredients[$name] = $properties;
    }

    return $ingredients;
}

/**
 * Advent of Code 2015
 * Day 15: Science for Hungry People
 * Part One
 *
 * @return integer
 * @throws \Exception
 */
function aoc2015day15part1(): int
{
    $ingredients = getIngredients();
    $names = array_keys($ingredients);

    $cookies = [];

    foreach (numberCombinations(100, count($ingredients)) as $teaspoons) {
        $capacity = 0;
        $durability = 0;
        $flavor = 0;
        $texture = 0;

        $combination = array_combine($names, $teaspoons);

        foreach ($combination as $ingredient => $teaspoons) {
            $ingredient = $ingredients[$ingredient];

            $capacity += $teaspoons * $ingredient['capacity'];
            $durability += $teaspoons * $ingredient['durability'];
            $flavor += $teaspoons * $ingredient['flavor'];
            $texture += $teaspoons * $ingredient['texture'];
        }

        // we will scrap the whole cookie if
        // an ingredient has a negative score.
        if (min($capacity, $durability, $flavor, $texture) < 0) {
            $cookies[] = 0;

            continue;
        }

        $cookies[] = $capacity * $durability * $flavor * $texture;
    }

    return max($cookies);
}

if (basename(__FILE__) == basename($_SERVER["SCRIPT_FILENAME"])) {
    $cookieScore = aoc2015day15part1();

    line("1. The score of the highest-scoring cookie is: $cookieScore");
}
