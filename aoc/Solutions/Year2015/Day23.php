<?php

namespace Mike\AdventOfCode\Solutions\Year2015;

use Mike\AdventOfCode\Solutions\Solution;

/**
 * @method  array[]  getInput()  Get the instructions.
 */
class Day23 extends Solution
{
    /**
     * The example input to be used to test the solution.
     */
    protected ?string $exampleInput = <<<TXT
    inc b
    jio b, +2
    tpl b
    inc b
    TXT;

    /**
     * Process the input from the challenge.
     *
     * @return array[]
     */
    public function transformInput(string $input): array
    {
        $input = explode("\n", str_replace(',', '', trim($input)));

        return array_map(fn(string $l) => explode(' ', $l), $input);
    }

    /**
     * Run the first part of the challenge.
     */
    public function part1(): int
    {
        return $this->run()['b'];
    }

    /**
     * Run the second part of the challenge.
     */
    public function part2(): int
    {
        return $this->run(registers: [
            'a' => 1,
        ])['b'];
    }

    /**
     * Run the program.
     */
    public function run(array $registers = []): array
    {
        $instructions = $this->getInput();

        $idx = 0;

        $count = count($instructions);

        while ($idx < $count) {
            $instruction = $instructions[$idx];

            if (count($instruction) < 2) {
                continue;
            }

            $cmd = $instruction[0];
            $register = $cmd != 'jmp' ? rtrim($instruction[1], ',') : null;
            $offset = intval($cmd == 'jmp' ? $instruction[1] : ($instruction[2] ?? 0));

            if ($register && ! isset($registers[$register])) {
                $registers[$register] = 0;
            }

            if ($cmd == 'jmp') {
                $idx += $offset;

                continue;
            }

            if ($cmd == 'jie') {
                $idx += ($registers[$register] % 2) == 0 ? $offset : 1;

                continue;
            }

            if ($cmd == 'jio') {
                $idx += $registers[$register] == 1 ? $offset : 1;

                continue;
            }

            if ($cmd == 'hlf') {
                $registers[$register] = intval($registers[$register] / 2);
                $idx += 1;

                continue;
            }

            if ($cmd == 'tpl') {
                $registers[$register] *= 3;
                $idx += 1;

                continue;
            }

            if ($cmd == 'inc') {
                $registers[$register] ++;
                $idx += 1;

                continue;
            }
        }

        return $registers;
    }
}
