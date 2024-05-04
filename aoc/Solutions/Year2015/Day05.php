<?php

namespace Mike\AdventOfCode\Solutions\Year2015;

use Mike\AdventOfCode\Solutions\Solution;

/**
 * @method  string[]  getInput()  Santa's strings.
 */
class Day05 extends Solution
{
    /**
     * The example input to be used to test the solution.
     */
    protected ?string $exampleInput = <<<TXT
    ugknbfddgicrmopn
    aaa
    jchzalrnumimnmhp
    haegwjzuvuyypxyu
    dvszwmarrgswjxmb
    qjhvhtzxzqqjkmpb
    xxyxx
    uurcxstgmygtbstg
    ieodomkazucvgmuy
    TXT;

    /**
     * Process the input from the challenge.
     *
     * @return string[]
     */
    public function transformInput(string $input): array
    {
        return explode_trim("\n", $input);;
    }

    /**
     * Run the first part of the challenge.
     */
    public function part1(): int
    {
        $nice = 0;

        $vowels = [
            'a', 'e', 'i', 'o', 'u',
        ];

        $exclusions = [
            'ab', 'cd', 'pq', 'xy',
        ];

        foreach ($this->getInput() as $string) {
            $len = strlen($string);

            // if any of the excluded strings are in the
            // current string, skip the whole string.
            if (strlen(str_replace($exclusions, '', $string)) !== $len) {
                continue;
            }

            $chars = str_split($string);

            // find all vowels from the string
            $stringVowels = array_intersect($chars, $vowels);

            if (count($stringVowels) < 3) {
                continue;
            }

            // search for repeating letters.
            if (count(array_find_repeating($chars)) < 1) {
                continue;
            }

            $nice ++;
        }

        return $nice;
    }

    /**
     * Run the second part of the challenge.
     */
    public function part2(): int
    {
        $nice = 0;

        foreach ($this->getInput() as $string) {
            $pairs = [];
            $repeats = [];

            $pair = '';
            $len = strlen($string);

            foreach (str_split($string) as $idx => $char) {
                $pair .= $char;

                if (strlen($pair) == 2) {
                    $pairs[] = $pair;
                    $pair = $char;
                }

                $compareIdx = $idx + 2;

                if ($compareIdx < $len && $char == $string[$compareIdx]) {
                    $repeats[] = $char;
                }
            }

            // if there is no character that repeats
            if (count($repeats) < 1) {
                continue;
            }

            $pairCount = count($pairs);
            $uniquePairs = count(array_flip($pairs));

            // if there are overlapping characters ex. aaa
            // and there are no other repeating pairs.
            if (preg_match('/(\w)\1{2}/', $string) && $pairCount - $uniquePairs <= 1) {
                continue;
            }

            // if there are no repeating pairs.
            if ($pairCount === $uniquePairs) {
                continue;
            }

            $nice ++;
        }

        return $nice;
    }
}
