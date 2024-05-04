<?php

namespace Mike\AdventOfCode\Solutions\Year2015;

use Mike\AdventOfCode\Solutions\Solution;
use Mike\AdventOfCode\Solutions\Year2015\Day07\LogicGate;
use Mike\AdventOfCode\Solutions\Year2015\Day07\Wire;

/**
 * @method  \Mike\AdventOfCode\Solutions\Year2015\Day07\LogicGate[]  getInput()  The input for the solution.
 */
class Day07 extends Solution
{
    /**
     * The example input to be used to test the solution.
     */
    protected ?string $exampleInput = <<<TXT
    123 -> a
    456 -> b
    a AND b -> d
    a OR b -> e
    a LSHIFT 2 -> f
    b RSHIFT 2 -> g
    NOT a -> h
    NOT b -> i
    TXT;

    /**
     * Process the input from the challenge.
     */
    public function transformInput(string $input): array
    {
        return array_map(function ($instruction) {
            $tokens = explode(' ', $instruction);
            $tokenLen = count($tokens);
            $out = $in1 = $in2 = $op = null;

            if ($tokenLen == 5) { // Full expression
                $in1 = Wire::make(array_shift($tokens));
                $op = array_shift($tokens);
                $in2 = Wire::make(array_shift($tokens));
            }

            if ($tokenLen == 4) { // NOT
                $op = array_shift($tokens);
                $in1 = Wire::make(array_shift($tokens));
            }

            if ($tokenLen == 3) { // assignment
                $op = 'ASSIGN';
                $in1 = Wire::make(array_shift($tokens));
            }

            $out = Wire::wire(array_pop($tokens));

            return  (new LogicGate($op, $out, $in1, $in2))
                ->setRaw($instruction);
        }, explode_trim("\n", $input));
    }

    /**
     * Run the first part of the challenge.
     */
    public function part1(): int
    {
        $this->processGates($this->getInput());

        return Wire::make('a')->getSignal();
    }

    /**
     * Run the second part of the challenge.
     */
    public function part2(): int
    {
        Wire::resetWires();

        $gates = $this->getInput();

        // remove the gate that will override the new b signal
        foreach ($gates as $idx => $gate) {
            if ($gate->getOutput()->getName() == 'b') {
                unset($gates[$idx]);
            }
        }

        Wire::wire('b')->setSignal($this->part1Result);

        $this->processGates($gates);

        return Wire::make('a')->getSignal();
    }

    /**
     * Process the queue of logic gates.
     *
     * @param  \Mike\AdventOfCode\Solutions\Year2015\Day07\LogicGate[]  $gates
     * @return void
     */
    public function processGates(array $gates): void
    {
        foreach ($gates as $idx => $gate) {
            if ($gate->ready()) {
                $gate->eval();

                unset($gates[$idx]);

                $this->processGates($gates);

                return;
            }
        }
    }
}
