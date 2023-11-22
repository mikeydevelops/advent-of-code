<?php

require_once __DIR__ . '/../../common.php';

/**
 * Advent of Code 2015
 * Day 23: Opening the Turing Lock
 * Part One
 *
 * @return integer
 */
function aoc2015day23part1(): int
{
    $instructions = explode("\n", getInput());

    $registers = [];

    $idx = 0;

    $count = count($instructions);

    while ($idx < $count) {
        $instruction = explode(' ', $instructions[$idx]);

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

    return $registers['b'];
}

if (basename(__FILE__) == basename($_SERVER["SCRIPT_FILENAME"])) {
    $registerB = aoc2015day23part1();

    line("1. Value in register b: $registerB.");
}
