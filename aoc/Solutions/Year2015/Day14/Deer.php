<?php

namespace Mike\AdventOfCode\Solutions\Year2015\Day14;

class Deer
{
    /**
     * The name of the deer.
     */
    public string $name;

    /**
     * The top speed of the deer.
     */
    public int $speed;

    /**
     * The amount of time the deer can sustain top speed.
     */
    public int $stamina;

    /**
     * The amount of time the deer needs to rest to be able to reach top speed.
     */
    public int $rest;

    /**
     * Create new instance of Deer.
     */
    public function __construct(string $name, int $speed = 0, int $stamina = 0, int $rest = 0)
    {
        $this->name = $name;
        $this->speed = $speed;
        $this->stamina = $stamina;
        $this->rest = $rest;
    }
}
