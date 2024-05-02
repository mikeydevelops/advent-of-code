<?php

namespace Mike\AdventOfCode\Console;

use DI\Container;
use Mike\AdventOfCode\Console\Command;
use Symfony\Component\Console\Command\Command as SymfonyCommand;
use ReflectionClass;
use RuntimeException;
use Symfony\Component\Console\Application as SymfonyApplication;
use Symfony\Component\Finder\Finder;

class Application extends SymfonyApplication
{
    /**
     * The container used for dependency injection.
     */
    protected Container $container;

    /**
     * Indicates if the Closure commands have been loaded.
     */
    protected bool $commandsLoaded = false;

    /**
     * The application base path.
     */
    protected string $basePath;

    /**
     * The application namespace.
     */
    protected ?string $namespace = null;

    /**
     * Create new instance of console application.
     */
    public function __construct(string $basePath, string $version)
    {
        parent::__construct('Advent of Code by Mike', $version);

        $this->container = new Container;
        $this->basePath = $basePath;
        $this->setAutoExit(false);
        $this->setCatchExceptions(false);

        $this->bootstrap();
    }

    /**
     * Bootstrap the console application.
     */
    public function bootstrap(): void
    {
        if (! $this->commandsLoaded) {
            $this->commands();

            $this->commandsLoaded = true;
        }
    }

    /**
     * Register a singleton in the application container.
     *
     * @template T
     * @param  string  $abstract
     * @param  T|mixed  $concrete
     * @return T
     */
    public function singleton(string $abstract, $concrete)
    {
        $this->container->set($abstract, $concrete);

        return $concrete;
    }

    /**
     * Get a binding from the application container.
     *
     * @param  string  $abstract
     * @return mixed
     */
    public function make(string $abstract)
    {
        return $this->container->make($abstract);
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__ . '/Commands');
    }

    /**
     * Register all of the commands in the given directory.
     */
    protected function load(string|array $paths)
    {
        $paths = array_unique(array_wrap($paths));

        $paths = array_filter($paths, function ($path) {
            return is_dir($path);
        });

        if (empty($paths)) {
            return;
        }

        $namespace = $this->getNamespace();

        foreach (Finder::create()->in($paths)->files() as $file) {
            $command = $this->getCommandFromPath($file->getRealPath(), $namespace);

            if (is_subclass_of($command, Command::class) && ! (new ReflectionClass($command))->isAbstract()) {
                $this->resolve($command);
            }
        }
    }

    /**
     * Add a command to the console.
     */
    #[\Override]
    public function add(SymfonyCommand $command): ?SymfonyCommand
    {
        if ($command instanceof Command) {
            $command->setApp($this);
            $command->setContainer($this->container);
        }

        return parent::add($command);
    }

    /**
     * Resolve the command from the container, then add it to the app.
     */
    public function resolve(string|Command $command): SymfonyCommand
    {
        if ($command instanceof Command) {
            return $this->add($command);
        }

        return $this->add($this->container->make($command));
    }

    /**
     * Get the path to the application source code.
     */
    public function path(string ...$append): string
    {
        return $this->basePath('aoc', ...$append);
    }

    /**
     * Get the base path of the project. Optionally append additional path.
     */
    public function basePath(string ...$append): string
    {
        return join_path($this->basePath, ...$append);
    }

    /**
     * Get the application namespace.
     *
     * @return string
     *
     * @throws \RuntimeException
     */
    public function getNamespace()
    {
        if (! is_null($this->namespace)) {
            return $this->namespace;
        }

        $composer = json_decode(file_get_contents($this->basePath('composer.json')), true);
        $psr4 = $composer['autoload']['psr-4'] ?? [];

        foreach ($psr4 as $namespace => $path) {
            foreach ((array) $path as $pathChoice) {
                if (realpath($this->path()) === realpath($this->basePath($pathChoice))) {
                    return $this->namespace = $namespace;
                }
            }
        }

        throw new RuntimeException('Unable to detect application namespace.');
    }

    /**
     * Extract the command class name from the given file path.
     */
    protected function getCommandFromPath(string $path, string $namespace): string
    {
        $classPath = str_replace([$this->path().DIRECTORY_SEPARATOR, '.php', '/'], ['', '', '\\'], $path);

        return $namespace.$classPath;
    }

}
