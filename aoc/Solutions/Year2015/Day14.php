<?php

namespace Mike\AdventOfCode\Solutions\Year2015;

use Mike\AdventOfCode\Solutions\Solution;
use Mike\AdventOfCode\Solutions\Year2015\Day14\Deer;

/**
 * @method \Mike\AdventOfCode\Solutions\Year2015\Day14\Deer[] getInput()  Get the deer statistics.
 */
class Day14 extends Solution
{
    /**
     * The example input to be used to test the solution.
     */
    protected ?string $exampleInput = <<<TXT
    Comet can fly 14 km/s for 10 seconds, but then must rest for 127 seconds.
    Dancer can fly 16 km/s for 11 seconds, but then must rest for 162 seconds.
    TXT;

    /**
     * The results of the simulation.
     */
    protected array $results = [];

    /**
     * Process the input from the challenge.
     *
     * @return \Mike\AdventOfCode\Solutions\Year2015\Day14\Deer[]
     */
    public function transformInput(string $input): array
    {
        $deer = [];

        // remove unused strings
        $input = str_replace(
            ['can fly ', 'km/s for ', 'seconds, but then must rest for ', ' seconds.'],
            '',
            trim($input)
        );

        foreach (explode("\n", $input) as $line) {
            [$name, $speed, $stamina, $rest] = explode(' ', $line);

            $speed = intval($speed);
            $stamina = intval($stamina);
            $rest = intval($rest);

            $deer[$name] = new Deer($name, $speed, $stamina, $rest);
        }

        return $deer;
    }

    /**
     * Hook before all parts are run.
     */
    public function before(): void
    {
        $this->io->info('Running race...');
        $this->io->newLine();

        $deer = $this->getInput();
        $initial = $this->getInput();

        $distances = array_combine(array_keys($deer), array_fill(0, count($deer), 0));
        $points = array_combine(array_keys($deer), array_fill(0, count($deer), 0));

        foreach (range(1, 2503) as $time) {
            foreach ($deer as $name => $d) {
                if ($d->stamina) {
                    $d->stamina --;
                    $distances[$name] += $d->speed;

                    continue;
                }

                $d->rest --;

                if ($d->rest == 0) {
                    $d->stamina = $initial[$name]->stamina;
                    $d->rest = $initial[$name]->rest;
                }
            }

            $max = max($distances);

            $stepWinners = array_keys(array_filter($distances, function ($distance) use ($max) {
                return $distance == $max;
            }));

            foreach ($stepWinners as $stepWinner) {
                $points[$stepWinner] += 1;
            }
        }

        $winnerDistance = max($distances);
        $distanceWinner = array_search($winnerDistance, $distances);

        $winnerPoints = max($points);
        $pointsWinner = array_search($winnerPoints, $points);

        $distanceWinner = [$distanceWinner, $winnerDistance];
        $pointsWinner = [$pointsWinner, $winnerPoints];

        $this->results = [$distanceWinner, $pointsWinner];
    }

    /**
     * Run the first part of the challenge.
     */
    public function part1(): int
    {

        $this->io->line("Winning deer, {$this->results[0][0]}, traveled: {$this->results[0][1]} km");

        return $this->results[0][1];
    }

    /**
     * Run the second part of the challenge.
     */
    public function part2(): int
    {
        $this->io->line("Winning deer, {$this->results[1][0]}, received: {$this->results[1][1]} points");

        return $this->results[1][1];
    }
}
