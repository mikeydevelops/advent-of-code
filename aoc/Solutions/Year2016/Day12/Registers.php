<?php

namespace Mike\AdventOfCode\Solutions\Year2016\Day12;

use InvalidArgumentException;

class Registers
{
    /**
     * The current registers collection instance.
     *
     * @var static
     */
    protected static $instance = null;

    /**
     * The instantiated registers.
     *
     * @var \Mike\AdventOfCode\Solutions\Year2016\Day12\Register[]
     */
    protected array $registers = [];

    /**
     * Create new instruction instance.
     */
    public function __construct(array $registers = [])
    {
        $this->registers = $registers;
    }

    /**
     * Find a named register.
     *
     * @return static|null|mixed
     */
    public function find(string $name, $default = null): mixed
    {
        return $this->registers[$name] ?? $default;
    }

    /**
     * Push a register in the collection.
     */
    public function push(Register $register): static
    {
        $this->registers[$register->name] = $register;

        return $this;
    }

    /**
     * PHP magic to access the named registers using properties.
     */
    public function __get(string $name): Register
    {
        if (! isset($this->registers[$name])) {
            throw new InvalidArgumentException("Register [$name] does not exist in this register collection.");
        }

        return $this->registers[$name];
    }

    /**
     * Get an instance of named register.
     */
    public static function get(string $name, int $value = 0): Register
    {
        $register = static::instance()->find($name);

        if (! $register) {
            static::instance()->push($register = new Register($value, $name));
        }

        return $register;
    }

    /**
     * Get an instance of the registers collection.
     */
    public static function instance(): static
    {
        if (isset(static::$instance)) {
            return static::$instance;
        }

        return static::$instance = new static;
    }

    /**
     * Reset the static instance to a new one.
     *
     * @return static
     */
    public static function resetInstance(): static
    {
        static::$instance = null;

        return static::instance();
    }
}
