<?php

use Mike\AdventOfCode\Year2016\Day11\Microchip;
use Mike\AdventOfCode\Year2016\Day11\RTG;

function getComponentLocations(bool $example = false): array
{
    $floors = explode("\n", $example ? <<<TXT
    The first floor contains a hydrogen-compatible microchip and a lithium-compatible microchip.
    The second floor contains a hydrogen generator.
    The third floor contains a lithium generator.
    The fourth floor contains nothing relevant.
    TXT : getInput());

    $locations = [];
    $map = [
        'microchip' => Microchip::class,
        'generator' => RTG::class,
    ];

    foreach ($floors as $floor) {
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
 * Render the given locations as per day 11 docs in website.
 */
function renderDiagram(array $locations, int $elevator = 0): void
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

        print("F$level $e   ");

        foreach (array_merge(...array_values($all)) as $g) {
            $partNames = array_map(fn(RTG|Microchip $p) => $p->abbr(), $parts);

            if (in_array($s = $g->abbr(), $partNames)) {
                print($s . ' ' . (strlen($s) == 2 ? ' ' : ''));

                continue;
            }

            print('.   ');
        }

        line('');
    }
}

/**
 * Advent of Code 2016
 * Day 11: Radioisotope Thermoelectric Generators
 *
 * Part One
 *
 * @return void
 */
function aoc2016day11()
{
    $loc = getComponentLocations(example: false);

    renderDiagram($loc);
}

if (basename(__FILE__) == basename($_SERVER["SCRIPT_FILENAME"])) {
    aoc2016day11();
}
