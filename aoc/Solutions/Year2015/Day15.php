<?php

namespace Mike\AdventOfCode\Solutions\Year2015;

use Mike\AdventOfCode\Solutions\Solution;

use Mike\AdventOfCode\Solutions\Year2015\Day15\Ingredient;

/**
 * @method  \Mike\AdventOfCode\Solutions\Year2015\Day15\Ingredient[]  getInput()  Get the ingredients.
 */
class Day15 extends Solution
{
    /**
     * The example input to be used to test the solution.
     */
    protected ?string $exampleInput = <<<TXT
    Butterscotch: capacity -1, durability -2, flavor 6, texture 3, calories 8
    Cinnamon: capacity 2, durability 3, flavor -2, texture -1, calories 3
    TXT;

    /**
     * The results of the simulation.
     */
    protected array $result = [];

    /**
     * Process the input from the challenge.
     *
     * @return \Mike\AdventOfCode\Solutions\Year2015\Day15\Ingredient[]
     */
    public function transformInput(string $input): array
    {
        $ingredients = [];

        foreach (split_lines($input) as $line) {
            [$name, $properties] = explode(': ', $line);

            $properties = array_sliding(explode(' ', str_replace(',', '', $properties)), 2, 2);
            $properties = array_combine(array_column($properties, 0), array_column($properties, 1));
            $properties = array_map('intval', $properties);

            $ingredients[$name] = Ingredient::fromArray($name, $properties);
        }

        return $ingredients;
    }

    /**
     * Hook before all parts are run.
     */
    public function before(): void
    {
        $ingredients = $this->getInput();
        $names = array_keys($ingredients);

        $cookies = [];
        $cookieCalories = [];

        foreach (number_combinations(100, count($ingredients)) as $teaspoons) {
            $capacity = 0;
            $durability = 0;
            $flavor = 0;
            $texture = 0;
            $calories = 0;

            $combination = array_combine($names, $teaspoons);

            foreach ($combination as $ingredient => $teaspoons) {
                $ingredient = $ingredients[$ingredient];

                $capacity += $teaspoons * $ingredient->capacity;
                $durability += $teaspoons * $ingredient->durability;
                $flavor += $teaspoons * $ingredient->flavor;
                $texture += $teaspoons * $ingredient->texture;
                $calories += $teaspoons * $ingredient->calories;
            }

            // we will scrap the whole cookie if
            // an ingredient has a negative score.
            if (min($capacity, $durability, $flavor, $texture) < 0) {
                continue;
            }

            $score = $capacity * $durability * $flavor * $texture;

            $cookies[] = $score;

            if ($calories == 500) {
                $cookieCalories[] = $score;
            }
        }

        $this->result = [max($cookies), max($cookieCalories)];
    }

    /**
     * Run the first part of the challenge.
     */
    public function part1(): int
    {
        return $this->result[0];
    }

    /**
     * Run the second part of the challenge.
     */
    public function part2(): int
    {
        return $this->result[1];
    }
}
