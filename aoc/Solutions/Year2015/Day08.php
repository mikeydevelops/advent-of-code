<?php

namespace Mike\AdventOfCode\Solutions\Year2015;

use Mike\AdventOfCode\Solutions\Solution;

/**
 * @method string[] getInput() Get the input strings.
 */
class Day08 extends Solution
{
    /**
     * The example input to be used to test the solution.
     */
    protected ?string $exampleInput = <<<TXT
    ""
    "abc"
    "aaa\"aaa"
    "\\x27"
    TXT;

    /**
     * Process the input from the challenge.
     *
     * @return string[]
     */
    public function transformInput(string $input): array
    {
        return array_filter(explode("\n", $input));
    }

    /**
     * Run the first part of the challenge.
     */
    public function part1(): int
    {
        $lengths = array_map(function ($string) {
            // Couldn't find right order to escape all characters at same time
            // gave up and used eval, quite a lot faster

            // // remove first and last characters
            // $memory = substr($string, 1, strlen($string) - 2);
            // // replace escaped characters
            // $memory = preg_replace_callback('/\\\([^x])/', fn($m) => $m[1], $memory);
            // // convert hex characters to ascii
            // $memory = preg_replace_callback('/\\\x([0-9a-f]{2})/', fn($m) => chr(hexdec($m[1])), $memory);

            // could use eval, but scary :P
            $memory = eval("return $string;");

            return [
                'code' => strlen($string),
                'memory' => strlen($memory),
            ];
        }, $this->getInput());

        $codeTotal = array_sum(array_column($lengths, 'code'));
        $memoryTotal = array_sum(array_column($lengths, 'memory'));

        return $codeTotal - $memoryTotal;
    }

    /**
     * Run the second part of the challenge.
     */
    public function part2(): int
    {
        $lengths = array_map(function ($string) {
            return [
                'code' => $len = strlen($string),
                'encoded' => $len + substr_count($string, '\\') + substr_count($string, '"') + 2,
            ];
        }, $this->getInput());

        $encodedTotal = array_sum(array_column($lengths, 'encoded'));
        $codeTotal = array_sum(array_column($lengths, 'code'));

        return $encodedTotal - $codeTotal;
    }
}
