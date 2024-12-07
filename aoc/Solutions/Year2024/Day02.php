<?php

namespace Mike\AdventOfCode\Solutions\Year2024;

use Mike\AdventOfCode\Solutions\Solution;

class Day02 extends Solution
{
    /**
     * The example input to be used to test the solution.
     */
    protected ?string $exampleInput = <<<TXT
    7 6 4 2 1
    1 2 7 8 9
    9 7 6 2 1
    1 3 2 4 5
    8 6 4 4 1
    1 3 6 7 9
    TXT;

    /**
     * Process the input from the challenge.
     */
    public function transformInput(string $input): array
    {
        $reports = split_lines($input);

        $reports = array_map(function ($report) {
            return array_map('intval', explode(' ', $report));
        }, $reports);

        return $reports;
    }

    /** Test the levels of a report */
    public function testReport(array $report, bool $reversed = false): bool
    {
        $result = true;

        if ($reversed) {
            $report = array_reverse($report);
        }

        foreach ($report as $idx => $level) {
            $next = $report[$idx+1] ?? null;

            if (is_null($next)) {
                break;
            }

            $diff = $next - $level;

            if ($level > $next || $diff < 1 || $diff > 3) {
                $result = false;
                break;
            }
        }

        if (! $reversed) {
            $result = $result || $this->testReport($report, true);
        }

        return $result;
    }

    /**
     * Run the first part of the challenge.
     */
    public function part1(): int
    {
        $safe = 0;

        foreach ($this->getInput() as $report) {
            if ($this->testReport($report)) {
                $safe++;
            }
        }

        return $safe;
    }

    /**
     * Run the second part of the challenge.
     */
    public function part2(): int
    {
        $safe = 0;

        foreach ($this->getInput() as $report) {
            foreach ($report as $idx => $level) {
                $newReport = $report;

                array_splice($newReport, $idx, 1);

                if ($this->testReport($newReport)) {
                    $safe++;
                    break;
                }
            }
        }

        return $safe;
    }
}
