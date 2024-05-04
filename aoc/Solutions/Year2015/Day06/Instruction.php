<?php

namespace Mike\AdventOfCode\Solutions\Year2015\Day06;

use Mike\AdventOfCode\Solutions\Year2015\Day06\Position;

class Instruction
{
    /**
     * The instruction command.
     */
    public string $command;

    /**
     * The position from which this instruction starts.
     */
    public Position $from;

    /**
     * The position to which this instruction ends.
     */
    public Position $to;

    /**
     * Create new instance of Instruction.
     */
    public function __construct(string $command)
    {
        $this->command = $command;
    }

    /**
     * Get the from position.
     */
    public function getFrom(): Position
    {
        return $this->from;
    }

    /**
     * Set the from position.
     */
    public function setFrom(Position $position): static
    {
        $this->from = $position;

        return $this;
    }

    /**
     * Get the to position.
     */
    public function getTo(): Position
    {
        return $this->to;
    }

    /**
     * Set the to position.
     */
    public function setTo(Position $position): static
    {
        $this->to = $position;

        return $this;
    }
}
