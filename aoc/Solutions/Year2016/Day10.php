<?php

namespace Mike\AdventOfCode\Solutions\Year2016;

use Mike\AdventOfCode\Solutions\Solution;

/**
 * @method  array  getInput()  Get the instructions.
 */
class Day10 extends Solution
{
    /**
     * The example input to be used to test the solution.
     */
    protected ?string $exampleInput = <<<TXT
    value 5 goes to bot 2
    bot 2 gives low to bot 1 and high to bot 0
    value 3 goes to bot 1
    bot 1 gives low to output 1 and high to bot 0
    bot 0 gives low to output 2 and high to output 0
    value 2 goes to bot 2
    TXT;

    /**
     * The result of the solution.
     *
     * @var int[]
     */
    protected array $result = [];

    /**
     * Process the input from the challenge.
     */
    public function transformInput(string $input): array
    {
        return array_map(function ($i) {
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
        }, split_lines($input));
    }

    /**
     * Hook before all parts are run.
     */
    protected function before(): void
    {
        $this->io->info('Running simulation...');

        $instructions = $this->getInput();
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

            if ($this->testing && $low == 2 && $high == 5) {
                $this->result[] = $fromId;
            }

            if (! $this->testing && $low == 17 && $high == 61) {
                $this->result[] = $fromId;
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

        $this->result[] = $product;
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
}
