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

        if (substr($candidate, 0, 5) !== '00000') {
            continue;
        }

        $password .= $candidate[5];
    }

    return $password;
}

/**
 * Advent of Code 2016
 * Day 5: How About a Nice Game of Chess?
 *
 * Part One
 *
 * @return string
 */
function aoc2016day5part2(): string
{
    $doorId = getInput();

    $password = [];

    $i = 0;
    while (count($password) < 8) {
        $candidate = md5($doorId.$i);
        $i++;

        if (substr($candidate, 0, 5) !== '00000') {
            continue;
        }

        $position = is_numeric($candidate[5]) ? intval($candidate[5]) : null;

        if ($position === null || $position > 7 || isset($password[$position])) {
            continue;
        }

        $password[$position] = $candidate[6];
    }

    ksort($password);

    return implode('', $password);
}


if (basename(__FILE__) == basename($_SERVER["SCRIPT_FILENAME"])) {
    $password = aoc2016day5part1();
    line("1. The password is: $password.");

    $password2 = aoc2016day5part2();
    line("2. The password is: $password2.");
}
