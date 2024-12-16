<?php

namespace Mike\AdventOfCode\Solutions\Year2024\Day14;

class Robot
{
    /**
     * The X coordinate where the robot is.
     */
    public int $x = 0;

    /**
     * The Y coordinate where the robot is.
     */
    public int $y = 0;

    /**
     * The velocity of the robot.
     * tiles per second.
     *
     * @var array{int,int}
     */
    public array $v = [0,0];

    /**
     * The initial x and y of the robot.
     * @var array{int,int}
     */
    public array $initialPosition = [];

    /**
     * Create new instance of Robot.
     */
    public function __construct(array $position = [], array $velocity = [])
    {
        if (! empty($position)) {
            $this->x = $position[0];
            $this->y = $position[1];
        }

        if (! empty($velocity)) {
            $this->v = $velocity;
        }

        $this->initialPosition = $position;
    }

    /**
     * Move the robot one time.
     *
     * @param  array{int,int} $space
     * @param  integer  $times  How many times to robot has to move.
     * @return \Mike\AdventOfCode\Solutions\Year2024\Day14\Robot
     */
    public function move(array $space, int $times = 1): self
    {
        [$w, $h] = $space;
        [$vx, $vy] = $this->v;

        // wrap around
        $x = (($this->x + ($vx * $times)) % $w + $w) % $w;

        // wrap around
        $y = (($this->y + ($vy * $times)) % $h + $h) % $h;

        $this->x = $x;
        $this->y = $y;

        return $this;
    }

    /**
     * Reset the robot.
     *
     * @return self
     */
    public function reset(): self
    {
        $this->x = $this->initialPosition[0];
        $this->y = $this->initialPosition[1];

        return $this;
    }
}
