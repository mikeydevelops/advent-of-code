<?php

namespace Mike\AdventOfCode\Solutions\Year2016\Day08;

use Mike\AdventOfCode\Solutions\Year2016\Day08\Command;

class Rotate extends Command
{
    /**
     * The type of axis to be rotated.
     */
    public string $type;

    /**
     * The id of the item to be rotated.
     */
    public int $id;

    /**
     * The amount of times the item will be rotated.
     */
    public int $value;

    /**
     * Create new instance of rotate command.
     */
    public function __construct(string $type, int $id, int $value)
    {
        parent::__construct('rotate', func_get_args());

        $this->type = $type;
        $this->id = $id;
        $this->value = $value;
    }

    /**
     * Update the given display.
     *
     * @param  int[][]  $display
     * @return int[][]
     */
    public function updateDisplay(array $display): array
    {
        if ($this->type === 'row') {
            $display[$this->id] = array_rotate($display[$this->id], $this->value);

            return $display;
        }

        $column = array_rotate(array_column($display, $this->id), $this->value);

        $count = count($display);

        for ($i = 0; $i < $count; $i++) {
            $display[$i][$this->id] = $column[$i];
        }

        return $display;
    }
}
