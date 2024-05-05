<?php

namespace Mike\AdventOfCode\Solutions\Year2016;

use Mike\AdventOfCode\Solutions\Solution;

/**
 * @method  string  getInput()  Get the compressed file.
 */
class Day09 extends Solution
{
    /**
     * The example input to be used to test the solution.
     */
    protected ?string $exampleInput = <<<TXT
    ADVENT
    A(1x5)BC
    (3x3)XYZ
    A(2x2)BCD(2x2)EFG
    (6x1)(1x3)A
    X(8x2)(3x3)ABCY
    TXT;

    /**
     * Process the input from the challenge.
     */
    public function transformInput(string $input): string
    {
        return trim($input);
    }

    /**
     * Run the first part of the challenge.
     */
    public function part1(): int
    {
        return $this->decompress($this->getInput(), countOnly: true);
    }

    /**
     * Run the second part of the challenge.
     */
    public function part2(): int
    {
        $this->io->warn('This method takes really long time. Please be patient.');

        // should probably optimize the improved decompression but eh, whatever
        // it's not like I am doing the leaderboards lol

        return $this->decompress($this->getInput(), improved: true, countOnly: true);
    }

    /**
     * Decompress the given string using the algo provided by the challenge.
     */
    public function decompress(string $input, bool $countOnly = false, bool $improved = false): string|int
    {
        $result = $countOnly ? 0 : '';

        for ($i = 0; $i < strlen($input); $i++) {
            $char = $input[$i];

            if ($char != '(') {
                if ($countOnly) {
                    if (trim($char)) {
                        $result += 1;
                    }

                    continue;
                }

                $result .= $char;

                continue;
            }

            $end = stripos($input, ')', $i);

            [$len, $repeat] = array_map('intval', explode('x', substr($input, $i + 1, $end - $i)));

            $data = str_repeat(substr($input, $end+1, $len), $repeat);

            if ($countOnly) {
                $result += $improved
                    ? $this->decompress($data, $countOnly, $improved)
                    : strlen(preg_replace('/[\s|\n|\r|\t|\v|\0]+/', '', $data));
            } else {
                $result .= $improved
                    ? $this->decompress($data, $countOnly, $improved)
                    : $data;
            }

            $i = $end + $len;
        }

        return $result;
    }
}
