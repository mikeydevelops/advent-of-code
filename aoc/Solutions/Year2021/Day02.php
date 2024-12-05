<?php

namespace Mike\AdventOfCode\Solutions\Year2021;

use Mike\AdventOfCode\Solutions\Solution;

class Day02 extends Solution
{
    /**
     * The example input to be used to test the solution.
     */
    protected ?string $exampleInput = <<<TXT
    forward 5
    down 5
    forward 8
    up 3
    down 8
    forward 2
    TXT;

    /**
     * Process the input from the challenge.
     */
    public function transformInput(string $input): array
    {
        preg_match_all('/(?<command>\w+)\s*(?<units>\d+)/', $input, $commands, PREG_SET_ORDER);

        return array_map(function ($cmd) {
            $cmd = array_filter($cmd, 'is_string', ARRAY_FILTER_USE_KEY);
            $cmd['units'] = intval($cmd['units']);

            return $cmd;
        }, $commands);
    }

    /**
     * Run the first part of the challenge.
     */
    public function part1(): int
    {
        $commands = $this->getInput();

        $depth = array_filter($commands, fn($cmd) => $cmd['command'] !== 'forward');
        $depth = array_sum(array_map(
            fn($cmd) => $cmd['command'] === 'up' ? -$cmd['units'] : $cmd['units'],
            $depth
        ));

        $horizontal = array_filter($commands, fn($cmd) => $cmd['command'] === 'forward');
        $horizontal = array_sum(array_column($horizontal, 'units'));

        return $depth * $horizontal;
    }

    /**
     * Run the second part of the challenge.
     */
    public function part2()
    {
        $aim = $depth = $horiz = 0;

        foreach ($this->getInput() as $cmd) {
            if ($cmd['command'] === 'down') {
                $aim += $cmd['units'];
                continue;
            }

            if ($cmd['command'] === 'up') {
                $aim -= $cmd['units'];
                continue;
            }

            if ($cmd['command'] === 'forward') {
                $horiz += $cmd['units'];
                $depth += $aim * $cmd['units'];
                continue;
            }
        }

        return $depth * $horiz;
    }
}
