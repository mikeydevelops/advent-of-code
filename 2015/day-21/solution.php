<?php

require_once __DIR__ . '/../../common.php';

/**
 * Get the boss stats.
 *
 * @return array
 * @throws \Exception
 */
function getBoss(): array
{
    $boss = [];

    $input = str_replace('Hit Points', 'hp', getInput());

    foreach (explode("\n", $input) as $line) {
        [$stat, $value] = explode(': ', $line);

        $boss[strtolower($stat)] = intval($value);
    }

    return $boss;
}

/**
 * Advent of Code 2015
 * Day 8: Matchsticks
 * Part One
 *
 * @return integer
 * @throws \Exception
 */
function aoc2015day21part1(): int
{
    $boss = getBoss();

    dd($boss);

    $wallets = [];

    return min($wallets);
}

if (basename(__FILE__) == basename($_SERVER["SCRIPT_FILENAME"])) {
    $gold = aoc2015day21part1();

    line("1. The cheapest the boss died for is: $gold");
}
