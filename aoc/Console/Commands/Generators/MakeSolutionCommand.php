<?php

namespace Mike\AdventOfCode\Console\Commands\Generators;

use Mike\AdventOfCode\Console\GeneratorCommand;
use Mike\AdventOfCode\Console\Traits\SolutionYearAndDay;

class MakeSolutionCommand extends GeneratorCommand
{
    use SolutionYearAndDay;

    /**
     * The name and signature of the console command.
     */
    protected string $signature = 'make:solution
                                        {--y|year= : The event year.}
                                        {--d|day= : The event day.}
                                        {--f|force : Create the solution class if it already exists, overwrite it.}';

    /**
     * The console command description.
     */
    protected ?string $description = 'Generate solution file for specified year and day.';

    /**
     * Get the type of the generated class.
     */
    protected string $generatorType = 'Solution';

    /**
     * Prepare the generator.
     */
    protected function prepare(): void
    {
        $this->ensureRemainingYearAndDayAvailable();
    }

    /**
     * Get the path to the stub for the new class.
     */
    protected function getStubPath(): string
    {
        return $this->app->basePath('stubs/solution.stub');
    }

    /**
     * Get the namespace for the class.
     */
    protected function getClassNamespace(string $baseNamespace): string
    {
        return $baseNamespace.'\Solutions\Year' . $this->year;
    }

    /**
     * Get the user provided name for the class.
     */
    protected function getClassName(): string|array
    {
        if (! is_array($this->day)) {
            return 'Day'.sprintf('%02d', $this->day);
        }

        return array_map(fn(int $day) => ('Day'.sprintf('%02d', $day)), $this->day);
    }

    /**
     * Get additional data to replace when the template is loaded.
     */
    protected function getClassData(array $data): array
    {
        return [];
    }
}
