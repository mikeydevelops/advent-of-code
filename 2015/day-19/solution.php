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

if (basename(__FILE__) == basename($_SERVER["SCRIPT_FILENAME"])) {
    $molecules = aoc2015day19part1();

    line("1. The total number of unique molecules is: $molecules");
}
