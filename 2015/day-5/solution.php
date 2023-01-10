<?php

require_once __DIR__ . '/../../common.php';

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

/**
 * Advent of Code 2015
 * Day 5: Doesn't He Have Intern-Elves For This?
 * Part Two
 *
 * @return integer
 * @throws \Exception
 */
function aoc2015day5part2()
{
    $strings = explode_trim("\n", getInput());

    $nice = 0;

    foreach ($strings as $string) {
        $pairs = [];
        $repeats = [];

        $pair = '';
        $len = strlen($string);

        foreach (str_split($string) as $idx => $char) {
            $pair .= $char;

            if (strlen($pair) == 2) {
                $pairs[] = $pair;
                $pair = $char;
            }

            $compareIdx = $idx + 2;

            if ($compareIdx < $len && $char == $string[$compareIdx]) {
                $repeats[] = $char;
            }
        }

        // if there is no character that repeats
        if (count($repeats) < 1) {
            continue;
        }

        $pairCount = count($pairs);
        $uniquePairs = count(array_flip($pairs));

        // if there are overlapping characters ex. aaa
        // and there are no other repeating pairs.
        if (preg_match('/(\w)\1{2}/', $string) && $pairCount - $uniquePairs <= 1) {
            continue;
        }

        // if there are no repeating pairs.
        if ($pairCount === $uniquePairs) {
            continue;
        }

        $nice ++;
    }

    return $nice;
}

if (basename(__FILE__) == basename($_SERVER["SCRIPT_FILENAME"])) {
    $part1 = aoc2015day5part1();
    $part2 = aoc2015day5part2();

    line("1. The nice strings are: $part1");
    line("2. The better nice strings are: $part2");
}
