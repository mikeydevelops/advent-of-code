<?php

namespace Mike\AdventOfCode\Providers;

use Mike\AdventOfCode\Providers\Provider;
use NunoMaduro\Collision\Handler;
use NunoMaduro\Collision\Provider as CollisionProvider;
use NunoMaduro\Collision\Writer;

class WhoopsProvider extends Provider
{
    /**
     * Register new services in application container.
     */
    public function register(): void
    {
        $console = clone $this->app->console;

        // remove line wrapping for exceptions.
        $console->setMaxLineWith(0);

        $writer = new Writer(output: $console);

        $writer->showEditor($console->isDebug());

        $handler = new Handler($writer);

        $provider = new CollisionProvider(handler: $handler);

        $provider->register();
    }
}
