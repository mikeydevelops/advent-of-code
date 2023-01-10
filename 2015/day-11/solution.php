<?php

require_once __DIR__ . '/../../common.php';

/**
 * Increment one character of given password.
 *
 * @param  string  $password
 * @return string
 */
function incrementString(string $password, int $times = 1) : string
{
    for ($i = 0; $i < $times; $i++) {
        $password ++;
    }

    return $password;
}

/**
 * Check to see if a string contains $limit consecutive characters.
 *
 * @param  string  $string
 * @param  integer  $limit 0 based.
 * @return boolean
 */
function stringHasConsecutiveCharacters(string $string, int $limit = 1) : bool
{
    if ($limit <= 0) {
        return false;
    }

    $len = strlen($string);
    $matching = 0;

    foreach (str_split($string) as $idx => $letter) {
        if ($matching == $limit) {
            return true;
        }

        if ($idx + $limit >= $len) {
            return false;
        }

        $ord = ord($letter);

        foreach (range(1, $limit) as $next) {
            if (chr($ord + $next) != $string[$idx + $next]) {
                $matching = 0;

                continue 2;
            }

            $matching ++;
        }
    }

    return true;
}

/**
 * Search for multiple values in a string.
 *
 * @param  array  $needles
 * @param  string  $haystack
 * @param  boolean  $strict
 * @return int|false
 */
function strpos_any(array $needles, string $haystack, bool $strict = false) : int|false
{
    foreach ($needles as $needle) {
        if (($idx = strpos($haystack, $needle, $strict)) !== false) {
            return $idx;
        }
    }

    return false;
}

/**
 * Find next password for given old password.
 *
 * @param  string  $oldPassword
 * @return string
 */
function findNextPassword(string $oldPassword): string
{
    $previousPassword = $oldPassword;

    $newPassword = null;

    $forbiddenLetters = ['i', 'o', 'l'];

    do {
        $possible = incrementString($previousPassword);

        // skip iterations if forbidden characters are found.
        if (($idx = strpos_any($forbiddenLetters, $possible)) !== false) {
            $possible[$idx] = incrementString($possible[$idx]);

            // Reset following characters to a because we skipped a letter
            if ($idx < 7) {
                foreach (range($idx + 1, 7) as $idx) {
                    $possible[$idx] = 'a';
                }
            }
        }

        $previousPassword = $possible;

        $letters = str_split($previousPassword);

        // Rule 1: Passwords must include one increasing straight of at least three letters
        if (! stringHasConsecutiveCharacters($possible, 2)) {
            continue;
        }

        // Rule 3: Passwords must contain at least two different, non-overlapping pairs of letters
        if (count(findRepeatingItems($letters)) < 2) {
            continue;
        }

        $newPassword = $possible;

    } while (is_null($newPassword));

    return $newPassword;
}

/**
 * Advent of Code 2015
 * Day 11: Corporate Policy
 * Part One
 *
 * @return string
 * @throws \Exception
 */
function aoc2015day11part1() : string
{
    return findNextPassword(getInput());
}

/**
 * Advent of Code 2015
 * Day 11: Corporate Policy
 * Part Two
 *
 * @return string
 * @throws \Exception
 */
function aoc2015day11part2() : string
{
    return findNextPassword(findNextPassword(getInput()));
}

if (basename(__FILE__) == basename($_SERVER["SCRIPT_FILENAME"])) {
    $password = aoc2015day11part1();
    $secondPassword = aoc2015day11part2();

    line("1. Santa's next password should be: $password");
    line("2. Santa's other next password should be: $secondPassword");
}
