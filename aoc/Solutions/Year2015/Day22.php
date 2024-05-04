<?php

namespace Mike\AdventOfCode\Solutions\Year2015;

use Mike\AdventOfCode\Solutions\Solution;

/**
 * @method  array  getBoss()  Get the boss.
 */
class Day22 extends Solution
{
    /**
     * The example input to be used to test the solution.
     */
    protected ?string $exampleInput = <<<TXT
    Hit Points: 13
    Damage: 8
    TXT;

    /**
     * Process the input from the challenge.
     */
    public function transformInput(string $input): array
    {
        $boss = [];

        $input = str_replace('Hit Points', 'hp', trim($input));

        foreach (explode("\n", $input) as $line) {
            [$stat, $value] = explode(': ', $line);

            $boss[strtolower($stat)] = intval($value);
        }

        return $boss;
    }

    /**
     * Run the first part of the challenge.
     */
    public function part1(): int
    {
        return $this->battle();
    }

    /**
     * Run the second part of the challenge.
     */
    public function part2(): int
    {
        return $this->battle(hard: true);
    }

    /**
     * Simulate fight.
     */
    public function battle(bool $hard = false): int
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

        $boss = $this->getInput();

        $probabilities = [
            [
                'mana' => $this->testing ? 250 : 500,
                'health' => $this->testing ? 10 : 50,
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

            $state = $this->applyEffects($state, turn: 'player');

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

                $newState = $this->applyEffects($newState, turn: 'boss');

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
     * Apply effect to given state.
     */
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
}
