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
 * Get optimal happiness given array with guests and points.
 *
 * @param  array[]  $points
 * @return integer
 */
function getOptimalHappiness(array $points) : int
{
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

    return getOptimalHappiness($points);
}

/**
 * Advent of Code 2015
 * Day 13: Knights of the Dinner Table
 * Part Two
 *
 * @return integer
 * @throws \Exception
 */
function aoc2015day13part2(): int
{
    $me = 'Me';

    // Lazy fix :/
    ini_set('memory_limit', '800M');

    $points = getPoints();

    $guests = array_keys($points);

    foreach ($guests as $guest) {
        $points[$guest][$me] = 0;
    }

    $points[$me] = array_combine($guests, array_fill(0, count($guests), 0));

    return getOptimalHappiness($points);
}

if (basename(__FILE__) == basename($_SERVER["SCRIPT_FILENAME"])) {
    $happiness = aoc2015day13part1();
    $happiness2 = aoc2015day13part2();

    line("1. The total happiness is: $happiness");
    line("2. The total happiness with me is: $happiness2");
}
