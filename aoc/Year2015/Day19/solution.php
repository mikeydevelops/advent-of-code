<?php

require_once __DIR__ . '/../../common.php';

/**
 * Get the replacement map for all molecules.
 *
 * @param  string  $replacements
 * @return array
 */
function getReplacementMap(string $replacements) : array
{
    $replacements = array_filter(explode("\n", $replacements));
    $map = [];

    foreach ($replacements as $replacement) {
        [$search, $replace] = explode(' => ', $replacement);

        $map[$search][] = $replace;
    }

    return $map;
}

/**
 * Reverse the replacements.
 *
 * @param  array  $replacements
 * @return array
 */
function reverseReplacements(array $replacements): array
{
    $reversed = [];

    foreach ($replacements as $search => $rep) {
        $reversed = array_merge($reversed, array_combine($rep, array_fill(0, count($rep), $search)));
    }

    return $reversed;
}

/**
 * Advent of Code 2015
 * Day 19: Medicine for Rudolph
 * Part One
 *
 * @return integer
 * @throws \Exception
 */
function aoc2015day19part1(): int
{
    [$replacements, $molecule] = explode("\n\n", getInput());
    $replacements = getReplacementMap($replacements);

    $molecules = [];

    foreach (str_split($molecule) as $i => $element) {
        foreach ($replacements as $search => $rep) {
            $searchLen = strlen($search);

            foreach ($rep as $replacement) {
                if (substr($molecule, $i, $searchLen) != $search) {
                    continue;
                }

                $new = substr_replace($molecule, $replacement, $i, $searchLen);

                $molecules[$new] = 0;
            }
        }
    }

    return count($molecules);
}


/**
 * Advent of Code 2015
 * Day 19: Medicine for Rudolph
 * Part One
 *
 * @return integer
 * @throws \Exception
 */
function aoc2015day19part2(): int
{
    [$replacements, $molecule] = explode("\n\n", getInput());
    $replacements = getReplacementMap($replacements);
    $reversed = reverseReplacements($replacements);

    $steps = 0;

    $target = $molecule;

    do {
        foreach ($reversed as $search => $replace) {
            if (($pos = strpos($target, $search)) === false) {
                continue;
            }

            $target = preg_replace('/' . $search . '/', $replace, $target, 1);

            $steps++;
        }
    } while ($target != 'e');

    return $steps;
}

if (basename(__FILE__) == basename($_SERVER["SCRIPT_FILENAME"])) {
    $molecules = aoc2015day19part1();
    line("1. The total number of unique molecules is: $molecules");

    $steps = aoc2015day19part2();
    line("2. The fewest number of steps to from e to the medicine molecule is: $steps");
}
