<?php

namespace Mike\AdventOfCode\Solutions\Year2015;

use Mike\AdventOfCode\Solutions\Solution;

/**
 * @method  string  getInput()  Get the document.
 */
class Day12 extends Solution
{
    /**
     * The example input to be used to test the solution.
     */
    protected ?string $exampleInput = '[1,{"c":"red","b":2},3]';

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
        preg_match_all('/[+-]?((\d*\.?\d+)|(\d+\.?\d*))/', $this->getInput(), $matches);

        return array_sum($matches[0]);
    }

    /**
     * Run the second part of the challenge.
     */
    public function part2(): int
    {
        $sanitized = $this->sanitizeDocument($this->getInput());

        preg_match_all('/[+-]?((\d*\.?\d+)|(\d+\.?\d*))/', $sanitized, $matches);

        return array_sum($matches[0]);
    }

    /**
     * Remove all red children from the document.
     */
    public function sanitizeDocument(string $document): string
    {
        $document = json_decode($document, true);

        if (! $document) {
            return '';
        }

        $document = array_map([$this, 'removeReds'], $document);

        return json_encode($document, JSON_PRETTY_PRINT);
    }

    /**
     * Remove all associative arrays with value red.
     *
     * @template T
     * @param  T  $child
     * @return T
     */
    public function removeReds(mixed $child): mixed
    {
        if (! is_array($child)) {
            return $child;
        }

        if (array_is_assoc($child) && in_array('red', $child)) {
            return 0;
        }

        foreach ($child as $idx => $item) {
            if (! is_array($item)) {
                continue;
            }

            if (array_is_assoc($item) && in_array('red', $item)) {
                $child[$idx] = 0;

                continue;
            }

            $child[$idx] = array_map([$this, 'removeReds'], $item);
        }

        return $child;
    }
}
