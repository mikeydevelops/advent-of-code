<?php

namespace Mike\AdventOfCode\Console\Exceptions;

use Mike\AdventOfCode\Console\Exceptions\ConsoleException;
use Mike\AdventOfCode\Console\Traits\InputOutput;

class TerminateException extends ConsoleException
{
    use InputOutput;

    /**
     * The code that will be returned to the terminal after command termination.
     */
    protected int $exitCode = 1;

    /**
     * Create new instance of terminate exception.
     */
    public function __construct(int $exitCode = 1, string $message = '')
    {
        parent::__construct($message);

        $this->exitCode = $exitCode;
    }

    /**
     * Get the exit code.
     */
    public function getExitCode(): int
    {
        return $this->exitCode;
    }

    /**
     * Set a new exit code.
     */
    public function setExitCode(int $exitCode): static
    {
        $this->exitCode = $exitCode;

        return $this;
    }
}
