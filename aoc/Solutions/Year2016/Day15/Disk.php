<?php

namespace Mike\AdventOfCode\Solutions\Year2016\Day15;

class Disk
{
    /**
     * The name of the disk.
     */
    public ?string $name = null;

    /**
     * The amount of positions this disk spins.
     */
    protected int $positions;

    /**
     * The initial position this disk is at.
     */
    protected int $initial = 0;

    /**
     * The current position of the disk.
     */
    protected int $current = 0;

    /**
     * Create new instance of disk.
     */
    public function __construct(string $name, int $positions, int $initial = 0)
    {
        $this->name = $name;
        $this->positions = $positions;
        $this->initial = $initial;
        $this->current = $initial;
    }

    /**
     * Update the disk based on elapsed ticks.
     */
    public function tick(int $elapsed)
    {
        $this->current = $this->getPositionAtTick($elapsed);

        return $this;
    }

    /**
     * Get the position of the disk at the specified tick.
     */
    public function getPositionAtTick(int $tick): int
    {
        $position = $this->initial + ($tick % $this->positions);

        if ($position >= $this->positions) {
            $position -= $this->positions;
        }

        return $position;
    }

    /**
     * Get the current position of the disk.
     */
    public function getPosition(): int
    {
        return $this->current;
    }

    /**
     * Reset the disk to the initial state.
     */
    public function reset(): static
    {
        $this->current = $this->initial;

        return $this;
    }
}
