<?php

namespace Mike\AdventOfCode\Console\Commands;

use Mike\AdventOfCode\AdventOfCode;
use Mike\AdventOfCode\AdventOfCodeDay;
use Mike\AdventOfCode\Console\Command;
use Mike\AdventOfCode\Console\IO;
use Mike\AdventOfCode\Console\Traits\SolutionYearAndDay;
use Mike\AdventOfCode\Solutions\Solution;

class SolutionCommand extends Command
{
    use SolutionYearAndDay;

    /**
     * The name and signature of the console command.
     */
    protected string $signature = 'solution
                                        {--y|year= : The event year.}
                                        {--d|day= : The event day.}
                                        {--t|test : Run in test mode, will use example input instead of challenge.}
                                        {--1|part1 : Run only part 1 of the solution.}
                                        {--2|part2 : Run only part 2 of the solution.}
                                        {--p|profile : Show time elapsed and memory used on each part of the solution.}';

    /**
     * The console command description.
     */
    protected ?string $description = 'Run the solution for given event year and day.';

    /**
     * The advent of code client.
     */
    protected AdventOfCode $aoc;

    /**
     * Create a new solution command instance.
     */
    public function __construct(AdventOfCode $aoc)
    {
        parent::__construct();

        $this->aoc = $aoc;
    }

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->ensureYearAndDayAvailable();

        $day = $this->aoc->getDay($this->year, $this->day);

        $solution = $this->makeSolution($day)
            ->setIO(new IO($this->getInput(), $this->getOutput()))
            ->testing($this->option('test'));

        $part1 = $this->option('part1');
        $part2 = $this->option('part2');

        if (! $part1 && ! $part2) {
            $part1 = true;
            $part2 = false;
        }

        $profile = $this->option('profile');

        return $solution->execute($part1, $part2, $profile) ? Command::SUCCESS : Command::FAILURE;
    }

    /**
     * Initialize a solution instance for givent advent of code day.
     */
    protected function makeSolution(AdventOfCodeDay $day): Solution
    {
        $class = $day->getSolutionClass();

        return $this->app->make($class, compact('day'));
    }
}
