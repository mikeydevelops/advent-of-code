<?php

use Psy\Readline\Hoa\IStream;

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
 * Do a check for part two.
 *
 * @param  integer[]  $match
 * @param  integer[]  $compounds
 * @return boolean
 */
function check(array $match, array $compounds) : bool
{
    foreach ($match as $prop => $value) {
        if (in_array($prop, ['cats', 'trees'])) {
            if ($value > $compounds[$prop]) {
                return false;
            }

            continue;
        }

        if (in_array($prop, ['pomeranians', 'goldfish'])) {
            if ($value < $compounds[$prop]) {
                return false;
            }

            continue;
        }

        if ($compounds[$prop] != $value) {
            return false;
        }
    }

    return true;
}

/**
 * Advent of Code 2015
 * Day 16: Aunt Sue
 *
 * @return integer[]
 * @throws \Exception
 */
function aoc2015day16(): array
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

    $impostorAuntNo = 0;
    $realAuntNo = 0;

    foreach ($aunts as $aunt => $compounds) {
        $match = array_intersect_key($tickerTape, $compounds);

        if ($match == $compounds) {
            $impostorAuntNo = intval(substr($aunt, strpos($aunt, ' ')));
        }

        if (check($match, $compounds)) {

            $realAuntNo = intval(substr($aunt, strpos($aunt, ' ')));
        }
    }

    return [$impostorAuntNo, $realAuntNo];
}

if (basename(__FILE__) == basename($_SERVER["SCRIPT_FILENAME"])) {
    [$impostorAuntNo, $realAuntNo] = aoc2015day16();

    line("1. The impostor aunt that gave me the gift is Sue: $impostorAuntNo");
    line("2. The real aunt that gave me the gift is Sue: $realAuntNo");
}
