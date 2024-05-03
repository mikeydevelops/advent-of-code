<?php

namespace Mike\AdventOfCode;

use Exception;
use Throwable;

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

    /**
     * Create new advent of code exception for failed fetching input.
     */
    public static function failedToFetchInput(int $year, int $day, Throwable $previous): static
    {
        return new static("Unable to fetch input for advent of code Year $year, Day $day.", 3, $previous);
    }

    /**
     * Create new advent of code exception for invalid solution part.
     */
    public static function invalidSolutionPartProvided(string $part, array $allowed = []): static
    {
        $parts = empty($parts) ? '' : (' Allowed parts: ' .implode(', ', $allowed));
        return new static("Invalid solution part [$part].$parts", 4);
    }

    /**
     * Create new advent of code exception for failed fetching information.
     */
    public static function failedToFetchInfo(int $year, int $day, Throwable $previous): static
    {
        return new static("Unable to fetch information for advent of code Year $year, Day $day.", 5, $previous);
    }

    /**
     * Create new advent of code exception for failed fetching page.
     */
    public static function failedToFetchPage(int $year, int $day, Throwable $previous): static
    {
        return new static("Unable to fetch page for advent of code Year $year, Day $day.", 6, $previous);
    }

    /**
     * Create new advent of code exception for expired session.
     */
    public static function promptExpiredSession(Throwable $previous = null): static
    {
        return new static('It seems your session has expired. Please provide new session key in your .env file.', 7, $previous);
    }
}
