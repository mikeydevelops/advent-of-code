<?php

namespace Mike\AdventOfCode\Console;

use ReflectionClass;
use RuntimeException;

use DI\Container;
use Dotenv\Dotenv;
use GuzzleHttp\ClientInterface;
use Mike\AdventOfCode\Console\Command;
use Mike\AdventOfCode\Console\Exceptions\ConsoleException;
use Mike\AdventOfCode\Providers\Provider;
use Mike\AdventOfCode\Support\Config;
use Mike\AdventOfCode\Support\Env;
use Symfony\Component\Console\Command\Command as SymfonyCommand;
use Symfony\Component\Console\Application as SymfonyApplication;
use Mike\AdventOfCode\Console\OutputFormatter;
use Mike\AdventOfCode\Console\OutputFormatterStyle;
use Symfony\Component\Console\Input\ArgvInput;
use Mike\AdventOfCode\Console\ConsoleOutput;
use Symfony\Component\Finder\Finder;

/**
 * @property  \Mike\AdventOfCode\Support\Config  $config The application configuration.
 *
 * @property  \Symfony\Component\Console\Input\ArgvInput  $input  The application input.
 * @property  \Mike\AdventOfCode\Console\OutputStyle  $output  The application output.
 * @property  \Mike\AdventOfCode\Console\ConsoleOutput  $console  The application console.
 * @property  \Mike\AdventOfCode\Console\OutputFormatter  $formatter  The application output formatter.
 * @property  \Mike\AdventOfCode\Console\IO  $io  Easier way to interact with console.
 * @property  \Mike\AdventOfCode\Console\Terminal  $terminal  The terminal instance.
 */
class Application extends SymfonyApplication
{
    /**
     * The container used for dependency injection.
     */
    protected Container $container;

    /**
     * Indicates if the providers have been loaded.
     */
    protected bool $providersLoaded = false;

    /**
     * Indicates if the commands have been loaded.
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
     * Array of registered providers
     *
     * @var \Mike\AdventOfCode\Providers\Provider[]
     */
    protected array $providers = [];

    /**
     * The current instance.
     *
     * @var \Mike\AdventOfCode\Console\Application
     */
    protected static ?Application $instance = null;

    /**
     * Cached string for the user agent used throughout the application.
     */
    protected ?string $userAgent = null;

    /**
     * The cached composer file.
     */
    protected array $composer = [];

