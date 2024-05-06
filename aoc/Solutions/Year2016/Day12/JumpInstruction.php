<?php

namespace Mike\AdventOfCode\Solutions\Year2016\Day12;

use Mike\AdventOfCode\Solutions\Year2016\Day12\Instruction;
use Mike\AdventOfCode\Solutions\Year2016\Day12\Register;

class JumpInstruction extends Instruction
{
    /**
     * The related register.
     */
    public Register|int $register;

    /**
     * The amount of jump.
     */
    public int $value = 0;

    /**
     * Create new instance of jump instruction.
     */
    public function __construct(Register|int $register, int $value = 0)
    {
        parent::__construct('jnz', $register, $value);

        $this->register = $register;
        $this->value = $value;
    }

    /**
     * Get the value of the register.
     */
    public function source(): int
    {
        return $this->register instanceof Register
            ? $this->register->value
            : $this->register;
    }
}
