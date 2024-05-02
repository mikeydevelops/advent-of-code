<?php

namespace Mike\AdventOfCode\Console\Commands\Generators;

use Mike\AdventOfCode\Console\GeneratorCommand;

class MakeCommandCommand extends GeneratorCommand
{
    /**
     * The name and signature of the console command.
     */
    protected string $signature = 'make:command
                                        {name : The name of the command.}
                                        {--c|command= : The name of the command that will be used to run the class.}
                                        {--g|generator= : Make the command a generator. And use input as stub name.}
                                        {--f|force : Create the command class event if it already exists, overwrite it.}';

    /**
     * The console command description.
     */
    protected ?string $description = 'Create a new command.';

    /**
     * Get the type of the generated class.
     */
    protected string $generatorType = 'Command';

    /**
     * Get the path to the stub for the new class.
     */
    protected function getStubPath(): string
    {
        if ($this->option('generator')) {
            return $this->app->basePath('stubs/command.generator.stub');
        }

        return $this->app->basePath('stubs/command.stub');
    }

    /**
     * Get the namespace for the class.
     */
    protected function getClassNamespace(string $baseNamespace): string
    {
        $namespace = $baseNamespace.'\Console\Commands';

        if ($this->option('generator')) {
            $namespace .= '\Generators';
        }

        return $namespace;
    }

    /**
     * Get additional data to replace when the template is loaded.
     */
    protected function getClassData(array $data): array
    {
        $command = $this->option('command') ?? str_kebab(str_replace('Command', '', $data['class']));

        $data = compact('command');

        if ($generator = $this->option('generator')) {
            $data['generator'] = $generator;
        }

        return $data;
    }
}
