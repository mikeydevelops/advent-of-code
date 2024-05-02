<?php

namespace Mike\AdventOfCode\Console;

use DI\Container;
use Mike\AdventOfCode\Console\Exceptions\TerminateException;
use Mike\AdventOfCode\Console\Exceptions\TerminateExceptionBuilder;
use Mike\AdventOfCode\Console\Parser;
use Mike\AdventOfCode\Console\OutputStyle;
use Mike\AdventOfCode\Console\Traits\InputOutput;
use Symfony\Component\Console\Command\Command as SymfonyCommand;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Console\Output\OutputInterface;

abstract class Command extends SymfonyCommand
{
    use InputOutput;

    /**
     * The application instance.
     */
    protected Application $app;

    /**
     * The application container instance.
     */
    protected Container $container;

    /**
     * The name and signature of the console command.
     */
    protected string $signature;

    /**
     * The console command name.
     *
     * @var string
     */
    protected string $name;

    /**
     * The console command description.
     */
    protected ?string $description = null;

    /**
     * The console command help text.
     */
    protected ?string $help = null;

    /**
     * Create new command instance.
     */
    public function __construct()
    {
        if (isset($this->signature)) {
            $this->configureUsingLaravelDefinition();
        } else {
            parent::__construct($this->name);
        }

        $this->setDescription((string) $this->description);
        $this->setHelp((string) $this->help);
    }

    /**
     * Configure the console command using a fluent definition.
     */
    protected function configureUsingLaravelDefinition(): void
    {
        [$name, $arguments, $options] = Parser::parse($this->signature);

        parent::__construct($this->name = $name);

        // After parsing the signature we will spin through the arguments and options
        // and set them on this command. These will already be changed into proper
        // instances of these "InputArgument" and "InputOption" Symfony classes.
        $this->getDefinition()->addArguments($arguments);
        $this->getDefinition()->addOptions($options);
    }

    /**
     * Run the console command.
     */
    #[\Override]
    public function run(InputInterface $input, OutputInterface $output): int
    {
        $this->output = $output instanceof OutputStyle ? $output : $this->container->make(
            OutputStyle::class, ['input' => $input, 'output' => $output]
        );

        try {
            return parent::run(
                $this->input = $input, $this->output
            );
        } catch (TerminateException $ex) {
            if (!is_null($termOut = $ex->getOutput())) {
                /** @var \Symfony\Component\Console\Output\BufferedOutput $buffer */
                $buffer = $termOut->getOutput();

                $this->output->write($buffer->fetch());
            } else if (! empty($msg = $ex->getMessage())) {
                $this->error($msg);
            }

            return $ex->getExitCode();
        }
    }

    /**
     * Execute the console command.
     */
    #[\Override]
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $method = method_exists($this, 'handle') ? 'handle' : '__invoke';

        return (int) $this->container->call([$this, $method]);
    }

    /**
     * Terminate the command with given exit code and message.
     */
    public function terminate(int|callable $exitCode = 1, string $message = ''): void
    {
        /** @var int $verbosity */
        $verbosity = $this->output->getVerbosity();
        $buffer = new BufferedOutput($verbosity, $this->output->isDecorated(), clone $this->output->getFormatter());

        $builder = new TerminateExceptionBuilder(message: $message);
        $builder->setOutput(new OutputStyle(new ArrayInput([]), $buffer));

        if (is_callable($exitCode)) {
            $this->container->call($exitCode, compact('builder'));
        } else {
            $builder->setExitCode($exitCode);
        }

        $builder->throw();
    }

    /**
     * Get the application instance.
     */
    public function getApp(): Application
    {
        return $this->app;
    }

    /**
     * Set the application instance.
     */
    public function setApp(Application $ap): static
    {
        $this->app = $ap;

        return $this;
    }

    /**
     * Get the application container instance.
     */
    public function getContainer(): Container
    {
        return $this->container;
    }

    /**
     * Set the application container instance.
     */
    public function setContainer(Container $container): static
    {
        $this->container = $container;

        return $this;
    }
}
