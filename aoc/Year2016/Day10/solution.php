<?php

/**
 * Get the instructions for the bots.
 *
 * @param  boolean  $example
 * @return array
 * @throws \Exception
 */
function getBotInstructions(bool $example = false): array
{
    $instructions = explode("\n", $example ? <<<TXT
    value 5 goes to bot 2
    bot 2 gives low to bot 1 and high to bot 0
    value 3 goes to bot 1
    bot 1 gives low to output 1 and high to bot 0
    bot 0 gives low to output 2 and high to output 0
    value 2 goes to bot 2
    TXT : getInput());

    $instructions = array_map(function ($i) {
        if ($i[0] == 'v') {
            preg_match('/^value\s+(\d+)\s+goes\s+to\s+bot\s+(\d+)$/i', $i, $matches);

            return [
                'from' => null,
                'to' => [
                    'value' => intval($matches[1]),
                    'target' => 'bot',
                    'id' => intval($matches[2]),
                ],
            ];
        }

        preg_match('/^bot\s+(\d+)\s+gives/i', $i, $tMatches);
        preg_match_all('/(high|low)\s+to\s+(output|bot)\s+(\d+)/i', $i, $matches, PREG_SET_ORDER);

        return [
            'from' => intval($tMatches[1]),
            'to' => array_map(fn($m) => [
                'value' => $m[1],
                'target' => $m[2],
                'id' => intval($m[3]),
            ], $matches),
        ];
    }, $instructions);

    // usort($instructions, function ($a, $b) {
    //     if (is_null($a['from']) && !is_null($b['from'])) {
    //         return -1;
    //     }

    //     if (!is_null($a['from']) && is_null($b['from'])) {
    //         return 1;
    //     }

    //     return 0;
    // });

    return $instructions;
}

/**
 * Advent of Code 2016
 * Day 10: Balance Bots
 *
 * Part One
 *
 * @return int
 */
function aoc2016day10(): int
{
    $instructions = getBotInstructions();

    $bots = [];
    $actions = [];
    $outputs = [];

    foreach ($instructions as $ins) {
        if (is_null($ins['from'])) {
            $bots[$ins['to']['id']][] = $ins['to']['value'];

            continue;
        }

        $actions[] = $ins;
    }

    while (count($actions)) {
        $action = array_shift($actions);
        $fromId = $action['from'];

        if (! isset($bots[$fromId]) || count($bots[$fromId]) != 2) {
            array_push($actions, $action);

            continue;
        }

        $from = &$bots[$fromId];
        [$low, $high] = $from;
        $from = [];

        if ($low == 17 && $high == 61) {
            line("1. The number of the bot comparing value-61 with value-17: $fromId.");
        }

        foreach ($action['to'] as $idx => $dest) {
            $target = &${$dest['target'] == 'bot' ? 'bots' : 'outputs'};

            if (! isset($target[$dest['id']])) {
                $target[$dest['id']] = [];
            }

            $target[$dest['id']][] = $dest['value'] == 'low' ? $low : $high;

            if ($dest['target'] == 'bot') {
                sort($target[$dest['id']]);
            }

            unset($target);
        }

        unset($from);
    }

    ksort($outputs);

    $outputs = array_column($outputs, 0);

    $product = array_product(array_slice($outputs, 0, 3));

    line("2. The product of output 0, 1 and 2 is: $product.");

    return 0;
}

if (basename(__FILE__) == basename($_SERVER["SCRIPT_FILENAME"])) {
    aoc2016day10();
}
