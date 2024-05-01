<?php

namespace Mike\AdventOfCode\Year2015\Day07;

use Mike\AdventOfCode\Year2015\Day07\LogicGate;
use Mike\AdventOfCode\Year2015\Day07\Wire;

/**
 * Get the instructions parsed instructions.
 *
 * @return \Mike\AdventOfCode\Year2015\Day07\LogicGate[]
 * @throws \Exception
 */
function getInstructions()
{
    $instructions = explode_trim("\n", getInput());

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

        $gate = new LogicGate($op, $out, $in1, $in2);

        $gate->setRaw($instruction);

        return $gate;
    }, $instructions);
}

/**
 * Process the queue of logic gates.
 *
 * @param  \LogicGate[]  $gates
 * @return void
 */
function processGates(array $gates)
{
    foreach ($gates as $idx => $gate) {

        // line($gate);
        if ($gate->ready()) {
            $gate->eval();

            // line($gate);

            unset($gates[$idx]);

            return processGates($gates);
        }
    }
}

/**
 * Advent of Code 2015
 * Day 7: Some Assembly Required
 * Part One
 *
 * @return integer
 * @throws \Exception
 */
function aoc2015day7part1()
{
    $gates = getInstructions();

    processGates($gates);

    return Wire::make('a')->getSignal();
}

/**
 * Advent of Code 2015
 * Day 7: Some Assembly Required
 * Part Two
 *
 * @param  integer  $overrideSignal
 * @return integer
 * @throws \Exception
 */
function aoc2015day7part2(int $overrideSignal)
{
    Wire::resetWires();

    $gates = getInstructions();

    // remove the gate that will override the new b signal
    foreach ($gates as $idx => $gate) {
        if ($gate->getOutput()->getName() == 'b') {
            unset($gates[$idx]);
        }
    }

    Wire::wire('b')->setSignal($overrideSignal);

    processGates($gates);

    return Wire::make('a')->getSignal();
}

if (basename(__FILE__) == basename($_SERVER["SCRIPT_FILENAME"])) {
    $signal = aoc2015day7part1();
    $signal2 = aoc2015day7part2($signal);

    line("1. The signal provided to wire a is: $signal");
    line("2. The signal provided to wire a after override is: $signal2");
}
