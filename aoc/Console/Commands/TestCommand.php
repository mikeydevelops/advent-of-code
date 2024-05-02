<?php

namespace Mike\AdventOfCode\Console\Commands;

use Mike\AdventOfCode\Console\Command;

class TestCommand extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected string $signature = 'test';

    /**
     * The console command description.
     */
    protected ?string $description = 'Command description';

    /**
     * Get the type of the generated class.
     */
    protected string $generatorType;

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        //

        return Command::FAILURE;
    }
}
