<?php

namespace Mike\AdventOfCode\Support;

use Closure;
use Throwable;

class Profiler
{
    /**
     * The profiler data path.
     */
    protected ?string $dataPath = null;

    /**
     * The profiler data.
     *
     * @var resource|null
     */
    protected $data = null;

    /**
     * The last tick time.
     */
    protected $tickTime;

    /**
     * The last tick memory.
     */
    protected $tickMemory;

    /**
     * The time taken.
     *
     * @var float
     */
    protected $time;

    /**
     * The memory usage.
     *
     * @var float
     */
    protected $memory;

    /**
     * Analyze given closure.
     */
    public function profile(Closure $callback): mixed
    {
        $this->prepare();
        $this->register();

        try {
            $result = $callback();
        } catch (Throwable $ex) {
            $this->unregister();
            $this->cleanup();

            throw $ex;
        }

        $this->unregister();
        $this->analyze();

        $this->cleanup();

        return $result;
    }

    /**
     * Prepare the profiler for recording data.
     */
    protected function prepare(): static
    {
        if ($this->data) {
            $this->cleanup();
        }

        $this->dataPath = tempnam(base_path('storage/temp'), 'prf');
        $this->data = fopen($this->dataPath, 'w');

        return $this;
    }

    /**
     * Clean up the resources used by the profiler.
     */
    protected function cleanup(): static
    {
        if ($this->data) {
            fclose($this->data);
            unlink($this->dataPath);
        }

        $this->dataPath = null;
        $this->data = null;
        $this->tickTime = null;

        return $this;
    }

    /**
     * The function run at each tick.
     *
     * @return void
     */
    protected function tick(): void
    {
        $time = microtime(true) - $this->tickTime;
        $memory = memory_get_usage(false) - $this->tickMemory;
        fwrite($this->data, sprintf("%.6f", $time).",".$memory.PHP_EOL);
        $this->tickTime = microtime(true);
        $this->tickMemory = memory_get_usage(false);
    }

    /**
     * Register the tick function.
     *
     * @return static
     */
    protected function register(): static
    {
        $this->tickMemory = memory_get_usage(false);
        $this->tickTime = microtime(true);
        register_tick_function([$this, 'tick']);
        declare(ticks = 1);

        return $this;
    }

    /**
     * Unregister the tick function.
     */
    protected function unregister(): static
    {
        unregister_tick_function([$this, 'tick']);

        return $this;
    }

    protected function analyze(): static
    {
        $data = fopen($this->dataPath, 'r');

        $time = 0.0;
        $memory = 0;
        $prevMemory = null;

        while (($line = fgets($data)) !== false) {
            [$t, $m] = explode(',', $line);
            $time += floatval($t);

            $memory += intval($m);
        }

        $this->time = $time;
        $this->memory = $memory;

        fclose($data);

        return $this;
    }

    /**
     * Get the time taken by the profile callback.
     */
    public function getTimeTaken()
    {
        return $this->time;
    }

    /**
     * Get the memory used by the profile callback.
     */
    public function getMemoryUsage()
    {
        return $this->memory;
    }
}
