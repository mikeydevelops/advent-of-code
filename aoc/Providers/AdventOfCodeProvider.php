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
}
