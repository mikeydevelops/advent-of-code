<?php

namespace Mike\AdventOfCode\Solutions\Year2016;

use Mike\AdventOfCode\Solutions\Solution;

/**
 * @method  string[][]  getInput()  Get the messages.
 */
class Day06 extends Solution
{
    /**
     * The example input to be used to test the solution.
     */
    protected ?string $exampleInput = <<<TXT
    eedadn
    drvtee
    eandsr
    raavrd
    atevrs
    tsrnev
    sdttsa
    rasrtv
    nssdts
    ntnada
    svetve
    tesnvt
    vntsnd
    vrdear
    dvrsen
    enarar
    TXT;

    /**
     * Process the input from the challenge.
     *
     * @return string[][]
     */
    public function transformInput(string $input): array
    {
        return array_map('str_split', split_lines($input));
    }

    /**
     * Run the first part of the challenge.
     */
    public function part1(): string
    {
        return $this->correctErrors($this->getInput());
    }

    /**
     * Run the second part of the challenge.
     */
    public function part2(): string
    {
        return $this->correctErrors($this->getInput(), 'asort');
    }

    /**
     * Correct the errors from given message.
     */
    public function correctErrors(array $messages, string $rankSort = 'arsort'): string
    {
        $messages = $this->getInput();
        $len = count($messages[0]);
        $corrected = '';

        for ($i = 0; $i < $len; $i ++) {
            $ranks = array_count_values(array_column($messages, $i));

            call_user_func_array($rankSort, [&$ranks]);

            $chars = array_keys($ranks);

            $corrected .= array_shift($chars);
        }

        return $corrected;
    }
}
