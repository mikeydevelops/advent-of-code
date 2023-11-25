<?php

/**
 * Parse a room from a string.
 *
 * @param  string  $room
 * @return array
 */
function parseRoom(string $room): array
{
    preg_match('/^([a-z\-]+)\-(\d+)\[([a-z]+)\]$/i', $room, $matches);

    return [
        'name' => $matches[1],
        'sector' => intval($matches[2]),
        'checksum' => $matches[3],
    ];
}

/**
 * Get the input rooms.
 *
 * @return array
 */
function getRooms(bool $example = false): array
{
    $rooms = explode("\n", $example ? <<<TXT
    aaaaa-bbb-z-y-x-123[abxyz]
    a-b-c-d-e-f-g-h-987[abcde]
    not-a-real-room-404[oarel]
    totally-real-room-200[decoy]
    TXT : getInput());

    return array_map('parseRoom', $rooms);
}

/**
 * Check to see if given room is real or a decoy.
 *
 * @param  array  $room
 * @return boolean
 */
function isRealRoom(array $room): bool
{
    return $room['checksum'] === getRoomChecksum($room['name']);
}

/**
 * Calculate room checksum from name.
 *
 * @param  string  $name
 * @return string
 */
function getRoomChecksum(string $name): string
{
    $chars = str_split(str_replace('-', '', $name));

    $ranks = array_count_values($chars);

    if (count($ranks) < 5) {
        return '';
    }

    $ranks = array_map(function ($rank, $char) {
        return compact('rank', 'char');
    }, $ranks, array_keys($ranks));

    usort($ranks, function ($a, $b) {
        // first sort by rank descending
        if ($a['rank'] > $b['rank']) return -1;
        if ($a['rank'] < $b['rank']) return 1;

        // then sort by char ascending if rank is same
        if ($a['char'] > $b['char']) return 1;
        if ($a['char'] < $b['char']) return -1;

        return 0;
    });

    $mostCommon = array_slice(array_column($ranks, 'char'), 0, 5);

    return implode('', $mostCommon);
}

/**
 * Advent of Code 2016
 * Day 4: Security Through Obscurity
 *
 * Part One
 *
 * @return int
 */
function aoc2016day4part1(): int
{
    $rooms = getRooms();

    $realRooms = array_filter($rooms, 'isRealRoom');

    return array_sum(array_column($realRooms, 'sector'));
}

if (basename(__FILE__) == basename($_SERVER["SCRIPT_FILENAME"])) {
    $real = aoc2016day4part1();

    line("1. The real rooms are: $real.");
}
