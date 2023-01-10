<?php

require_once __DIR__ . '/../../common.php';

/**
 * Advent of Code 2015
 * Day 12: JSAbacusFramework.io
 * Part One
 *
 * @return integer
 * @throws \Exception
 */
function aoc2015day12part1(): int
{
    $document = getInput();

    preg_match_all('/[+-]?((\d*\.?\d+)|(\d+\.?\d*))/', $document, $matches);

    return array_sum($matches[0]);
}

/**
 * Remove all associative arrays with value red.
 *
 * @param  mixed  $child
 * @return mixed
 */
function removeReds($child)
{
    if (! is_array($child)) {
        return $child;
    }

    if (array_is_assoc($child) && in_array('red', $child)) {
        return 0;
    }

    foreach ($child as $idx => $item) {
        if (! is_array($item)) {
            continue;
        }

        if (array_is_assoc($item) && in_array('red', $item)) {
            $child[$idx] = 0;

            continue;
        }

        $child[$idx] = array_map('removeReds', $item);
    }

    return $child;
}

/**
 * Remove all red children from the document.
 *
 * @param  string  $document
 * @return string
 */
function sanitizeDocument(string $document) : string
{
    $document = json_decode($document, true);

    if (! $document) {
        return '';
    }

    $document = array_map('removeReds', $document);

    return json_encode($document, JSON_PRETTY_PRINT);
}

/**
 * Advent of Code 2015
 * Day 12: JSAbacusFramework.io
 * Part Two
 *
 * @return integer
 * @throws \Exception
 */
function aoc2015day12part2(): int
{
    $document = getInput();

    $sanitized = sanitizeDocument($document);

    preg_match_all('/[+-]?((\d*\.?\d+)|(\d+\.?\d*))/', $sanitized, $matches);

    return array_sum($matches[0]);
}

if (basename(__FILE__) == basename($_SERVER["SCRIPT_FILENAME"])) {
    $sum = aoc2015day12part1();
    $sum2 = aoc2015day12part2();

    line("1. The total sum of all numbers is : $sum");
    line("2. The total sum of all numbers without red is: $sum2");
}
