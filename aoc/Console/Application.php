<?php

namespace Mike\AdventOfCode\Console;

use ReflectionClass;
use RuntimeException;

use DI\Container;
use Dotenv\Dotenv;
use Mike\AdventOfCode\Console\Command;
use Mike\AdventOfCode\Support\Config;
use Mike\AdventOfCode\Support\Env;
use Symfony\Component\Console\Command\Command as SymfonyCommand;
use Symfony\Component\Console\Application as SymfonyApplication;
use Symfony\Component\Console\Formatter\OutputFormatter;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Finder\Finder;

/**
 * @property  \Mike\AdventOfCode\Support\Config  $config The application configuration.
 *
 * @property  \Symfony\Component\Console\Input\ArgvInput  $input  The application input.
 * @property  \Mike\AdventOfCode\Console\OutputStyle  $output  The application output.
 * @property  \Symfony\Component\Console\Output\ConsoleOutput  $console  The application console.
 * @property  \Symfony\Component\Console\Formatter\OutputFormatter  $formatter  The application output formatter.
 * @property  \Mike\AdventOfCode\Console\IO  $io  Easier way to interact with console.
 */
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
        $this->bootstrapIO();

        $this->loadEnvironment();

        $this->config = new Config(require $this->basePath('includes', 'config.php'));

        if (! $this->commandsLoaded) {
            $this->commands();

            $this->commandsLoaded = true;
        }
    }

    /**
     * Load environment variables from .env file in base path.
     */
    protected function loadEnvironment(): bool
    {
        if (! file_exists($this->basePath('.env'))) {
            return false;
        }

        $dotenv = Dotenv::create(
            Env::getRepository(),
            $this->basePath(),
            '.env'
        );

        $dotenv->safeLoad();

        return true;
    }

    /**
     * Bootstrap the console application input and output.
     */
    protected function bootstrapIO(): void
    {
        $this->formatter = new OutputFormatter(styles: [
            'info' => new OutputFormatterStyle('cyan'),
            'success' => new OutputFormatterStyle('green'),
            'warning' => new OutputFormatterStyle('yellow'),
            'emergency' => new OutputFormatterStyle('white', 'red'),
            'error' => new OutputFormatterStyle('red'),
            'danger' => new OutputFormatterStyle('red'),
            'comment' => new OutputFormatterStyle('gray'),
            'question' => new OutputFormatterStyle('magenta'),
            'white' => new OutputFormatterStyle('white'),
            'black' => new OutputFormatterStyle('black'),
        ]);

        $this->input = new ArgvInput();
        $this->console = new ConsoleOutput(formatter: $this->formatter);
        $this->output = new OutputStyle($this->input, $this->console);

        $this->io = new IO($this->input, $this->output);
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

    /**
     * Try to resolve the dynamic properties from the container.
     */
    public function __get(string $key)
    {
        return $this->make($key);
    }

    /**
     * Set dynamic properties in the container.
     */
    public function __set(string $key, $value)
    {
        $this->container->set($key, $value);
    }
}
