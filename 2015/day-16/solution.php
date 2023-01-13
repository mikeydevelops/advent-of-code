<?php

require_once __DIR__ . '/../../common.php';

/**
 * Get all Aunt Sue samples.
 *
 * @return array
 * @throws \Exception
 */
function getAunts() : array
{
    $aunts = [];

    foreach (explode("\n", getInput()) as $line) {
        [$name, $compounds] = explode(': ', $line, 2);

        $compounds = array_sliding(explode(' ', str_replace([':', ','], '', $compounds)), 2, 2);
        $compounds = array_combine(array_column($compounds, 0), array_column($compounds, 1));
        $compounds = array_map('intval', $compounds);

        ksort($compounds);

        $aunts[$name] = $compounds;
    }

    return $aunts;
}

/**
 * Advent of Code 2015
 * Day 16: Aunt Sue
 * Part One
 *
 * @return integer
 * @throws \Exception
 */
function aoc2015day16part1(): int
{
    $aunts = getAunts();

    $tickerTape = [
        'children' => 3,
        'cats' => 7,
        'samoyeds' => 2,
        'pomeranians' => 3,
        'akitas' => 0,
        'vizslas' => 0,
        'goldfish' => 5,
        'trees' => 3,
        'cars' => 2,
        'perfumes' => 1,
    ];

    ksort($tickerTape);

    $auntNo = 0;

    foreach ($aunts as $aunt => $compounds) {
        $match = array_intersect_key($tickerTape, $compounds);

        if ($match == $compounds) {
            $auntNo = intval(substr($aunt, strpos($aunt, ' ')));

            break;
        }
    }

    return $auntNo;
}

if (basename(__FILE__) == basename($_SERVER["SCRIPT_FILENAME"])) {
    $auntNo = aoc2015day16part1();

    line("1. The aunt that gave me the gift is Sue: $auntNo");
}
