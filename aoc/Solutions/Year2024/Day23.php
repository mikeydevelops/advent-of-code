<?php

namespace Mike\AdventOfCode\Solutions\Year2024;

use Mike\AdventOfCode\Solutions\Solution;

class Day23 extends Solution
{
    /**
     * The example input to be used to test the solution.
     */
    protected ?string $exampleInput = <<<TXT
    kh-tc
    qp-kh
    de-cg
    ka-co
    yn-aq
    qp-ub
    cg-tb
    vc-aq
    tb-ka
    wh-tc
    yn-cg
    kh-ub
    ta-co
    de-co
    tc-td
    tb-wq
    wh-td
    ta-ka
    td-qp
    aq-cg
    wq-ub
    ub-vc
    de-ta
    wq-aq
    wq-vc
    wh-yn
    ka-de
    kh-ta
    co-tc
    wh-qp
    tb-vc
    td-yn
    TXT;

    /**
     * Process the input from the challenge.
     */
    public function transformInput(string $input): array
    {
        return split_lines($input, map: fn($l) => explode('-', $l, 2));
    }

    /**
     * Index the connections to an array of computer => [...related computers]
     *
     * @param  array  $connections
     * @param  boolean  $sort
     * @return array<string,string[]>
     */
    protected function indexComputers(array $connections, bool $sort = false): array
    {
        $index = [];

        foreach ($connections as [$c1, $c2]) {
            !isset($index[$c1]) && $index[$c1] = [];
            !isset($index[$c2]) && $index[$c2] = [];

            !in_array($c2, $index[$c1]) && $index[$c1][] = $c2;
            !in_array($c1, $index[$c2]) && $index[$c2][] = $c1;
        }

        if ($sort) {
            $index = array_map(function ($pc) {
                sort($pc);

                return $pc;
            }, $index);

            ksort($index);
        }

        return $index;
    }

    /**
     * Group computers by their connections.
     *
     * @param  array<string,string[]>  $computers
     * @param  integer  $limit
     * @return array<integer,string[]>
     */
    protected function groupByConnections(array $computers, int $limit): array
    {
        $result = [];

        if ($limit < 2) {
            return $result;
        }

        foreach ($computers as $c1 => $connections) {
            if ($limit === 2) {
                foreach ($connections as $connection) {
                    $c = [$c1, $connection];
                    sort($c);
                    $k = implode('', $c);

                    if (! isset($result[$k])) {
                        $result[$k] = $c;
                    }
                }

                continue;
            }

            foreach (combinations($connections, $limit - 1) as $comb) {
                $c = [$c1, ...$comb];
                sort($c);
                $k = implode('', $c);

                if (isset($result[$k])) {
                    continue;
                }

                if ($limit === 3 && in_array($comb[0], $computers[$comb[1]])) {
                    $result[$k] = $c;
                    continue;
                }

                // all pcs in combination must have connection to each other.
                foreach (combinations($comb, 2) as $cmb) {
                    // checking one side only because, they should be connected
                    if (! in_array($cmb[0], $computers[$cmb[1]])) {
                        continue 2;
                    }
                }

                $result[$k] = $c;
            }
        }

        return array_values($result);
    }

    /**
     * Run the first part of the challenge.
     */
    public function part1(): int
    {
        $index = $this->indexComputers($this->getInput());

        $groups = $this->groupByConnections($index, 3);

        $groups = array_filter($groups, function ($group) {
            return array_filter($group, fn ($pc) => substr($pc, 0, 1) === 't');
        });

        return count($groups);
    }

    /**
     * Run the second part of the challenge.
     */
    public function part2(): string
    {
        $index = $this->indexComputers($this->getInput());

        $groups = $this->groupByConnections($index, max(array_map('count', $index)));

        return implode(',', $groups[0]);
    }
}
