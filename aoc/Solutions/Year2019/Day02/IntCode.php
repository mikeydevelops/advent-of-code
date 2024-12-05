<?php

namespace Mike\AdventOfCode\Solutions\Year2019\Day02;

class IntCode
{
    /**
     * Wether the current program is halted or not.
     * @var boolean
     */
    protected bool $halted = false;

    /**
     * The currently loaded program.
     *
     * @var integer[]
     */
    protected array $memory = [];

    /**
     * The current position of the program.
     */
    protected int $pointer = 0;

    /**
     * Supported operations.
     */
    protected array $operations = [
        1 => [
            'opcode' => 1,
            'method' => 'add',
            'parameters' => 3,
        ],
        2 => [
            'opcode' => 2,
            'method' => 'multiply',
            'parameters' => 3,
        ],
        99 => [
            'opcode' => 99,
            'method' => 'halt',
            'parameters' => 0,
        ],
    ];

    /**
     * Create new instance of IntCode.
     */
    public function __construct()
    {
        //
    }

    /**
     * Load new program.
     *
     * @param  string|integer[]  $program
     * @return static
     */
    public function load(string|array $program): static
    {
        if (is_string($program)) {
            $program = static::parseProgram($program);
        }

        $this->memory = $program;

        return $this;
    }

    /**
     * Run the loaded program.
     */
    public function run(): int
    {
        if (empty($this->memory)) {
            // throw error?
            return 0;
        }

        for ($this->pointer = 0; $this->pointer < count($this->memory); $this->pointer ++) {
            if ($this->halted) {
                break;
            }

            $opcode = $this->value();

            if (! isset($this->operations[$opcode])) {
                continue;
            }

            $op = $this->operations[$opcode];

            $this->{$op['method']}(...$this->parameters($op['parameters'] ?? 0));

            $this->pointer += $op['parameters'];
        }

        return $this->value(0);
    }

    /**
     * Get a value from memory at given address or
     * at the current pointer position if address is not provided.
     */
    public function value(?int $address = null): int
    {
        return $this->memory[$address ?? $this->pointer];
    }

    /**
     * Modify the current memory at given address.
     */
    protected function modify(int $address, int $value): static
    {
        $this->memory[$address] = $value;

        return $this;
    }

    /**
     * Get the needed parameters from memory.
     */
    protected function parameters(int $amount)
    {
        $params = [];

        for ($i = 1; $i <= $amount; $i ++) {
            $params[] = $this->value($this->pointer + $i);
        }

        return $params;
    }

    /**
     * Parse an IntCode program from string.
     */
    public static function parseProgram(string $program)
    {
        return array_map('intval', preg_split('/,\s*/', $program));
    }

    /**
     * Halt the program.
     */
    public function halt(): void
    {
        $this->halted = true;

        return;
    }

    /**
     * Run addition operation.
     */
    public function add(int $first, int $second, int $dest): int
    {
        $value =  $this->value($first) + $this->value($second);

        $this->modify($dest, $value);

        return $value;
    }

    /**
     * Run addition operation.
     */
    public function multiply(int $first, int $second, int $dest): int
    {
        $value =  $this->value($first) * $this->value($second);

        $this->modify($dest, $value);

        return $value;
    }
}
