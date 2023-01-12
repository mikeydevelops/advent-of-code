<?php

require_once __DIR__ . '/../../common.php';

/**
 * Get the list of attendees and their points.
 *
 * @return array[]
 */
function getPoints() : array
{
    $attendees = [];

    // remove unused strings
    $input = str_replace(
        ['would ', 'happiness units by sitting next to ', '.', 'gain ', 'lose '],
        ['', '', '', '+', '-'],
        getInput()
    );

    foreach (explode("\n", $input) as $line) {
        [$attendee, $points, $guest] = explode(' ', $line);

        $attendees[$attendee][$guest] = intval($points);
    }

    return $attendees;
}

/**
 * Advent of Code 2015
 * Day 13: Knights of the Dinner Table
 * Part One
 *
 * @return integer
 * @throws \Exception
 */
function aoc2015day13part1(): int
{
    $points = getPoints();

    $guests = array_keys($points);

    $totalGuests = count($guests);
    $combinations = combinations($guests, $totalGuests);

    foreach ($combinations as $cIdx => $combination) {
        $arrangement = [];

        foreach ($combination as $gIdx => $guest) {
            $next = $combination[$gIdx + 1] ?? $combination[0];
            $prev = $combination[$gIdx - 1] ?? $combination[$totalGuests - 1];

            $arrangement[$guest] = 0 + $points[$guest][$prev] + $points[$guest][$next];
        }

        $combinations[$cIdx] = array_sum($arrangement);
    }

    return max($combinations);
}

if (basename(__FILE__) == basename($_SERVER["SCRIPT_FILENAME"])) {
    $happiness = aoc2015day13part1();

    line("1. The total happiness is: $happiness");
}
