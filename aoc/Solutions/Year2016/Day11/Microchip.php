<?php

namespace Mike\AdventOfCode\Solutions\Year2016\Day11;

use Mike\AdventOfCode\Solutions\Year2016\Day11\PeriodicTable;

class Microchip
{
    /**
     * Create new instance of Radioisotope Thermoelectric Microchip
     */
    public function __construct(public string $element)
    {
        //
    }

    /**
     * Convert the microchip to abbreviated string.
     */
    public function abbr(): string
    {
        return PeriodicTable::symbol($this->element) . 'M';
    }

    /**
     * Convert the microchip to string.
     */
    public function __toString(): string
    {
        return "$this->element-compatible microchip";
    }
}
