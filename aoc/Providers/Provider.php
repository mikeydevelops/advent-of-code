<?php

namespace Mike\AdventOfCode\Providers;

use Mike\AdventOfCode\Console\Application;

abstract class Provider
{
    /**
     * The main application.
     */
    protected Application $app;

    /**
     * Change the application instance.
     */
    public function setApp(Application $app): static
    {
        $this->app = $app;

        return $this;
    }

    /**
     * Register the provider.
     */
    abstract public function register(): void;
}
