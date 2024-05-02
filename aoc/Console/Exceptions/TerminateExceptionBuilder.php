<?php

namespace Mike\AdventOfCode\Console\Exceptions;

use Mike\AdventOfCode\Console\Exceptions\TerminateException;

/**
 * @mixin \Mike\AdventOfCode\Console\Exceptions\TerminateException
 */
class TerminateExceptionBuilder
{
    /**
     * The underlying exception.
     */
    protected TerminateException $ex;

    /**
     * Create new Terminate Exception Builder
     */
    public function __construct(int $exitCode = 1, string $message = '')
    {
        $this->ex = new TerminateException($exitCode, $message);
    }

    /**
     * Throw the exception.
     */
    public function throw(): void
    {
        throw $this->ex;
    }

    /**
     * Get the underlying exception instance.
     *
     * @return \Mike\AdventOfCode\Console\Exceptions\TerminateException
     */
    public function getException(): TerminateException
    {
        return $this->ex;
    }

    /**
     * Proxy any call to any method to the exception.
     */
    public function __call(string $method, array $arguments): mixed
    {
        return call_user_func_array([$this->ex, $method], $arguments);
    }
}
