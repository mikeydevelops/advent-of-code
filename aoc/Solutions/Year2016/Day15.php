<?php

namespace Mike\AdventOfCode\Solutions\Year2016;

use Mike\AdventOfCode\Solutions\Solution;
use Mike\AdventOfCode\Solutions\Year2016\Day15\Disk;
use Mike\AdventOfCode\Solutions\Year2016\Day15\Sculpture;

/**
 * @method  \Mike\AdventOfCode\Solutions\Year2016\Day15\Disk[]  getInput()  Get the disks inside the sculpture.
 * @package Mike\AdventOfCode\Solutions\Year2016
 */
class Day15 extends Solution
{
    /**
     * The example input to be used to test the solution.
     */
    protected ?string $exampleInput = <<<TXT
    Disc #1 has 5 positions; at time=0, it is at position 4.
    Disc #2 has 2 positions; at time=0, it is at position 1.
    TXT;

    /**
     * Process the input from the challenge.
     *
     * @return \Mike\AdventOfCode\Solutions\Year2016\Day15\Disk[]
     */
    public function transformInput(string $input): array
    {
        preg_match_all('/^\s*(.*?)\s+has\s+(\d+)\s+positions\;\s+at\s+time\=(\d+),.*?(\d+)\.$/im', $input, $matches, PREG_SET_ORDER);

        return array_map(function (array $match) {
            return new Disk($match[1], intval($match[2]), intval($match[4]));
        }, $matches);
    }

    /**
     * Run the first part of the challenge.
     */
    public function part1(): int
    {
        $sculpture = new Sculpture($this->getInput());

        $ticks = 0;

        do {
            $sculpture->reset($ticks++);
        } while (! $sculpture->run());

        return $ticks - 1;
    }

    /**
     * Run the second part of the challenge.
     */
    public function part2(): int
    {
        $disks = $this->getInput();
        $disks[] = new Disk('Some magic appeared out of nowhere :?', 11);

        $sculpture = new Sculpture($disks);

        $ticks = $this->part1Result;

        do {
            $sculpture->reset($ticks++);
        } while (! $sculpture->run());

        return $ticks - 1;
    }
}
