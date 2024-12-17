<?php

namespace Mike\AdventOfCode\Solutions\Year2024\Day17;

use Exception;

class Computer
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
     * The defined registers.
     *
     * @var array<string,integer|float>
     */
    protected array $registers = [
        'A' => 0,
        'B' => 0,
        'C' => 0,
    ];

    /**
     * The current position of the program.
     */
    protected int $pointer = 0;

    /**
     * Supported instructions.
     *
     * Keys represent instruction opcode and the value is the instruction method.
     *
     * @var array<int,string>
     */
    protected array $instructions = [
        0 => 'adv',
        1 => 'bxl',
        2 => 'bst',
        3 => 'jnz',
        4 => 'bxc',
        5 => 'out',
        6 => 'bdv',
        7 => 'cdv',
    ];

    /**
     * The output of the program.
     */
    protected array $output = [];

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
     * Add new register to the computer.
     *
     * @param  string  $label  The name of the register.
     * @param  integer  $value  The value of the register.
     * @return static
     */
    public function addRegister(string $label, int $value): static
    {
        $this->registers[$label] = $value;

        return $this;
    }

    /**
     * Set the computer registers.
     *
     * @param  array  $registers
     * @return static
     */
    public function setRegisters(array $registers): static
    {
        $this->registers = $registers;

        return $this;
    }

    /**
     * Run the loaded program.
     *
     * @return integer[]
     */
    public function run(): array
    {
        if (empty($this->memory)) {
            throw new Exception("No program was loaded.");
        }

        $this->pointer = 0;
        $this->output = [];
        $this->halted = false;

        while ($this->pointer < count($this->memory)) {
            if ($this->halted) {
                break;
            }

            // if we read a null, halt.
            if (is_null($opcode = $this->value()) || is_null($operand = $this->value($this->pointer + 1))) {
                break;
            }

            if (! isset($this->instructions[$opcode])) {
                throw new Exception("Tried to run undefined instruction with opcode $opcode.");
            }

            $op = $this->instructions[$opcode];

            $result = $this->{$op}($operand);

            if ($opcode === 3 && $result) {
                continue;
            }

            $this->pointer += 2;
        }

        // ensure we are properly halted after loop ends.
        !$this->halted && $this->halt();

        return $this->output;
    }

    /**
     * Get the value of a combo operand.
     */
    public function combo(int $operand): int
    {
        if ($operand >= 0 && $operand <= 3) {
            return $operand;
        }

        $registers = [
            4 => 'A',
            5 => 'B',
            6 => 'C',
        ];

        if (isset($registers[$operand])) {
            return $this->registers[$registers[$operand]];
        }

        throw new \Exception("Received invalid operand $operand.");
    }

    /**
     * Get a value from memory at given address or
     * at the current pointer position if address is not provided.
     */
    public function value(?int $address = null): ?int
    {
        return $this->memory[$address ?? $this->pointer] ?? null;
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
     * Divide value from register A by 2 to the power of the value of combo $operand.
     *
     * @param  integer  $operand
     * @return integer
     */
    public function adv(int $operand): int
    {
        // not casting to int gave me headaches in the form of infinite loops :/
        return $this->registers['A'] = (int) floor($this->registers['A'] / pow(2, $this->combo($operand)));
    }

    /**
     * Calculate bitwise XOR for register B.
     *
     * @param  integer  $operand
     * @return integer
     */
    public function bxl(int $operand): int
    {
        return $this->registers['B'] = $this->registers['B'] ^ $operand;
    }

    /**
     * Update register B with the value of the combo $operand modulo 8.
     *
     * @param  integer  $operand
     * @return integer
     */
    public function bst(int $operand): int
    {
        return $this->registers['B'] = $this->combo($operand) % 8;
    }

    /**
     * Set the pointer to $operand if value in register A is not 0.
     *
     * @param  integer  $operand
     * @return boolean  If it jumped.
     */
    public function jnz(int $operand): bool
    {
        if ($this->registers['A'] === 0) {
            return false;
        }

        $this->pointer = $operand;

        return true;
    }

    /**
     * Calculate bitwise XOR for register B and register C and assign it to register B.
     *
     * @param  integer  $operand
     * @return integer
     */
    public function bxc(int $operand): int
    {
        return $this->registers['B'] = $this->registers['B'] ^ $this->registers['C'];
    }

    /**
     * Output the value the value of the combo $operand modulo 8.
     *
     * @param  integer  $operand
     * @return integer
     */
    public function out(int $operand): int
    {
        return $this->output[] = $this->combo($operand) % 8;
    }

    /**
     * Divide value from register A by 2 to the power of combo $operand and store it in register B.
     *
     * @param  integer  $operand
     * @return integer
     */
    public function bdv(int $operand): int
    {
        return $this->registers['B'] = floor($this->registers['A'] / pow(2, $this->combo($operand)));
    }

    /**
     * Divide value from register A by 2 to the power of combo $operand and store it in register C.
     *
     * @param  integer  $operand
     * @return integer
     */
    public function cdv(int $operand): int
    {
        return $this->registers['C'] = floor($this->registers['A'] / pow(2, $this->combo($operand)));
    }
}
