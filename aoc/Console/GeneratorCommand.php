<?php

namespace Mike\AdventOfCode\Console;

use Mike\AdventOfCode\Console\Command;

abstract class GeneratorCommand extends Command
{
    /**
     * Get the type of the generated class.
     */
    protected string $generatorType = 'Class';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->prepare();

        $fullClass = $this->getFullClassNamespace();
        $path = $this->namespaceToPath($fullClass);
        $relativePath = '.'.str_replace($this->app->basePath(), '', $path);

        $className = basename($fullClass);
        $namespace = dirname($fullClass);

        if (!$this->shouldOverwrite() && file_exists($path)) {
            $this->error("$this->generatorType <white>[$className]</> already exits in <white>[$namespace]</>.");

            return Command::FAILURE;
        }

        $this->ensureClassDirectoryExists(dirname($path));

        file_put_contents($path, $this->makeClass($fullClass));

        $this->success("$this->generatorType <white>[$relativePath]</> created successfully.");

        return Command::SUCCESS;
    }

    /**
     * Prepare the generator.
     */
    protected function prepare(): void
    {
        //
    }

    /**
     * Convert given full namespace of a class, to absolute file path.
     */
    protected function namespaceToPath(string $fullNamespace): string
    {
        $baseNamespace = $this->namespace();

        $relative = str_replace($baseNamespace, '', $fullNamespace);
        $relative = str_replace(['\\', '/'], DIRECTORY_SEPARATOR, $relative) . '.php';

        return $this->app->path($relative);
    }

    /**
     * Get the path to the stub for the new class.
     */
    abstract protected function getStubPath(): string;

    /**
     * Get the full namespace for the provided class name.
     */
    protected function getFullClassNamespace(): string
    {
        $name = trim($this->getClassName(), '\\/');
        $name = str_replace('/', '\\', $name);

        return $this->getClassNamespace(rtrim($this->namespace(), '\\')).'\\'.$name;
    }

    /**
     * Generate the class.
     */
    protected function makeClass(string $fullNamespace): string
    {
        $template = file_get_contents($this->getStubPath());

        $namespace = rtrim($this->namespace(), '\\');
        $classNamespace = dirname($fullNamespace);
        $class = basename($fullNamespace);

        $data = compact('classNamespace', 'class', 'namespace');
        $data = array_merge($data, $this->getClassData($data));
        $search = array_map(fn(string $key) => '@'.$key, array_keys($data));

        return str_replace($search, $data, $template);
    }

    /**
     * Get the namespace for the class.
     */
    protected function getClassNamespace(string $baseNamespace): string
    {
        return $baseNamespace;
    }

    /**
     * Get additional data to replace when the template is loaded.
     */
    protected function getClassData(array $data): array
    {
        return [];
    }

    /**
     * Get the user provided name for the class.
     *
     * @return string
     */
    protected function getClassName(): string
    {
        return trim($this->argument('name'));
    }

    /**
     * Check to see if the class should be overwritten.
     * That is when a force option is available and used.
     */
    protected function shouldOverwrite(): bool
    {
        return $this->hasOption('force') && $this->option('force');
    }

    /**
     * Get the base namespace for the project.
     */
    protected function namespace(): string
    {
        return $this->app->getNamespace();
    }

    /**
     * Make sure the directory where the class will be saved, exists.
     */
    protected function ensureClassDirectoryExists(string $directory): static
    {
        if (! is_dir($directory)) {
            mkdir($directory, 0777, true);
        }

        return $this;
    }
}
