<?php

namespace Mike\AdventOfCode\Solutions\Year2016;

use Mike\AdventOfCode\Solutions\Solution;
use Mike\AdventOfCode\Solutions\Year2016\Day12\CopyInstruction;
use Mike\AdventOfCode\Solutions\Year2016\Day12\DecrementInstruction;
use Mike\AdventOfCode\Solutions\Year2016\Day12\IncrementInstruction;
use Mike\AdventOfCode\Solutions\Year2016\Day12\Instruction;
use Mike\AdventOfCode\Solutions\Year2016\Day12\JumpInstruction;
use Mike\AdventOfCode\Solutions\Year2016\Day12\Registers;

/**
 * @method  \Mike\AdventOfCode\Solutions\Year2016\Day12\Instruction[]  getInput()  get the inputs.
 */
class Day12 extends Solution
{
    /**
     * The example input to be used to test the solution.
     */
    protected ?string $exampleInput = <<<TXT
    cpy 41 a
    inc a
    inc a
    dec a
    jnz a 2
    dec a
    TXT;

    /**
     * Process the input from the challenge.
     *
     * @return \Mike\AdventOfCode\Solutions\Year2016\Day12\Instruction[]
     */
    public function transformInput(string $input): array
    {
        $input = split_lines($input);

        return array_map(fn(string $l) => Instruction::make(...explode(' ', $l)), $input);
    }

    /**
     * Run the first part of the challenge.
     */
    public function part1(): int
    {
        return $this->run()->a->value;
    }

    /**
     * Run the second part of the challenge.
     */
    public function part2(): int
    {
        return $this->run([ 'c' => 1, ])->a->value;
    }

    /**
     * Run the program.
     */
    public function run(array $registers = []): Registers
    {
        Registers::resetInstance();

        // initialize given registers
        foreach ($registers as $r => $value) {
            Registers::get($r, $value);
        }

        $idx = 0;
        $instructions = $this->getInput();
        $count = count($instructions);

        while ($idx < $count) {
            $i = $instructions[$idx];

            if ($i instanceof JumpInstruction) {
                if ($i->source() != 0) {
                    $idx += $i->value;

                    continue;
                }

                $idx ++;

                continue;
            }

            $idx ++;

            if ($i instanceof CopyInstruction) {
                $i->register->value = $i->value();

                continue;
            }

            if ($i instanceof IncrementInstruction) {
                $i->register->value += $i->value;

                continue;
            }

            if ($i instanceof DecrementInstruction) {
                $i->register->value -= $i->value;

                continue;
            }
        }

        return Registers::instance();
    }
}
