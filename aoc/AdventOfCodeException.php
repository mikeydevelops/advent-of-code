<?php

namespace Mike\AdventOfCode;

use Exception;

class AdventOfCodeException extends Exception
{
    /**
     * Create new advent of code exception for invalid year.
     */
    public static function invalidYear(int $year, int $maxYear): static
    {
        return new static("The specified year is invalid [$year]. The year must be >= 2015 and <= $maxYear", 1);
    }

    /**
     * Create new advent of code exception for invalid day.
     */
    public static function invalidDay(int $year, int $day, int $maxDays): static
    {
        return new static("The specified day is invalid [$day] for year $year. The day must be >= 1 and <= $maxDays", 2);
    }
}
