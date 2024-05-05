<?php

namespace Mike\AdventOfCode\Solutions\Year2015;

use Generator;
use Mike\AdventOfCode\Solutions\Solution;
use Mike\AdventOfCode\Solutions\Year2015\Day21\Characters\Boss;
use Mike\AdventOfCode\Solutions\Year2015\Day21\Characters\Player;
use Mike\AdventOfCode\Solutions\Year2015\Day21\Game;
use Mike\AdventOfCode\Solutions\Year2015\Day21\Inventory\Items\Item;

/**
 * @method  \Mike\AdventOfCode\Solutions\Year2015\Day21\Characters\Boss  getInput()  Get the boss.
 */
class Day21 extends Solution
{
    /**
     * The example input to be used to test the solution.
     */
    protected ?string $exampleInput = <<<TXT
    Hit Points: 12
    Damage: 7
    Armor: 2
    TXT;

    /**
     * Cached initial properties of the boss.
     * Because there will be many iterations, no need to read,
     * and parse the info every single time.
     */
    protected array $boss = [];

    /**
     * The results of the battle.
     */
    protected array $result;

    /**
     * Process the input from the challenge.
     */
    public function transformInput(string $input): Boss
    {
        $boss = $this->boss;

        if (empty($boss)) {
            $boss = [];

            $input = str_replace('Hit Points', 'hp', $input);

            foreach (split_lines($input) as $line) {
                [$stat, $value] = explode(': ', $line);

                $boss[strtolower($stat)] = intval($value);
            }

            $this->boss = $boss;
        }

        return new Boss($boss['hp'], $boss['armor'], $boss['damage']);
    }

    /**
     * Hook before all parts are run.
     */
    protected function before(): void
    {
        $this->io->info('War machine is running...');
        $this->io->newLine();

        $winCosts = [];
        $loseCosts = [];

        foreach ($this->getScenarios() as $game) {
            $winner = $game->run();
            $loser = $game->getLoser();

            if ($winner instanceof Player) {
                $winCosts[] = $winner->getInventory()->getCost();
            }

            else if ($loser instanceof Player) {
                $loseCosts[] = $loser->getInventory()->getCost();
            }
        }

        $this->result = [min($winCosts), max($loseCosts)];
    }

    /**
     * Run the first part of the challenge.
     */
    public function part1(): int
    {
        return $this->result[0];
    }

    /**
     * Run the second part of the challenge.
     */
    public function part2(): int
    {
        return $this->result[1];
    }

    /**
     * Generate player inventory scenarios.
     *
     * @return \Generator<int,\Mike\AdventOfCode\Year2015\Day21\Game>
     * @throws \Exception
     */
    public function getScenarios(): Generator
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
            $player = new Player(
                $this->testing ? 8 : 100,
                $this->testing ? 5 : 0,
                $this->testing ? 5 : 0,
            );

            $player->setShop($game->getShop());

            $player->buy($comb['weapon']);
            $comb['armor'] ? $player->buy($comb['armor']) : false;
            $comb['ring1'] ? $player->buy($comb['ring1']) : false;
            $comb['ring2'] ? $player->buy($comb['ring2']) : false;

            $game->setPlayer1($player);
            $game->setPlayer2($this->getInput());

            yield $game;
        }
    }
}
