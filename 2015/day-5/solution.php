<?php

require_once __DIR__ . '/../../common.php';

/**
 * Find repeating items in an array.
 *
 * @param  array  $items
 * @return array
 */
function findRepeatingItems(array $items)
{
    $overlapping = [];

    foreach ($items as $idx => $item) {
        $next = $items[$idx + 1] ?? false;

        if ($next === false) {
            break;
        }

        if ($item !== $next) {
            continue;
        }

        if (in_array($item, $overlapping)) {
            continue;
        }

        $overlapping[] = $item;
    }

    return $overlapping;
}

/**
 * Advent of Code 2015
 * Day 5: Doesn't He Have Intern-Elves For This?
 * Part One
 *
 * @return integer
 * @throws \Exception
 */
function aoc2015day5part1()
{
    $strings = explode_trim("\n", getInput());

    $nice = 0;

    $vowels = [
        'a', 'e', 'i', 'o', 'u',
    ];

    $exclusions = [
        'ab', 'cd', 'pq', 'xy',
    ];

    foreach ($strings as $string) {
        $len = strlen($string);

        // if any of the excluded strings are in the
        // current string, skip the whole string.
        if (strlen(str_replace($exclusions, '', $string)) !== $len) {
            continue;
        }

        $chars = str_split($string);

        // find all vowels from the string
        $stringVowels = array_intersect($chars, $vowels);

        if (count($stringVowels) < 3) {
            continue;
        }

        // search for repeating letters.
        if (count(findRepeatingItems($chars)) < 1) {
            continue;
        }

        $nice ++;
    }

    return $nice;
}

if (basename(__FILE__) == basename($_SERVER["SCRIPT_FILENAME"])) {
    $part1 = aoc2015day5part1();

    line("1. The nice strings are: $part1");
}
