<?php

namespace Mike\AdventOfCode\Solutions\Year2015;

use Mike\AdventOfCode\Solutions\Solution;

/**
 * @method  array  getInput()  Get the molecule and the required replacements.
 */
class Day19 extends Solution
{
    /**
     * The example input to be used to test the solution.
     */
    protected ?string $exampleInput = <<<TXT
    H => HO
    H => OH
    O => HH

    HOH
    TXT;

    /**
     * Process the input from the challenge.
     */
    public function transformInput(string $input): array
    {
        [$replacements, $molecule] = explode("\n\n", trim($input));
        $replacements = split_lines($replacements);
        $map = [];

        foreach ($replacements as $replacement) {
            [$search, $replace] = explode(' => ', $replacement);

            $map[$search][] = $replace;
        }

        return [$molecule, $map];
    }

    /**
     * Run the first part of the challenge.
     */
    public function part1(): int
    {
        [$molecule, $replacements] = $this->getInput();

        $molecules = [];

        foreach (str_split($molecule) as $i => $element) {
            foreach ($replacements as $search => $rep) {
                $searchLen = strlen($search);

                foreach ($rep as $replacement) {
                    if (substr($molecule, $i, $searchLen) != $search) {
                        continue;
                    }

                    $new = substr_replace($molecule, $replacement, $i, $searchLen);

                    $molecules[$new] = 0;
                }
            }
        }

        return count($molecules);
    }

    /**
     * Run the second part of the challenge.
     */
    public function part2(): int
    {
        [$molecule, $replacements] = $this->getInput(example: <<<TXT
        e => H
        e => O
        H => HO
        H => OH
        O => HH

        HOH
        TXT);
        $replacements = $this->reverseReplacements($replacements);

        $steps = 0;

        $target = $molecule;

        do {
            foreach ($replacements as $search => $replace) {
                if (strpos($target, $search) === false) {
                    continue;
                }

                $target = preg_replace('/' . $search . '/', $replace, $target, 1);

                $steps++;
            }
        } while ($target != 'e');

        return $steps;
    }

    /**
     * Reverse the replacements.
     */
    public function reverseReplacements(array $replacements): array
    {
        $reversed = [];

        foreach ($replacements as $search => $rep) {
            $reversed = array_merge($reversed, array_combine($rep, array_fill(0, count($rep), $search)));
        }

        return $reversed;
    }
}
