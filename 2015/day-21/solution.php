<?php

require_once __DIR__ . '/../../common.php';

$shop = null;

/**
 * Get the boss stats.
 *
 * @return array
 * @throws \Exception
 */
function getBoss(): array
{
    $boss = [];

    $input = str_replace('Hit Points', 'hp', getInput());

    foreach (explode("\n", $input) as $line) {
        [$stat, $value] = explode(': ', $line);

        $boss[strtolower($stat)] = intval($value);
    }

    return $boss;
}

/**
 * Get the shop items.
 *
 * @param  string|null  $category
 * @return array
 */
function getShop(string $category = null): array
{
    global $shop;

    if (! isset($shop)) {
        $shop = json_decode(file_get_contents(__DIR__ . '/shop.json'), true);
    }

    if (isset($category)) {
        return $shop[$category] ?? throw new Exception("Unknown shop category [$category].");
    }

    return $shop;
}

/**
 * Get the available weapons.
 *
 * @param  string|null  $weapon
 * @return array
 */
function getWeapons(string $weapon = null): array
{
    $weapons = getShop('weapons');

    if (isset($weapon)) {
        return $weapons[$weapon] ?? throw new Exception("Unknown weapon [$weapon].");
    }

    return $weapons;
}

/**
 * Get the available armor.
 *
 * @param  string|null  $name
 * @return array
 */
function getArmor(string $name = null): array
{
    $armor = getShop('armor');

    if (isset($name)) {
        return $armor[$name] ?? throw new Exception("Unknown armor [$name].");
    }

    return $armor;
}

/**
 * Get the available rings.
 *
 * @param  string|null  $ring
 * @return array
 */
function getRings(string $ring = null): array
{
    $rings = getShop('rings');

    if (isset($ring)) {
        return $rings[$ring] ?? throw new Exception("Unknown ring [$ring].");
    }

    return $rings;
}

/**
 * Add shop item statistics together to the first item.
 *
 * @param  array  $first
 * @param  array  $items,...
 * @return array
 */
function addStats(array $first, array ...$items)
{
    foreach ($items as $item) {
        $first['cost'] = ($first['cost'] ?? 0) + $item['cost'];
        $first['damage'] = ($first['damage'] ?? 0) + $item['damage'];
        $first['armor'] = ($first['armor'] ?? 0) + $item['armor'];
    }

    return $first;
}

/**
 * Generate player inventory scenarios.
 *
 * @return \Generator
 * @throws \Exception
 */
function getScenarios(): Generator
{
    $parameters = [
        'weapon' => array_keys(getWeapons()),
        'armor' => array_merge([False], array_keys(getArmor())),
        'ring1' => array_merge([False], array_keys(getRings())),
        'ring2' => array_merge([False], array_keys(getRings())),
    ];

    $keys = array_keys($parameters);
    $values = array_values($parameters);

    foreach (array_combinations($values) as $comb) {
        $comb = array_combine($keys, $comb);

        // cannot have 2 of the same ring.
        if ($comb['ring1'] && $comb['ring1'] == $comb['ring2']) {
            continue;
        }

        $scenario = [
            'armor' => 0,
            'hp' => 100,
            'damage' => 0,
            'cost' => 0,
        ];

        $comb['weapon'] = getWeapons($comb['weapon']);
        $comb['armor'] = $comb['armor'] ? getArmor($comb['armor']) : false;
        $comb['ring1'] = $comb['ring1'] ? getRings($comb['ring1']) : false;
        $comb['ring2'] = $comb['ring2'] ? getRings($comb['ring2']) : false;

        $scenario = addStats($scenario, ...array_values(array_filter($comb)));

        yield $scenario;
    }
}

/**
 * Simulate fighting returning true if player1 wins or false if player2 wins.
 *
 * @param  array  $player1
 * @param  array  $player2
 * @return boolean
 */
function fight(array $player1, array $player2): bool
{
    $turn = false; // false = player1, true = player2

    do {
        $attacker = &${$turn ? 'player2' : 'player1'};
        $defender = &${$turn ? 'player1' : 'player2'};

        $damage = $attacker['damage'] - $defender['armor'];
        $damage = $damage < 1 ? 1 : $damage;

        $defender['hp'] -= $damage;

        $turn = !$turn;
    } while($player1['hp'] > 0 && $player2['hp'] > 0);

    return $player1['hp'] > 0 && $player2['hp'] <= 0;
}

/**
 * Advent of Code 2015
 * Day 8: Matchsticks
 * Part One
 *
 * @return integer
 * @throws \Exception
 */
function aoc2015day21part1(): int
{
    $boss = getBoss();

    $costs = [];

    foreach (getScenarios() as $player) {
        if (fight($player, $boss)) {
            $costs[] = $player['cost'];
        }
    }

    return min($costs);
}

if (basename(__FILE__) == basename($_SERVER["SCRIPT_FILENAME"])) {
    $gold = aoc2015day21part1();

    line("1. The cheapest the boss died for is: $gold");
}
