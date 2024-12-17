<?php

namespace Mike\AdventOfCode\Solutions\Year2024;

use Mike\AdventOfCode\Solutions\Solution;
use Mike\AdventOfCode\Solutions\Year2024\Day17\Computer;

class Day17 extends Solution
{
    /**
     * The example input to be used to test the solution.
     */
    protected ?string $exampleInput = <<<TXT
    Register A: 729
    Register B: 0
    Register C: 0

    Program: 0,1,5,4,3,0
    TXT;

    /**
     * Process the input from the challenge.
     *
     * @return array{registers:array{A:integer,B:integer,C:integer},integer[]}
     */
    public function transformInput(string $input): array
    {
        $lines = split_lines($input);

        $registers = [];

        foreach ($lines as $line) {
            if (substr($line, 0, 8) === 'Register') {
                [$label, $value] = explode(' ', str_replace(['Register ', ':'], '', $line));

                $registers[$label] = intval($value);

                continue;
            }

            if (substr($line, 0, 7) === 'Program') {
                $program = Computer::parseProgram(str_replace('Program: ', '', $line));
            }
        }

        return [$registers, $program];
    }

    /**
     * Try finding value of A. The correct value is when computer returns same program.
     *
     * @param  integer[]  $program
     * @param  integer  $start  The initial value of a.
     * @param  integer  $outNum The limit of output to check.
     * @return boolean|integer
     */
    protected function findA(array $program, int $start, int $outNum): bool|int
    {
        $count = count($program);

        if ($outNum > $count) {
            return $start;
        }

        for ($i = 0; $i < 8; $i++) {
            $possible = $start << 3 | $i;

            $output = $this->run($program, [
                'A' => $possible,
                'B' => 0,
                'C' => 0,
            ]);

            if ($output !== array_slice($program, - $outNum)) {
                continue;
            }

            $next = $this->findA($program, $possible, $outNum + 1);

            if ($next !== false) {
                return $next;
            }
        }

        return false;
    }

    /** Run a program using given registers. */
    protected function run(array $program, array $registers): array
    {
        $computer = (new Computer)
            ->load($program)
            ->setRegisters($registers);

        return $computer->run();
    }

    /**
     * Run the first part of the challenge.
     */
    public function part1(): string
    {
        /** @var \Mike\AdventOfCode\Solutions\Year2024\Day17\Computer $computer */
        [$registers, $program] = $this->getInput();

        return implode(',', $this->run($program, $registers));
    }

    /**
     * Run the second part of the challenge.
     */
    public function part2(): int
    {
        /** @var \Mike\AdventOfCode\Solutions\Year2024\Day17\Computer $computer */
        [$registers, $program] = $this->getInput();

        if ($this->testing) {
            $program = [0,3,5,4,3,0];
        }

        return $this->findA($program, 0, 1);
    }
}
