<?php

namespace Mike\AdventOfCode\Solutions\Year2016;

use Mike\AdventOfCode\Solutions\Solution;
use Mike\AdventOfCode\Solutions\Year2016\Day04\Room;

/**
 * @method  \Mike\AdventOfCode\Solutions\Year2016\Day04\Room[]  getInput()  Get the rooms.
 */
class Day04 extends Solution
{
    /**
     * The example input to be used to test the solution.
     */
    protected ?string $exampleInput = <<<TXT
    aaaaa-bbb-z-y-x-123[abxyz]
    a-b-c-d-e-f-g-h-987[abcde]
    not-a-real-room-404[oarel]
    totally-real-room-200[decoy]
    TXT;

    /**
     * Process the input from the challenge.
     *
     * @return \Mike\AdventOfCode\Solutions\Year2016\Day04\Room[]
     */
    public function transformInput(string $input): array
    {
        preg_match_all('/^([a-z\-]+)\-(\d+)\[([a-z]+)\]$/im', $input, $matches, PREG_SET_ORDER);

        return array_map(fn (array $m) => new Room($m[1], $m[2], $m[3]), $matches);
    }

    /**
     * Run the first part of the challenge.
     */
    public function part1(): int
    {
        $rooms = array_filter($this->getInput(), fn(Room $r) => $r->isReal());

        return array_sum(array_map(fn(Room $r) => $r->sector, $rooms));
    }

    /**
     * Run the second part of the challenge.
     */
    public function part2(): int
    {
        $rooms = array_filter($this->getInput(), fn(Room $r) => $r->isReal());

        $rooms = array_map(fn(Room $r) => $r->decrypt(), $rooms);

        $search = $this->testing ? 'ttttt uuu s r q' : 'northpole object storage';

        $storage = current(array_filter(
            $rooms,
            fn (Room $room) => $room->name === $search,
        ));

        if ($storage === false) {
            $this->io->error('Unable to find storage room.');

            return -1;
        }

        return $storage->sector;
    }
}
