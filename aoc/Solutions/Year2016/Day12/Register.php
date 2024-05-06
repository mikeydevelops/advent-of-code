<?php

namespace Mike\AdventOfCode\Solutions\Year2016\Day12;

class Register
{
    /**
     * Name of the register.
     *
     * @var string|null
     */
    public ?string $name = null;

    /**
     * The value of the register.
     */
    public int $value = 0;

    /**
     * Create new instruction instance.
     */
    public function __construct(int $value = 0, string $name = null)
    {
        $this->value = $value;
        $this->name = $name;
    }
}
