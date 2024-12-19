<?php

namespace Mike\AdventOfCode\Solutions\Year2024;

use Mike\AdventOfCode\Solutions\Solution;

class Day19 extends Solution
{
    /**
     * The example input to be used to test the solution.
     */
    protected ?string $exampleInput = <<<TXT
    r, wr, b, g, bwu, rb, gb, br

    brwrr
    bggr
    gbbr
    rrbgbr
    ubwu
    bwurrg
    brgr
    bbrgwb
    TXT;

    /**
     * The cache for already seen patterns.
     *
     * @var array<int,bool>
     */
    protected array $cache = [];

    /**
     * Process the input from the challenge.
     *
     * @return array{string[],string[]}
     */
    public function transformInput(string $input): array
    {
        $designs = split_lines($input);
        $patterns = explode(', ', array_shift($designs));

        return [$patterns, $designs];
    }

    /**
     * Render the given design in the cli.
     */
    protected function render(string $design): void
    {
        if (! $this->getIO()->getOutput()->isVerbose()) {
            return;
        }

        $colorMap = [
            'w' => 'white',
            'u' => 'blue',
            'b' => 'black',
            'r' => 'red',
            'g' => 'green',
        ];

        $design = array_map(fn($d) => '<fg=' . $colorMap[$d] . ">$d</>", str_split($design));

        $this->getIO()->getOutput()->writeln(implode('', $design));
    }

    /**
     * Check if given design can be made from the given list of patterns.
     *
     * @param  string  $design
     * @param  string[]  $patterns
     * @param  integer  $start  The starting position of the design.
     * @param  boolean  $countOptions  Wether to return the count of all possible options, instead of just boolean.
     * @return boolean|integer  Integer if $countOptions is true else boolean
     */
    protected function canMake(string $design, array $patterns, int $start = 0, bool $countOptions = false): bool|int
    {
        if ($start === 0) {
            $this->cache = [];
        }

        $len = strlen($design);

        if ($start === $len) {
            return $countOptions ? 1 : true;
        }

        if (isset($this->cache[$start])) {
            return $this->cache[$start];
        }

        $total = 0;

        foreach ($patterns as $pattern) {
            $pLen = strlen($pattern);

            if (substr($design, $start, $pLen) === $pattern) {
                if ($nested = $this->canMake($design, $patterns, $start + $pLen, $countOptions)) {
                    if (! $countOptions) {
                        return $this->cache[$start] = true;
                    }

                    $total += $nested;
                }
            }
        }

        return $this->cache[$start] = $countOptions ? $total : false;
    }

    /**
     * Run the first part of the challenge.
     */
    public function part1(): int
    {
        [$patterns, $designs] = $this->getInput();

        return count(array_filter($designs, fn($d) => $this->canMake($d, $patterns)));
    }

    /**
     * Run the second part of the challenge.
     */
    public function part2(): int
    {
        [$patterns, $designs] = $this->getInput();

        return array_sum(array_map(fn($d) => $this->canMake($d, $patterns, countOptions: true), $designs));
    }
}
