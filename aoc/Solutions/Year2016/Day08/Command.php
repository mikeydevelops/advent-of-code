<?php

namespace Mike\AdventOfCode\Solutions\Year2016\Day08;

use Mike\AdventOfCode\Solutions\Year2016\Day08\Rectangle;
use Mike\AdventOfCode\Solutions\Year2016\Day08\Rotate;

class Command
{
    /**
     * The string representation of the command.
     */
    protected string $command;

    /**
     * The command arguments.
     */
    protected string|array $args = [];

    /**
     * Create new instance of command.
     */
    public function __construct(string $command, string|array $args)
    {
        $this->command = $command;
        $this->args = $args;
    }

    /**
     * Update the given display.
     *
     * @param  int[][]  $display
     * @return int[][]
     */
    public function updateDisplay(array $display): array
    {
        return $display;
    }

    /**
     * Create new command instance from string.
     */
    public static function fromString(string $command, string $args): static
    {
        if ($command === 'rect') {
            $args = array_map('intval', explode('x', $args));

            return new Rectangle($args[0], $args[1]);
        }

        if ($command === 'rotate') {
            preg_match('/^(row|column)\s+(?:x|y)\=(\d+)\s+by\s+(\d+)$/i', $args, $matches);

            return new Rotate($matches[1], $matches[2], $matches[3]);
        }

        return new static($command, $args);
    }
}
