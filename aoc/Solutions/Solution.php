<?php

namespace Mike\AdventOfCode\Solutions;

use Closure;
use Mike\AdventOfCode\AdventOfCodeDay;
use Mike\AdventOfCode\AdventOfCodeException;
use Mike\AdventOfCode\Console\IO;
use Mike\AdventOfCode\Support\Profiler;

abstract class Solution
{
    /**
     * The related day for the solution.
     */
    protected AdventOfCodeDay $day;

    /**
     * The console input/output.
     */
    protected IO $io;

    /**
     * The example input to be used to test the solution.
     */
    protected ?string $exampleInput = null;

    /**
     * Indicating that the solution is running in testing mode.
     */
    protected bool $testing;

    /**
     * The prepared input for the solution.
     */
    protected mixed $input;

    /**
     * Te result of part1 method for the solution.
     */
    protected mixed $part1Result;

    /**
     * Te result of part2 method for the solution.
     */
    protected mixed $part2Result;

    /**
     * Create new solution instance.
     */
    public function __construct(AdventOfCodeDay $day)
    {
        $this->day = $day;
    }

    /**
     * Run the solution.
     */
    public function execute(bool $part1 = true, bool $part2 = true, bool $profile = true): bool
    {
        if ($this->testing) {
            $this->io->warn('Testing Mode.');

            if (! $this->exampleInput) {
                $this->io->error(sprintf(
                    'Solution <white>[%s]</> is in testing mode, but <white>$exampleInput</> is not set.',
                    get_class($this)
                ));

                return false;
            }
        }

        $this->io->info("Advent of Code {$this->day->getYear()}");
        $this->io->info("Day {$this->day->getDay()}: {$this->day->info('title')}");
        $this->io->newLine();

        $this->before();

        if ($part1) {
            $this->runPart('part1', 'Part One', $profile);
        } else {
            // because this part was not run, but part two may use the result from part one,
            // we will load the previously stored results.
            $this->part1Result = $this->day->info('part1.result');

            // maybe silently run part1 just in case it is needed for part2.
            // or find a way to dynamically detect when result from part1 is
            // needed and then run part1 solution silently showing a notice
            // to the user.
        }

        if ($part2) {
            if ($part1) {
                $this->io->newLine();
            }

            $this->day->part2IsUnlocked();

            $this->runPart('part2', 'Part Two', $profile);
        } else {
            $this->part2Result = $this->day->info('part2.result');
        }

        $this->after();

        return true;
    }

    /**
     * Run specified part of the day's solution.
     */
    public function runPart(string $part, string $label = null, bool $profile = true, bool $silent = false): void
    {
        $this->beforeEach($part);

        $part = strtolower($part);
        $parts = ['part1', 'part2'];

        if (! in_array($part, $parts)) {
            throw AdventOfCodeException::invalidSolutionPartProvided($part, $parts);
        }

        $label = $label ?? $part;

        !$silent && $this->io->line(sprintf('<question>%s: %s</>', $label, $this->day->info("$part.question", $label.' question has not been set')));

        $result = null;
        $profiler = null;

        if ($profile) {
            $profiler = $this->profile(function () use ($part, &$result) {
                $result = $this->{$part}();
            });
        } else {
            $result = $this->{$part}();
        }

        $this->{"{$part}Result"} = $result;

        ! $silent && $this->io->line(sprintf('> <white>%s</>', $result));

        $this->day->setInfo("$part.result", $result);

        if ($profile && !$silent) {
            $this->io->newLine();
            $this->io->info(sprintf(
                "$part took <white>%s</> and used <white>%s</> memory.",
                human_duration($time = $profiler->getTimeTaken()),
                human_filesize($memory = $profiler->getMemoryUsage()),
            ));

            $this->day->setInfo("$part.time", $time);
            $this->day->setInfo("$part.memory", $memory);
        }

        $this->afterEach($part, $result);
    }

    /**
     * Get the input of the day.
     */
    public function getInput(): mixed
    {
        return $this->transformInput($this->testing ? $this->exampleInput : $this->day->getInput());
    }

    /**
     * Profile given closure and show time and memory usage.
     */
    protected function profile(Closure $callback): Profiler
    {
        $profiler = new Profiler;

        $profiler->profile($callback);

        return $profiler;
    }
    /**
     * Process the input from the challenge.
     */
    public function transformInput(string $input): mixed
    {
        return $input;
    }

    /**
     * Run the first part of the challenge.
     */
    abstract function part1();

    /**
     * Run the second part of the challenge.
     */
    abstract function part2();

    /**
     * Hook before all parts are run.
     */
    protected function before(): void
    {
        //
    }

    /**
     * Hook after all parts are run.
     */
    protected function after(): void
    {
        //
    }

    /**
     * Hook before each part is run.
     */
    protected function beforeEach(string $part): void
    {
        //
    }

    /**
     * Hook after each part is run.
     */
    protected function afterEach(string $part, $result): void
    {
        //
    }

    /**
     * Make the solution in testing mode.
     */
    public function testing(bool $value = true): static
    {
        $this->testing = $value;

        return $this;
    }

    /**
     * Get the result of part1 method.
     */
    public function getPart1Result(): mixed
    {
        return $this->part1Result;
    }

    /**
     * Get the result of part2 method.
     */
    public function getPart2Result(): mixed
    {
        return $this->part2Result;
    }


    /**
     * Get the console instance.
     */
    public function getIO(): IO
    {
        return $this->io;
    }

    /**
     * Set the console input/output instance.
     */
    public function setIO(IO $io): static
    {
        $this->io = $io;

        return $this;
    }
}
