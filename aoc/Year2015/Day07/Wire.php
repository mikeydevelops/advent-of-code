<?php

namespace Mike\AdventOfCode\Year2015\Day07;

class Wire
{
    /** The name of the wire. */
    protected string $name;

    /** The output of the wire. */
    protected int|null $signal = null;

    /**
     * An array of already instanciated wires.
     *
     * @var static[]
     */
    protected static array $wires = [];

    /**
     * Create new wire instance.
     *
     * @return void
     */
    public function __construct(string $name, int $signal = null)
    {
        $this->name = $name;
        $this->signal = $signal;
    }

    /**
     * Get the current signal of the wire.
     *
     * @return int
     */
    public function getSignal() : int|null
    {
        return $this->signal;
    }

    /**
     * Set the new signal for the wire.
     *
     * @param  int  $signal
     * @return \Wire
     */
    public function setSignal(int $signal) : self
    {
        $this->signal = $signal;

        return $this;
    }

    /**
     * Get the name of the wire.
     *
     * @return string
     */
    public function getName() : string
    {
        return $this->name;
    }

    /**
     * Check to see if the wire has signal.
     *
     * @return bool
     */
    public function hasSignal() : bool
    {
        return !is_null($this->signal);
    }

    /**
     * Get or add new instance of a wire with given name.
     *
     * @param  string|static|int  $wire
     * @return static
     */
    public static function make(string|self|int|null $wire) : static|int|null
    {
        if (is_int($wire) || is_null($wire)) {
            return $wire;
        }

        if (is_numeric($wire)) {
            return intval($wire);
        }

        if ($wire instanceof static) {
            if (! isset(static::$wires[$name = $wire->getName()])) {
                static::$wires[$name] = $wire;
            }

            return $wire;
        }

        if (! isset(static::$wires[$wire])) {
            static::$wires[$wire] = new static($wire);
        }

        return static::$wires[$wire];
    }

    /**
     * Get an instance of the provided wire.
     *
     * @param   string|\Mike\AdventOfCode\Year2015\Day07\Wire $wire
     * @return static
     */
    public static function wire(string|self $wire) : static
    {
        return static::make($wire);
    }

    /**
     * Reset the wire cache.
     *
     * @return void
     */
    public static function resetWires() : void
    {
        static::$wires = [];
    }
}
