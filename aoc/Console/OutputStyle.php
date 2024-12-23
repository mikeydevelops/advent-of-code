<?php

namespace Mike\AdventOfCode\Console;

use Closure;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class OutputStyle extends SymfonyStyle
{
    /**
     * The output instance.
     *
     * @var \Mike\AdventOfCode\Console\ConsoleOutput
     */
    protected OutputInterface $output;

    /**
     * Create a new Console OutputStyle instance.
     */
    public function __construct(InputInterface $input, OutputInterface $output)
    {
        $this->output = $output;

        parent::__construct($input, $output);
    }

     /**
     * Run a callback with progress bar.
     *
     * @param  iterable|integer  $steps
     * @param  \Closure  $callback
     * @param  mixed  ...$args  additional arguments to pass to the callback.
     * @return \Symfony\Component\Console\Helper\ProgressBar
     */
    public function withProgress(iterable|int $steps, Closure $callback, ...$args)
    {
        $bar = $this->createProgressBar(is_iterable($steps) ? count($steps) : $steps);

        $args[] = $bar;

        $bar->start();

        if (is_iterable($steps)) {
            foreach ($steps as $value) {
                $callback($value, ...$args);

                $bar->advance();
            }
        } else {
            $callback(...$args);
        }

        $bar->finish();

        return $bar;
    }

    /**
     * Returns whether verbosity is quiet (-q).
     */
    public function isQuiet(): bool
    {
        return $this->output->isQuiet();
    }

    /**
     * Returns whether verbosity is verbose (-v).
     */
    public function isVerbose(): bool
    {
        return $this->output->isVerbose();
    }

    /**
     * Returns whether verbosity is very verbose (-vv).
     */
    public function isVeryVerbose(): bool
    {
        return $this->output->isVeryVerbose();
    }

    /**
     * Returns whether verbosity is debug (-vvv).
     */
    public function isDebug(): bool
    {
        return $this->output->isDebug();
    }

    /**
     * Get the underlying Symfony output implementation.
     */
    public function getOutput(): OutputInterface|ConsoleOutput
    {
        return $this->output;
    }
}
