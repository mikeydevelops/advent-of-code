<?php

require_once __DIR__ . '/../../common.php';

/**
 * Advent of Code 2015
 * Day 1: Not Quite Lisp
 *
 * @return integer[]
 * @throws \Exception
 */
function aoc2015day1()
{
    $input = getInput();

    $floor = 0;
    $basementPosition = 0;

    foreach (str_split($input) as $idx => $instruction) {
        if ($instruction == '(') {
            $floor ++;
        }

        if ($instruction == ')') {
            $floor --;
        }

        if ($basementPosition === 0 && $floor == -1) {
            $basementPosition = $idx + 1;
        }
    }

    return [$floor, $basementPosition];
}

if (basename(__FILE__) == basename($_SERVER["SCRIPT_FILENAME"])) {
    list($floor, $basementPosition) = aoc2015day1();

    line("1. Santa will need to go to floor: $floor");
    line("2. The position of the character that causes Santa to enter the basement is: $basementPosition");
}
