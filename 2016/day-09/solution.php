<?php

/**
 * Get the input file.
 *
 * @param  boolean  $example  Get the example input.
 * @return string
 * @throws \Exception
 */
function getFile(bool $example = false): string
{
    return $example ? <<<TXT
    ADVENT
    A(1x5)BC
    (3x3)XYZ
    A(2x2)BCD(2x2)EFG
    (6x1)(1x3)A
    X(8x2)(3x3)ABCY
    TXT : getInput();
}

/**
 * Decompress the given string using the algo provided by the challenge.
 *
 * @param  string  $input
 * @return string|integer
 */
function decompress(string $input, bool $countOnly = false, bool $improved = false): string|int
{
    $result = $countOnly ? 0 : '';

    for ($i = 0; $i < strlen($input); $i++) {
        $char = $input[$i];

        if ($char != '(') {
            if ($countOnly) {
                if (trim($char)) {
                    $result += 1;
                }

                continue;
            }

            $result .= $char;

            continue;
        }

        $end = stripos($input, ')', $i);

        [$len, $repeat] = array_map('intval', explode('x', substr($input, $i + 1, $end - $i)));

        $data = str_repeat(substr($input, $end+1, $len), $repeat);

        if ($countOnly) {
            $result += $improved ? decompress($data, $countOnly, $improved) : strlen(preg_replace('/[\s|\n|\r|\t|\v|\0]+/', '', $data));
        } else {
            $result .= $improved ? decompress($data, $countOnly, $improved) : $data;
        }

        $i = $end + $len;
    }

    return $result;
}

/**
 * Advent of Code 2016
 * Day 9: Explosives in Cyberspace
 *
 * Part One
 *
 * @return int
 */
function aoc2016day9part1(): int
{
    return decompress(getFile(), countOnly: true);
}

/**
 * Advent of Code 2016
 * Day 9: Explosives in Cyberspace
 *
 * Part Two
 *
 * @return int
 */
function aoc2016day9part2(): int
{
    // ! Takes very long time, needs some optimisation, lol!
    return decompress(getFile(), improved: true, countOnly: true);
}

if (basename(__FILE__) == basename($_SERVER["SCRIPT_FILENAME"])) {
    $length = aoc2016day9part1();
    line("1. The decompressed length is: $length.");

    $improved = aoc2016day9part2();
    line("2. The decompressed length with improved algo is: $improved.");
}
