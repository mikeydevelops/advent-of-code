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
 *
 * @return array{integer,integer}
 * @throws \Exception
 */
function aoc2015day15(): array
{
    $ingredients = getIngredients();
    $names = array_keys($ingredients);

    $cookies = [];

    foreach (numberCombinations(100, count($ingredients)) as $teaspoons) {
        $capacity = 0;
        $durability = 0;
        $flavor = 0;
        $texture = 0;
        $calories = 0;

        $combination = array_combine($names, $teaspoons);

        foreach ($combination as $ingredient => $teaspoons) {
            $ingredient = $ingredients[$ingredient];

            $capacity += $teaspoons * $ingredient['capacity'];
            $durability += $teaspoons * $ingredient['durability'];
            $flavor += $teaspoons * $ingredient['flavor'];
            $texture += $teaspoons * $ingredient['texture'];
            $calories += $teaspoons * $ingredient['calories'];
        }

        // we will scrap the whole cookie if
        // an ingredient has a negative score.
        if (min($capacity, $durability, $flavor, $texture) < 0) {
            continue;
        }

        $score = $capacity * $durability * $flavor * $texture;

        $cookies[] = $score;

        if ($calories == 500) {
            $calorieCookies[] = $score;
        }

    }

    return [max($cookies), max($calorieCookies)];
}

if (basename(__FILE__) == basename($_SERVER["SCRIPT_FILENAME"])) {
    [$cookieScore, $calorieScore] = aoc2015day15();

    line("1. The score of the highest-scoring cookie is: $cookieScore");
    line("2. The score of the highest-scoring cookie with max 500 calories is: $calorieScore");
}
