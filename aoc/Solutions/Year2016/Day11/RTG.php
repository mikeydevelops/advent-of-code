<?php

namespace Mike\AdventOfCode\Solutions\Year2016\Day11;

use Mike\AdventOfCode\Solutions\Year2016\Day11\PeriodicTable;

class RTG
{
    /**
     * Create new instance of Radioisotope Thermoelectric Generator
     */
    public function __construct(public string $element)
    {
        //
    }

    /**
     * Convert the generator to abbreviated string.
     */
    public function abbr(): string
    {
        return PeriodicTable::symbol($this->element) . 'G';
    }

    /**
     * Convert the generator to string.
     */
    public function __toString(): string
    {
        return "$this->element generator";
    }
}
