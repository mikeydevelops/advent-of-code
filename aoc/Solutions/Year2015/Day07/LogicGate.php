<?php

namespace Mike\AdventOfCode\Solutions\Year2015\Day07;

use Mike\AdventOfCode\Solutions\Year2015\Day07\Wire;

class LogicGate
{
    /**
     * The operation that the logic gate should perform.
     *
     * @var string
     */
    protected string $operator;

    /**
     * The first signal for the gate.
     *
     * @var \Mike\AdventOfCode\Solutions\Year2015\Day07\Wire|int
     */
    protected Wire|int $input1;

    /**
     * The second signal for the gate.
     *
     * @var \Mike\AdventOfCode\Solutions\Year2015\Day07\Wire|int
     */
    protected Wire|int|null $input2;

    /**
     * The output of the gate.
     *
     * @var \Mike\AdventOfCode\Solutions\Year2015\Day07\Wire
     */
    protected Wire $output;

    /**
     * The raw instruction for the gate.
     *
     * @var string
     */
    protected string $raw = '';

    /**
     * Create new instance of the logic gate.
     *
     * @return void
     */
    public function __construct(string $operator, Wire|string $output, Wire|string|int $input1, Wire|string|int $input2 = null)
    {
        $this->operator = $operator;

        $this->output = Wire::wire($output);

        $this->input1 = Wire::make($input1);
        $this->input2 = Wire::make($input2);
    }

    /**
     * Get the signals of the gate as array.
     *
     * @return integer[]
     */
    public function signals() : array
    {
        $s1 = $this->input1 instanceof Wire
            ? $this->input1->getSignal() : $this->input1;

        $s2 = $this->input2 instanceof Wire
            ? $this->input2->getSignal() : $this->input2;

        return [$s1, $s2];
    }

    /**
     * Check to see if the gate is ready to be tested.
     *
     * @return bool
     */
    public function ready() : bool
    {
        [$s1, $s2] = $this->signals();

        if (is_null($s1)) {
            return false;
        }

        if (is_null($s2) && !in_array($this->operator, ['ASSIGN', 'NOT'])) {
            return false;
        }

        return true;
    }

    /**
     * Evaluate the output signal.
     *
     * @return $this
     */
    public function eval()
    {
        if (! $this->ready()) {
            return $this;
        }

        [$s1, $s2] = $this->signals();


        if ($this->operator == 'ASSIGN') {
            $result = $s1;
        }

        if ($this->operator == 'NOT') {
            $result = ~ $s1;
        }

        if ($this->operator == 'AND') {
            $result = $s1 & $s2;
        }

        if ($this->operator == 'OR') {
            $result = $s1 | $s2;
        }

        if ($this->operator == 'LSHIFT') {
            $result = $s1 << $s2;
        }

        if ($this->operator == 'RSHIFT') {
            $result = $s1 >> $s2;
        }

        $this->output->setSignal($result);

        return $this;
    }

    /**
     * Get the output of the gate.
     *
     * @return \Mike\AdventOfCode\Solutions\Year2015\Day07\Wire
     */
    public function getOutput() : Wire
    {
        return $this->output;
    }

    /**
     * Set the raw gate string.
     *
     * @param  string  $raw
     * @return static
     */
    public function setRaw(string $raw) : static
    {
        $this->raw = $raw;

        return $this;
    }

    /**
     * Get the raw gate string.
     *
     * @return string
     */
    public function getRaw() : string
    {
        return $this->raw;
    }

    /**
     * Convert the object to string;
     *
     * @return string
     */
    public function __toString()
    {
        return $this->raw;
    }
}
