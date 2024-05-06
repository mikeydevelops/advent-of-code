<?php

namespace Mike\AdventOfCode\Solutions\Year2016\Day12;

use Mike\AdventOfCode\Solutions\Year2016\Day12\Instruction;
use Mike\AdventOfCode\Solutions\Year2016\Day12\Register;

class CopyInstruction extends Instruction
{
    /**
     * The related register.
     */
    public Register $register;

    /**
     * The amount to be copied over.
     */
    public Register|int $value = 0;

    /**
     * Create new instance of copy instruction.
     */
    public function __construct(Register $register, Register|int $value)
    {
        parent::__construct('cpy', $register, $value);

        $this->register = $register;
        $this->value = $value;
    }

    /**
     * Get the value that needs to be copied over.
     */
    public function value(): int
    {
        return $this->value instanceof Register
            ? $this->value->value
            : $this->value;
    }
}
