<?php

/**
 * Get the corrupted messages.
 *
 * @param  boolean  $example
 * @return array
 * @throws \Exception
 */
function getMessages(bool $example = false): array
{
    $messages = explode("\n", $example ? <<<TXT
    eedadn
    drvtee
    eandsr
    raavrd
    atevrs
    tsrnev
    sdttsa
    rasrtv
    nssdts
    ntnada
    svetve
    tesnvt
    vntsnd
    vrdear
    dvrsen
    enarar
    TXT : getInput());

    return array_map('str_split', $messages);
}

/**
 * Advent of Code 2016
 * Day 6: Signals and Noise
 *
 * Part One
 *
 * @return string
 */
function aoc2016day6part1(): string
{
    $messages = getMessages(example: false);
    $len = count($messages[0]);
    $corrected = '';

    for ($i = 0; $i < $len; $i ++) {
        $ranks = array_count_values(array_column($messages, $i));

        arsort($ranks);

        $chars = array_keys($ranks);

        $corrected .= array_shift($chars);
    }

    return $corrected;
}

if (basename(__FILE__) == basename($_SERVER["SCRIPT_FILENAME"])) {
    $corrected = aoc2016day6part1();

    line("1. The corrected message is: $corrected.");
}
