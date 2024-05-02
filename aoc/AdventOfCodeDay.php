<?php

namespace Mike\AdventOfCode;

class AdventOfCodeDay
{
    /**
     * The year of the solution.
     */
    protected int $year;

    /**
     * The day of the solution.
     */
    protected int $day;

    /**
     * The day's input.
     */
    protected ?string $input = null;

    /**
     * The question for part one of this day's challenge.
     */
    protected ?string $part1Question = null;

    /**
     * The question for part two of this day's challenge.
     */
    protected ?string $part2Question = null;

    /**
     * Create a new Advent of Code Day instance.
     */
    public function __construct(int $year, int $day)
    {
        $this->year = $year;
        $this->day = $day;
    }

    /**
     * Get the day's input.
     */
    public function getInput(): string
    {
        if ($this->input) {
            return $this->input;
        }

        if ($this->inputIsCached()) {
            return $this->input = $this->getCachedInput();
        }

        return $this->input = $this->fetchInput();
    }

    /**
     * Get the path of the day's input.
     */
    public function inputPath(): string
    {
        return base_path('storage', 'cache', 'inputs', strval($this->year), sprintf('day-%02d', $this->day));
    }

    /**
     * Check to see if the day's input has been downloaded and cached.
     */
    public function inputIsCached(): bool
    {
        return file_exists($this->inputPath());
    }

    /**
     * Load the day's input from storage.
     */
    public function getCachedInput(): string
    {
        return file_get_contents($this->inputPath());
    }

    /**
     * Get the question for this day's challenge for part one.
     */
    public function getPart1Question(): string
    {
        return $this->part1Question ?? 'Part 1 question has not been set.';
    }

    /**
     * Get the question for this day's challenge for part one.
     */
    public function getPart2Question(): string
    {
        return $this->part2Question ?? 'Part 2 question has not been set.';
    }

    /**
     * Get the class that will run the challenges.
     */
    public function getSolutionClass(): string
    {
        return "Mike\\AdventOfCode\\Solutions\\Year$this->year\\Day" . sprintf('%02d', $this->day);
    }

    /**
     * Get the solution year.
     */
    public function getYear(): int
    {
        return $this->year;
    }

    /**
     * Set the solution year.
     */
    public function setYear(int $year): static
    {
        $this->year = $year;

        return $this;
    }

    /**
     * Get the solution day.
     */
    public function getDay(): int
    {
        return $this->day;
    }

    /**
     * Set the solution day.
     */
    public function setDay(int $day): static
    {
        $this->day = $day;

        return $this;
    }
}
