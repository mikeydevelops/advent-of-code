<?php

namespace Mike\AdventOfCode\Solutions\Year2016\Day12;

use Mike\AdventOfCode\Solutions\Year2016\Day12\Instruction;
use Mike\AdventOfCode\Solutions\Year2016\Day12\Register;

class DecrementInstruction extends Instruction
{
    /**
     * The related register.
     */
    public Register $register;

    /**
     * The amount to decrement.
     */
    public int $value = 1;

    /**
     * Create new instance of decrement instruction.
     */
    public function __construct(Register $register, int $value = 1)
    {
        parent::__construct('dec', $register, $value);

        $this->register = $register;
        $this->value = $value;
    }
}
