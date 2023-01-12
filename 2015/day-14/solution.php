<?php

require_once __DIR__ . '/../../common.php';

function getDeerStats() : array
{
    $deer = [];

    // remove unused strings
    $input = str_replace(
        ['can fly ', 'km/s for ', 'seconds, but then must rest for ', ' seconds.'],
        '',
        getInput()
    );

    foreach (explode("\n", $input) as $line) {
        [$name, $speed, $stamina, $rest] = explode(' ', $line);

        $speed = intval($speed);
        $stamina = intval($stamina);
        $rest = intval($rest);

        $deer[$name] = compact('name', 'speed', 'stamina', 'rest');
    }

    return $deer;
}

/**
 * Advent of Code 2015
 * Day 14: Reindeer Olympics
 * Part One
 *
 * @return array{string,int}
 * @throws \Exception
 */
function aoc2015day14part1(): array
{
    $deer = getDeerStats();
    $initial = $deer;

    $distances = array_combine(array_keys($deer), array_fill(0, count($deer), 0));

    foreach (range(1, 2503) as $time) {
        foreach ($deer as $name => &$d) {
            if ($d['stamina']) {
                $d['stamina'] --;
                $distances[$name] += $d['speed'];

                continue;
            }

            $d['rest'] --;

            if ($d['rest'] == 0) {
                $d['stamina'] = $initial[$name]['stamina'];
                $d['rest'] = $initial[$name]['rest'];
            }
        }
    }

    $winnerDistance = max($distances);
    $winner = array_search($winnerDistance, $distances);

    return [$winner, $winnerDistance];
}

if (basename(__FILE__) == basename($_SERVER["SCRIPT_FILENAME"])) {
    [$winningDeerName, $distance] = aoc2015day14part1();

    line("1. Winning deer, $winningDeerName, traveled: $distance km");
}