    /**
     * Create new instance of console application.
     */
    public function __construct(string $basePath, string $version)
    {
        parent::__construct('Advent of Code Solutions by Mike', $version);

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
        static::$instance = $this;
        $this->app = $this;
        $this->singleton(static::class, $this);

        $this->bootstrapIO();

        $this->loadEnvironment();

        $this->config = new Config(require $this->basePath('includes', 'config.php'));
        $this->singleton(Config::class, $this->config);

        $this->loadProviders();

        $this->loadCommands();
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

            'em' => new OutputFormatterStyle(options: ['underscore'], inherit: ['foreground', 'background']),
            'code' => new OutputFormatterStyle(options: ['bold'], inherit: ['foreground', 'background']),
        ]);

        $this->input = new ArgvInput();
        $this->console = new ConsoleOutput(formatter: $this->formatter);
        // 75 characters appears to be the maximum on adventofcode.com
        // $this->console->setMaxLineWith(75);
        $this->output = new OutputStyle($this->input, $this->console);

        $this->configureIO($this->input, $this->output);

        $this->io = new IO($this->input, $this->output);

        $this->terminal = new Terminal();
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
     * @template T
     * @param  class-string<T>  $abstract
     * @param  array  $parameters
     * @return T|mixed
     */
    public function make(string $abstract, array $parameters = [])
    {
        return $this->container->make($abstract, $parameters);
    }

    /**
     * Register the providers for the application.
     *
     * @return void
     */
    protected function loadProviders(): bool
    {
        if ($this->providersLoaded) {
            return false;
        }

        foreach ($this->config->get('providers') as $provider) {
            if (is_subclass_of($provider, Provider::class) && ! (new ReflectionClass($provider))->isAbstract()) {
                $this->registerProvider($provider);
            }
        }

        return $this->providersLoaded = true;

        // The following is for auto loading providers from directory.
        // but it is disabled because it does not allow for changing
        // the order of providers.

        // $paths = $this->path('Providers');

        // $paths = array_unique(array_wrap($paths));

        // $paths = array_filter($paths, function ($path) {
        //     return is_dir($path);
        // });

        // if (empty($paths)) {
        //     return false;
        // }

        // $namespace = $this->getNamespace();

        // foreach (Finder::create()->in($paths)->files() as $file) {
        //     $provider = $this->getClassFromPath($file->getRealPath(), $namespace);

        //     if (is_subclass_of($provider, Provider::class) && ! (new ReflectionClass($provider))->isAbstract()) {
        //         $this->registerProvider($provider);
        //     }
        // }

        // return $this->providersLoaded = true;
    }

    /**
     * Resolve the provider from the container, then register in the app.
     */
    public function registerProvider(string|Provider $provider): Provider
    {
        if (! $provider instanceof Provider) {
            $provider = $this->container->make($provider);
        }

        $providerClass = get_class($provider);

        if (! isset($this->providers[$providerClass])) {
            $provider->setApp($this);

            $provider->register();

            $this->providers[$providerClass] = $provider;
        }

        return $provider;
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function loadCommands(): bool
    {
        if ($this->commandsLoaded) {
            return false;
        }

        $paths = $this->path('Console', 'Commands');

        $paths = array_unique(array_wrap($paths));

        $paths = array_filter($paths, function ($path) {
            return is_dir($path);
        });

        if (empty($paths)) {
            return false;
        }

        $namespace = $this->getNamespace();

        foreach (Finder::create()->in($paths)->files() as $file) {
            $command = $this->getClassFromPath($file->getRealPath(), $namespace);

            if (is_subclass_of($command, Command::class) && ! (new ReflectionClass($command))->isAbstract()) {
                $this->addCommand($command);
            }
        }

        return $this->commandsLoaded = true;
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
    public function addCommand(string|Command $command): SymfonyCommand
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

        $psr4 = $this->composer('autoload.psr-4', []);

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
    protected function getClassFromPath(string $path, string $namespace): string
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

    /**
     * Get the static application instance.
     */
    public static function instance(): static
    {
        if (! isset(static::$instance)) {
            throw new ConsoleException('Application static intance has not been set.');
        }

        return static::$instance;
    }

    /**
     * Get the application user agent.
     */
    public function getUserAgent(): string
    {
        if ($this->userAgent) {
            return $this->userAgent;
        }

        $appName = str_replace(' ', '-', $this->getName());
        $appVersion = $this->getVersion();
        $source = $this->composer('support.source', 'https://github.com/mikeydevelops/advent-of-code');
        $email = $this->composer('support.email', 'mike@mikeydevs.com');

        $guzzleVersion = ClientInterface::MAJOR_VERSION;
        $phpVersion = phpversion();

        $platformName = php_uname('s');
        $platformRelease = php_uname('r');
        $platformVersion = php_uname('v');
        $machineType = php_uname('m');

        $platform = "($platformName $platformRelease; $platformVersion; $machineType)";

        return $this->userAgent = "$appName/$appVersion ($source; $email) GuzzleHttp/$guzzleVersion PHP/$phpVersion $platform";
    }

    /**
     * Get an item from the app composer.json.
     */
    public function composer(string $key, $default = null): mixed
    {
        if (empty($this->composer)) {
            $this->composer = json_decode(file_get_contents($this->basePath('composer.json')), true);
        }

        return array_get_dot($this->composer, $key, $default);
    }
}
