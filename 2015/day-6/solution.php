<?php

require_once __DIR__ . '/../../common.php';

/**
 * Get the instructions for the lights.
 *
 * @return array
 * @throws \Exception
 */
function getInstructions()
{
    $instructions = explode_trim("\n", getInput());

    $parsed = array_map(function ($instruction) {
        preg_match('/^(?<command>.*?)\s*(?<x1>\d{1,3}),(?<y1>\d{1,3})\s+through\s+(?<x2>\d{1,3}),(?<y2>\d{1,3})\s*$/', $instruction, $matches);

        return [
            'command' => $matches['command'],
            'from' => [
                'x' => intval($matches['x1']),
                'y' => intval($matches['y1']),
            ],
            'to' => [
                'x' => intval($matches['x2']),
                'y' => intval($matches['y2']),
            ],
        ];
    }, $instructions);

    if (count($parsed) !== count($instructions)) {
        return error('Some instructions were unable to be parsed!');
    }

    return $parsed;
}

/**
 * Advent of Code 2015
 * Day 6: Probably a Fire Hazard
 * Part One
 *
 * @return integer[]
 * @throws \Exception
 */
function aoc2015day6part1()
{
    $instructions = getInstructions();

    $grid = makeGrid(1000, 1000, 0);

    $litLights = 0;

    foreach ($instructions as $instruction) {
        extract($instruction);
        /** @var string $command */
        /** @var integer[] $from */
        /** @var integer[] $to */

        for ($y = $from['y']; $y <= $to['y']; $y++) {
            for ($x = $from['x']; $x <= $to['x']; $x++) {
                $prev = $grid[$y][$x];
                $value = $prev;

                if ($command == 'toggle') {
                    $value = $prev ? 0 : 1;
                }

                if ($command == 'turn on') {
                    $value = 1;
                }

                if ($command == 'turn off') {
                    $value = 0;
                }

                $grid[$y][$x] = $value;

                if ($prev == 1 && $value == 0) {
                    $litLights--;
                }

                if ($prev == 0 && $value == 1) {
                    $litLights ++;
                }
            }
        }
    }

    return $litLights;
}

if (basename(__FILE__) == basename($_SERVER["SCRIPT_FILENAME"])) {
    $litLights = aoc2015day6part1();

    line("1. The number of lit lights is: $litLights");
}
