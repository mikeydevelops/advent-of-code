<?php

/**
 * Advent of Code 2016
 * Day 5: How About a Nice Game of Chess?
 *
 * Part One
 *
 * @return string
 */
function aoc2016day5part1(): string
{
    $doorId = getInput();

    $password = '';

    $i = 0;
    while (strlen($password) < 8) {
        $candidate = md5($doorId.$i);
        $i++;

        $sub = substr($candidate, 0, 5);

        if ($sub !== '00000') {
            continue;
        }

        $password .= $candidate[5];
    }

    return $password;
}


if (basename(__FILE__) == basename($_SERVER["SCRIPT_FILENAME"])) {
    $password = aoc2016day5part1();

    line("1. The password is: $password.");
}
