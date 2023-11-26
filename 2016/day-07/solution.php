<?php

/**
 * Parse a string into the parts that make an IPv7
 *
 * @param  string  $ip
 * @return array
 */
function parseIpv7(string $ip): array
{
    preg_match_all('/([a-z]+)(?:\[([a-z]+)\])?/i', $ip, $matches);

    return [
        'string' => implode(':-', $matches[1]),
        'hypernet' => implode(':-', array_filter($matches[2])),
    ];
}

/**
 * Get the IPv7 IPs.
 *
 * @param  boolean  $example  Get the example input.
 * @return array
 * @throws \Exception
 */
function getIps(bool $example = false): array
{
    return explode("\n", $example ? <<<TXT
    abba[mnop]qrst
    abcd[bddb]xyyx
    aaaa[qwer]tyui
    ioxxoj[asdfgh]zxcvbn
    TXT : getInput());
}

/**
 * Check to see if a string has Autonomous Bridge Bypass Annotation, or ABBA.
 *
 * @param  string  $string
 * @return boolean
 */
function hasAbba(string $string): bool
{
    $pairs = array_sliding(str_split($string));

    foreach ($pairs as $idx => $pair) {
        if (count(array_unique($pair)) != 2 || !isset($pairs[$idx + 2])) {
            continue;
        }

        if ($pairs[$idx + 2] === array_reverse($pair)) {
            return true;
        }
    }

    return false;
}

/**
 * Check to see if an IPv7 supports TLS (transport-layer snooping).
 *
 * @param  string  $ip
 * @return boolean
 */
function ipv7SupportsTls(string $ip): bool
{
    $parts = parseIpv7($ip);

    return !hasAbba($parts['hypernet']) && hasAbba($parts['string']);
}

/**
 * Advent of Code 2016
 * Day 7: Internet Protocol Version 7
 *
 * Part One
 *
 * @return int
 */
function aoc2016day7part1(): int
{
    $ips = getIps();

    $ips = array_filter($ips, 'ipv7SupportsTls');

    return count($ips);
}


if (basename(__FILE__) == basename($_SERVER["SCRIPT_FILENAME"])) {
    $tlsIps = aoc2016day7part1();

    line("1. The number of IPs that support TLS is: $tlsIps.");
}
