<?php

namespace Mike\AdventOfCode\Solutions\Year2015\Day15;

class Ingredient
{
    /**
     * The name of the ingredient.
     */
    public string $name;

    /**
     * How well will the cookie absorb milk.
     */
    public int $capacity;

    /**
     * How well keeps the cookie intact when full of milk.
     */
    public int $durability;

    /**
     * How tasty the cookie is.
     */
    public int $flavor;

    /**
     * How the cookie feels.
     */
    public int $texture;

    /**
     * How many calories it adds to the cookie.
     */
    public int $calories;

    /**
     * Create new instance of Ingredient.
     */
    public function __construct(string $name, int $capacity = 0, int $durability = 0, int $flavor = 0, int $texture = 0, int $calories = 0)
    {
        $this->name = $name;
        $this->capacity = $capacity;
        $this->durability = $durability;
        $this->flavor = $flavor;
        $this->texture = $texture;
        $this->calories = $calories;
    }

    /**
     * Create new instance of Ingredient from an array of properties.
     */
    public static function fromArray(string $name, array $properties): static
    {
        return new static(
            $name,
            $properties['capacity'] ?? 0,
            $properties['durability'] ?? 0,
            $properties['flavor'] ?? 0,
            $properties['texture'] ?? 0,
            $properties['calories'] ?? 0,
        );
    }
}
