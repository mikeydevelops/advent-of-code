<?php

namespace Mike\AdventOfCode\Console\Commands;

use Mike\AdventOfCode\Console\Command;
use Psy\Shell;

class ShellCommand extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected string $signature = 'shell';

    /**
     * The console command description.
     */
    protected ?string $description = 'Interact with the application using interactive REPL shell.';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $shell = new Shell();

        return $shell->run();
    }
}
