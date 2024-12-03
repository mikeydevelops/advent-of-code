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
                                        {--o|open=env : Auto open generated day in configured editor. If empty, value from EDITOR_COMMAND environment variable will be used.}
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
     * Hook after all files have been generated.
     *
     * @param  array<string,string>  $classes  The keys are the full class name to the file, and the value is the path to the generated file.
     */
    protected function onGeneratorFinish(array $classes): void
    {
        $editor = $this->option('open');
        $count = count($classes);

        if ($editor !== 'env' && $count > 1) {
            $this->warn("--open is not supported when generating more than one solution.");

            return;
        } else if ($count != 1) {
            return;
        }

        $editor = $editor ?? $this->app->config->get('aoc.editor');

        if (! $editor) {
            return;
        }

        $file = array_pop($classes);

        $cmd = "$editor \"$file\"";

        if(class_exists('COM')) {
            $shell = new \COM('WScript.Shell');
            $shell->Run($cmd, 1, false);
        } else {
            exec('nohup ' . $cmd . ' 2>&1 &');
        }
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
