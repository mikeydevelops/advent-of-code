<?php

require_once __DIR__ . '/../../common.php';

$boss = null;

/**
 * Make a new instance of the boss.
 *
 * @return array
 * @throws \Exception
 */
function makeBoss(): array
{
    global $boss;

    if (is_null($boss)) {
        $boss = [];

        $input = str_replace('Hit Points', 'hp', getInput());

        foreach (explode("\n", $input) as $line) {
            [$stat, $value] = explode(': ', $line);

            $boss[strtolower($stat)] = intval($value);
        }
    }

    return $boss;
}

/** Apply effect to given state. */
function applyEffects(array $state, string $turn = 'player'): array
{
    foreach ($state['spells'] as &$spell) {
        $state['bossHealth'] -= $spell['damage'];
        $state['mana'] += $spell['mana'];
        $state['health'] += $spell['health'];

        if ($spell['armor'] && $turn == 'boss') {
            $state['health'] += 7;
        }

        $spell['duration'] -= 1;

        unset($spell);
    }

    $state['spells'] = array_filter($state['spells'], function ($spell) {
        return $spell['duration'] > 0;
    });

    return $state;
}

/** Simulate fight. */
function battle(bool $hard = false): int
{
    $spells = [
        ['Magic Missile',   53,  4, 0, 0,   0, 0],
        ['Drain',           73,  2, 2, 0,   0, 0],
        ['Shield',          113, 0, 0, 7,   0, 6],
        ['Poison',          173, 3, 0, 0,   0, 6],
        ['Recharge',        229, 0, 0, 0, 101, 5],
    ];

    $spells = array_map(function ($stats) {
        return array_combine(
            ['name', 'cost', 'damage', 'health', 'armor', 'mana', 'duration'],
            $stats,
        );
    }, $spells);

    $boss = makeBoss();

    $probabilities = [
        [
            'mana' => 500,
            'health' => 50,
            'bossHealth' => $boss['hp'],
            'spent' => 0,
            'spells' => [],
        ],
    ];

    $spent = 99999;

    while (! empty($probabilities)) {
        $state = array_pop($probabilities);

        $state['health'] -= intval($hard);

        if ($state['health'] <= 0) {
            continue;
        }

        $state = applyEffects($state, turn: 'player');

        if ($state['bossHealth'] <= 0) {
            $spent = min($spent, $state['spent']);

            continue;
        }

        foreach ($spells as $spell) {
            if ($spell['cost'] > $state['mana']) {
                continue;
            }

            if ($state['spent'] + $spell['cost'] > $spent) {
                continue;
            }

            if (isset($state['spells'][$spell['name']])) {
                continue;
            }

            $newState = $state;

            $newState['mana'] -= $spell['cost'];
            $newState['spent'] += $spell['cost'];
            $newState['spells'][$spell['name']] = $spell;

            $newState = applyEffects($newState, turn: 'boss');

            if ($newState['bossHealth'] <= 0) {
                $spent = min($spent, $newState['spent']);

                continue;
            }

            $newState['health'] -= $boss['damage'];

            if ($newState['health'] <= 0) {
                continue;
            }

            array_push($probabilities, $newState);
        }
    }

    return $spent;
}

/**
 * Advent of Code 2015
 * Day 22: Wizard Simulator 20XX
 * Part One
 *
 * @return integer
 */
function aoc2015day22part1(): int
{
    return battle();
}

/**
 * Advent of Code 2015
 * Day 22: Wizard Simulator 20XX
 * Part Two
 *
 * @return integer
 */
function aoc2015day22part2(): int
{
    return battle(hard: true);
}

if (basename(__FILE__) == basename($_SERVER["SCRIPT_FILENAME"])) {
    $leastMana = aoc2015day22part1();
    $leastManaHard = aoc2015day22part2();

    line("1. The least amount of mana spent is: $leastMana.");
    line("2. The least amount of mana spent on hard mode is: $leastManaHard.");
}
