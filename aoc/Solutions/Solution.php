<?php

namespace Mike\AdventOfCode\Solutions;

use Closure;
use Mike\AdventOfCode\AdventOfCodeDay;
use Mike\AdventOfCode\Console\Exceptions\ConsoleException;
use Mike\AdventOfCode\Console\Exceptions\TerminateExceptionBuilder;
use Mike\AdventOfCode\Console\IO;
use Mike\AdventOfCode\Console\OutputStyle;
use Mike\AdventOfCode\Console\Traits\InputOutput;
use Mike\AdventOfCode\Support\Profiler;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;
use Throwable;

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
    public function execute(): bool
    {
        if ($this->testing) {
            $this->io->info('Testing Mode.');

            if (! $this->exampleInput) {
                $this->io->error(sprintf(
                    'Solution <white>[%s]</> is in testing mode, but <white>$exampleInput</> is not set.',
                    get_class($this)
                ));

                return false;
            }
        }

        try {
            $this->io->line(sprintf('<question>%s</>', $this->day->getPart1Question()));
            $profiler = $this->measure(fn () => $this->part1Result = $this->part1());
            $this->io->line(sprintf('> <white>%s</>', $this->part1Result));
            $this->showProfilerResults($profiler, 'Part 1');

            $this->io->newLine();

            $profiler = $this->measure(fn () => $this->part2Result = $this->part2());
            $this->io->line(sprintf('<question>%s</>', $this->day->getPart2Question()));
            $this->io->line(sprintf('> <white>%s</>', $this->part2Result));
            $this->showProfilerResults($profiler, 'Part 2');
        } catch (Throwable $ex) {
            throw $ex;

            return false;
        }

        return true;
    }

    /**
     * Get the input of the day.
     */
    public function getInput(): mixed
    {
        return $this->transformInput($this->testing ? $this->exampleInput : $this->day->getInput());
    }

    /**
     * Measure given closure and show time and memory usage.
     */
    protected function measure(Closure $callback): Profiler
    {
        $profiler = new Profiler;

        $profiler->profile($callback);

        return $profiler;
    }

    /**
     * Print out the results from the given profiler.
     */
    protected function showProfilerResults(Profiler $profiler, string $label): static
    {
        $this->io->newLine();
        $this->io->info(sprintf(
            "$label took <white>%s</> and used <white>%s</> memory.",
            human_duration($profiler->getTimeTaken()),
            human_filesize($profiler->getMemoryUsage()),
        ));

        return $this;
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
