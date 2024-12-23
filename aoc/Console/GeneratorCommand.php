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

        $classes = array_wrap($this->getFullClassNamespace());
        $fails = [];
        $generated = [];

        foreach ($classes as $class) {

            $path = $this->namespaceToPath($class);
            $relativePath = '.'.str_replace($this->app->basePath(), '', $path);

            $className = basename($class);
            $namespace = dirname($class);

            if (!$this->shouldOverwrite() && file_exists($path)) {
                $this->error("$this->generatorType <white>[$className]</> already exits in <white>[$namespace]</>.");

                $fails[] = $class;

                continue;
            }

            $this->ensureClassDirectoryExists(dirname($path));

            file_put_contents($path, $this->makeClass($class));

            $generated[$class] = $path;

            $this->success("$this->generatorType <white>[$relativePath]</> created successfully.");
        }

        $this->onGeneratorFinish($generated);

        return count($fails) ? Command::FAILURE : Command::SUCCESS;
    }

    /**
     * Prepare the generator.
     */
    protected function prepare(): void
    {
        //
    }

    /**
     * Hook after all files have been generated.
     *
     * @param  array<string,string>  $classes  The keys are the full class name to the file, and the value is the path to the generated file.
     */
    protected function onGeneratorFinish(array $classes): void
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
    protected function getFullClassNamespace(): string|array
    {
        $name = $this->getClassName();

        if (! is_array($name)) {
            return $this->makeFullClassNamespace($name);
        }

        return array_map([$this, 'makeFullClassNamespace'], $name);
    }

    /**
     * Construct the full namespace for provided class name.
     */
    protected function makeFullClassNamespace(string $name): string
    {
        $name = trim($name, '\\/');
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
     */
    protected function getClassName(): string|array
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
