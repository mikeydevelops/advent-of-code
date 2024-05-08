<?php

namespace Mike\AdventOfCode\Solutions\Year2016\Day15;

class Capsule
{
    /**
     * Wether the capsule has fallen all the way through the machine or not.
     */
    protected bool $fallen = false;

    /**
     * Wether the capsule has bounced off a disk or not.
     */
    protected bool $bounced = false;

    /**
     * The current position of the capsule.
     */
    protected int $position = 0;

    /**
     * Create new instance of capsule.
     */
    public function __construct()
    {
        //
    }

    /**
     * Update the capsule based on elapsed ticks.
     *
     * @param  int  $elapsed  The elapsed ticks
     * @param  \Mike\AdventOfCode\Solutions\Year2016\Day15\Disk[]  $disks
     * @return static
     */
    public function tick(int $elapsed, array $disks): static
    {
        $count = count($disks);

        $current = $this->position++;

        $slice = array_slice($disks, 0, $current);
        $slice = array_reverse($slice);

        foreach ($slice as $idx => $disk) {
            if ($disk->getPositionAtTick($elapsed - $idx)) {
                $this->bounced = true;

                return $this;
            }
        }

        if ($current == $count) {
            $this->fallen = true;
        }

        return $this;
    }

    /**
     * Reset the capsule to initial state.
     */
    public function reset(): static
    {
        $this->fallen = false;
        $this->bounced = false;
        $this->position = 0;

        return $this;
    }

    /**
     * Get the current position of the capsule.
     */
    public function getPosition(): int
    {
        return $this->position;
    }

    /**
     * Check to see if the capsule has fallen through.
     */
    public function hasFallen(): bool
    {
        return $this->fallen;
    }

    /**
     * Check to see if the capsule has not fallen through.
     */
    public function hasNotFallen(): bool
    {
        return !$this->fallen;
    }

    /**
     * Check to see if the capsule has bounced from the disks.
     */
    public function hasBounced(): bool
    {
        return $this->bounced;
    }

    /**
     * Check to see if the capsule has not bounced from the disks.
     */
    public function hasNotBounced(): bool
    {
        return !$this->bounced;
    }
}
