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
        'supernet' => implode(':-', $matches[1]),
        'hypernet' => implode(':-', array_filter($matches[2])),
    ];
}

/**
 * Get the IPv7 IPs.
 *
 * @param  boolean  $input  Override the input.
 * @return array
 * @throws \Exception
 */
function getIps(string $input = null): array
{
    return explode("\n", $input ?? getInput());
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

    return !hasAbba($parts['hypernet']) && hasAbba($parts['supernet']);
}

/**
 * Extract ABAs (Area-Broadcast Accessor) from given supernet.
 *
 * @param  string  $supernet
 * @return array
 */
function extractAbas(string $supernet): array
{
    $triplets = array_sliding(str_split($supernet), 3);

    return array_values(array_filter($triplets, fn(array $t) => $t[0] === $t[2] && $t[0] !== $t[1]));
}
/**
 * Extract BABs (Byte Allocation Block) from given hypernet.
 *
 * @param  string  $supernet
 * @param  array  $abas  The output of extractAbas
 * @return array
 */
function extractBabs(string $hypernet, array $abas): array
{
    $triplets = array_sliding(str_split($hypernet), 3);

    return array_values(array_filter($triplets, function ($t) use ($abas) {
        if ($t[0] !== $t[2] || $t[0] === $t[1]) {
            return false;
        }

        $aba = [$t[1], $t[0], $t[1]];

        return in_array($aba, $abas);
    }));
}

/**
 * Check to see if an IPv7 supports SSL (super-secret listening).
 *
 * @param  string  $ip
 * @return boolean
 */
function ipv7SupportsSsl(string $ip): bool
{
    $parts = parseIpv7($ip);

    $abas = extractAbas($parts['supernet']);

    if (count($abas) == 0) {
        return false;
    }

    $babs = extractBabs($parts['hypernet'], $abas);

    return count($babs) > 0;
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

/**
 * Advent of Code 2016
 * Day 7: Internet Protocol Version 7
 *
 * Part Two
 *
 * @return int
 */
function aoc2016day7part2(): int
{
    $ips = getIps();

    $ips = array_filter($ips, 'ipv7SupportsSsl');

    return count($ips);
}


if (basename(__FILE__) == basename($_SERVER["SCRIPT_FILENAME"])) {
    $tlsIps = aoc2016day7part1();
    $sslIps = aoc2016day7part2();

    line("1. The number of IPs that support TLS is: $tlsIps.");
    line("2. The number of IPs that support SSL is: $sslIps.");
}
