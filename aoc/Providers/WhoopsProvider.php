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
        $writer = new Writer(output: $this->app->console);

        $writer->showEditor($this->app->console->isDebug());

        $handler = new Handler($writer);

        $provider = new CollisionProvider(handler: $handler);

        $provider->register();
    }
}
