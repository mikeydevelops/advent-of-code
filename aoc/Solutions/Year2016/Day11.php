<?php

namespace Mike\AdventOfCode\Solutions\Year2016;

use Mike\AdventOfCode\Solutions\Solution;
use Mike\AdventOfCode\Solutions\Year2016\Day11\Microchip;
use Mike\AdventOfCode\Solutions\Year2016\Day11\RTG;

class Day11 extends Solution
{
    /**
     * The example input to be used to test the solution.
     */
    protected ?string $exampleInput = <<<TXT
    The first floor contains a hydrogen-compatible microchip and a lithium-compatible microchip.
    The second floor contains a hydrogen generator.
    The third floor contains a lithium generator.
    The fourth floor contains nothing relevant.
    TXT;

    /**
     * Process the input from the challenge.
     */
    public function transformInput(string $input): mixed
    {
        $locations = [];
        $map = [
            'microchip' => Microchip::class,
            'generator' => RTG::class,
        ];

        foreach (split_lines($input) as $floor) {
            $location = [];

            if (preg_match_all('/([a-z]+)(?:-compatible)?\s(microchip|generator)/', $floor, $parts, PREG_SET_ORDER)) {
                foreach ($parts as $part) {
                    $location[] = new $map[$part[2]]($part[1]);
                }
            }

            $locations[] = $location;
        }

        return $locations;
    }

    /**
     * Run the first part of the challenge.
     */
    public function part1()
    {
        $this->renderDiagram($this->getInput());
    }

    /**
     * Run the second part of the challenge.
     */
    public function part2()
    {
        //
    }

    /**
     * Render the given locations as per day 11 docs in website.
     */
    public function renderDiagram(array $locations, int $elevator = 0): void
    {
        $all = array_group_by(
            array_merge(...$locations),
            fn(RTG|Microchip $item) => $item->element
        );

        $all = array_map(function ($g) {
            // sort so generator is first in the group, then microchip
            usort($g, fn ($a) => $a instanceof RTG ? 0 : 1);

            return $g;
        }, $all);

        foreach (array_reverse($locations) as $idx => $parts) {
            $level = 4 - $idx;

            $e = $level == $elevator + 1 ? 'E' : '.';

            $this->io->write("F$level $e   ");

            foreach (array_merge(...array_values($all)) as $g) {
                $partNames = array_map(fn(RTG|Microchip $p) => $p->abbr(), $parts);

                if (in_array($s = $g->abbr(), $partNames)) {
                    $this->io->write($s . ' ' . (strlen($s) == 2 ? ' ' : ''));

                    continue;
                }

                $this->io->write('.   ');
            }

            $this->io->line('');
        }
    }
}
