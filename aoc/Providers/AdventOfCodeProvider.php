<?php

namespace Mike\AdventOfCode\Providers;

use Mike\AdventOfCode\AdventOfCode;
use Mike\AdventOfCode\Providers\Provider;

class AdventOfCodeProvider extends Provider
{
    /**
     * Register new services in application container.
     */
    public function register(): void
    {
        if (empty($key = $this->app->config->get('aoc.session'))) {
            AdventOfCode::promptEmptySession($this->app->io);

            exit(1);

            return;
        }

        if (! $this->validateSessionKey($key)) {
            AdventOfCode::promptInvalidSessionKey($this->app->io);

            exit(1);

            return;
        }

        $this->app->aoc = $client = $this->createClient();
        $this->app->singleton(AdventOfCode::class, $client);
    }

    /**
     * Create new instance of GuzzleHttp Client configured for use
     * for adventofcode.com
     *
     * @return \Mike\AdventOfCode\AdventOfCode
     */
    protected function createClient(): AdventOfCode
    {
        return new AdventOfCode(
            $this->app->config->get('aoc.session'),
            $this->app->getName(),
            $this->app->getVersion(),
        );
    }

    /**
     * Validate the configured session key.
     */
    protected function validateSessionKey(string $key): bool
    {
        return !!preg_match('/^[a-f0-9]{128}$/', $key);
    }
}
