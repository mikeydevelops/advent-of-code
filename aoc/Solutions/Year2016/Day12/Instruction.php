<?php

namespace Mike\AdventOfCode\Solutions\Year2016\Day12;

use Mike\AdventOfCode\Solutions\Year2016\Day12\Registers;

class Instruction
{
    /**
     * The command this instruction will run.
     */
    public string $command;

    /**
     * The first parameter for the command.
     */
    public Register|int $x;

    /**
     * The second parameter for the command if available.
     */
    public Register|int|null $y;

    /**
     * Create new instruction instance.
     */
    public function __construct(string $command, Register|string|int $x, Register|string|int|null $y = null)
    {
        $this->command = $command;

        if (is_numeric($x)) {
            $x = intval($x);
        }

        if (is_numeric($y)) {
            $y = intval($y);
        }

        $this->x = is_string($x) ? Registers::get($x) : $x;
        $this->y = is_string($y) ? Registers::get($y) : $y;
    }

    /**
     * Make an instruction based on command.
     */
    public static function make(string $command, Register|string|int $x, Register|string|int|null $y = null): static
    {
        if (is_numeric($x)) {
            $x = intval($x);
        }

        if (is_numeric($y)) {
            $y = intval($y);
        }

        $x = is_string($x) ? Registers::get($x) : $x;
        $y = is_string($y) ? Registers::get($y) : $y;

        if ($command == 'cpy') {
            return new CopyInstruction($y, $x);
        }

        if ($command == 'jnz') {
            return new JumpInstruction($x, $y);
        }

        if ($command == 'inc') {
            return new IncrementInstruction($x, $y ?? 1);
        }

        if ($command == 'dec') {
            return new DecrementInstruction($x, $y ?? 1);
        }

        return new static($command, $x, $y);
    }

    /**
     * Convert the object to string.
     *
     * @return string
     */
    public function __toString(): string
    {
        $xName = $this->x instanceof Register ? $this->x->name : $this->x;
        $xValue = $this->x instanceof Register ? $this->x->value : $this->x;

        $yName = '';
        $yValue = '';

        if ($this->y) {
            $yName = ' ' . ($this->y instanceof Register ? $this->y->name : $this->y);
            $yValue = ' ' . ($this->y instanceof Register ? $this->y->value : $this->y);
        }

        return "$this->command $xName$yName -> $xValue$yValue";
    }
}
