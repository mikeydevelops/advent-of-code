<?php

namespace Mike\AdventOfCode\Console\Commands;

use Mike\AdventOfCode\Console\Command;
use Mike\AdventOfCode\Console\Traits\SolutionYearAndDay;

class SolutionCommand extends Command
{
    use SolutionYearAndDay;

    /**
     * The name and signature of the console command.
     */
    protected string $signature = 'solution {--y|year= : The event year.} {--d|day= : The event day.}';

    /**
     * The console command description.
     */
    protected ?string $description = 'Run the solution for given event year and day.';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->ensureYearAndDayAvailable();

        dump($this->year, $this->day);

        return Command::SUCCESS;
    }
}
