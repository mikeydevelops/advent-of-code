<?php

namespace Mike\AdventOfCode\Year2015\Day21;

use Mike\AdventOfCode\Year2015\Day21\Characters\Boss;
use Mike\AdventOfCode\Year2015\Day21\Characters\Player;
use Mike\AdventOfCode\Year2015\Day21\Game;
use Mike\AdventOfCode\Year2015\Day21\Inventory\Items\Item;

$boss = null;

/**
 * Make a new instance of the boss.
 *
 * @return \Mike\AdventOfCode\Year2015\Day21\Characters\Boss
 * @throws \Exception
 */
function makeBoss(): Boss
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

    return new Boss($boss['hp'], $boss['armor'], $boss['damage']);
}

/**
 * Generate player inventory scenarios.
 *
 * @return \Generator<int,\Mike\AdventOfCode\Year2015\Day21\Game>
 * @throws \Exception
 */
function getScenarios(): \Generator
{
    $parameters = [
        'weapon' => Item::listWeapons(),
        'armor' => array_merge([false], Item::listArmor()),
        'ring1' => array_merge([false], Item::listRings()),
        'ring2' => array_merge([false], Item::listRings()),
    ];

    $keys = array_keys($parameters);
    $values = array_values($parameters);

    foreach (array_combinations($values) as $comb) {
        $comb = array_combine($keys, $comb);

        // cannot have 2 of the same ring.
        if ($comb['ring1'] && $comb['ring1'] == $comb['ring2']) {
            continue;
        }

        $game = new Game;
        $player = new Player;

        $player->setShop($game->getShop());

        $player->buy($comb['weapon']);
        $comb['armor'] ? $player->buy($comb['armor']) : false;
        $comb['ring1'] ? $player->buy($comb['ring1']) : false;
        $comb['ring2'] ? $player->buy($comb['ring2']) : false;

        $game->setPlayer1($player);
        $game->setPlayer2(makeBoss());

        yield $game;
    }
}

/**
 * Advent of Code 2015
 * Day 21: RPG Simulator 20XX
 *
 * @return integer[]
 * @throws \Exception
 */
function aoc2015day21(): array
{
    $winCosts = [];
    $loseCosts = [];

    foreach (getScenarios() as $game) {
        $winner = $game->run();
        $loser = $game->getLoser();

        if ($winner instanceof Player) {
            $winCosts[] = $winner->getInventory()->getCost();
        }

        else if ($loser instanceof Player) {
            $loseCosts[] = $loser->getInventory()->getCost();
        }
    }

    return [min($winCosts), max($loseCosts)];
}

if (basename(__FILE__) == basename($_SERVER["SCRIPT_FILENAME"])) {
    [$winCosts, $loseCosts] = aoc2015day21();

    line("1. The least amount of gold spent is: $winCosts");
    line("2. The most amount of gold spent is: $loseCosts");
}
