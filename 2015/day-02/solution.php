<?php

require_once __DIR__ . '/../../common.php';

/**
 * Parse the dimensions of a box size string.
 *
 * @param  string  $size
 * @return integer[]
 */
function parseBoxSize(string $size) : array
{
    list($length, $width, $height) = array_map('intval', explode('x', $size));

    return compact('length', 'width', 'height');
}

/**
 * Advent of Code 2015
 * Day 2: I Was Told There Would Be No Math
 *
 * @return integer[]
 * @throws \Exception
 */
function aoc2015day2()
{
    $input = getInput();

    $sizes = explode_trim("\n", $input);

    $sizes = array_map('parseBoxSize', $sizes);

    $wrappingPaper = 0;
    $ribbon = 0;

    foreach ($sizes as $box) {
        $sides = [
            $box['length'] * $box['width'],
            $box['width'] * $box['height'],
            $box['height'] * $box['length'],
        ];

        $area = (2 * $sides[0]) + (2 * $sides[1]) + (2 * $sides[2]);

        $extra = min($sides);

        $wrappingPaper += $area + $extra;

        $volume = $box['length'] * $box['width'] * $box['height'];

        $perimeters = [
            (2 * $box['length']) + (2 * $box['width']),
            (2 * $box['width']) + (2 * $box['height']),
            (2 * $box['height']) + (2 * $box['length']),
        ];

        $perimeter = min($perimeters);

        $ribbon += $perimeter + $volume;
    }

    return [$wrappingPaper, $ribbon];
}

if (basename(__FILE__) == basename($_SERVER["SCRIPT_FILENAME"])) {
    list($wrappingPaper, $ribbon) = aoc2015day2();

    line("1. The elves should order $wrappingPaper sqft of wrapping paper.");
    line("2. The elves should order $ribbon sqft of ribbon.");
}
