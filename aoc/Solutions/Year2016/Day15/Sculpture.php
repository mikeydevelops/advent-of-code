<?php

namespace Mike\AdventOfCode\Solutions\Year2016\Day15;

use Mike\AdventOfCode\Solutions\Year2016\Day15\Capsule;
use Mike\AdventOfCode\Solutions\Year2016\Day15\Disk;

class Sculpture
{
    /**
     * The disks in the sculpture.
     *
     * @var \Mike\AdventOfCode\Solutions\Year2016\Day15\Disk[]
     */
    protected array $disks;

    /**
     * The capsule in the sculpture.
     */
    protected Capsule $capsule;

    /**
     * The time elapsed. 1 tick = 1 second when interactive.
     */
    protected int $ticks = 0;

    /**
     * Create new instance of sculpture
     */
    public function __construct(array $disks, Capsule $capsule = null)
    {
        $this->disks = $disks;
        $this->capsule = $capsule ?? new Capsule;
    }

    /**
     * Advance the ticks by one.
     *
     * @return static
     */
    public function tick(): static
    {
        foreach ($this->disks as $disk) {
            $disk->tick($this->ticks);
        }

        $this->capsule->tick($this->ticks, $this->disks);

        $this->ticks++;

        return $this;
    }

    /**
     * Start a simulation of the sculpture.
     */
    public function run(): bool
    {
        while ($this->capsule->hasNotFallen() && $this->capsule->hasNotBounced()) {
            $this->tick();
        }

        return $this->capsule->hasFallen();
    }

    /**
     * Reset the state of the sculpture to the initial state.
     */
    public function reset(int $ticks = 0): static
    {
        $this->ticks = $ticks;

        $this->capsule->reset();

        foreach ($this->disks as $disk) {
            $disk->reset();
        }

        return $this;
    }
}
