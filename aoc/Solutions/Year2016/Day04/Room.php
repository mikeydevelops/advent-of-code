<?php

namespace Mike\AdventOfCode\Solutions\Year2016\Day04;

class Room
{
    /**
     * The name of the room.
     */
    public string $name;

    /**
     * The encrypted name of the room.
     */
    public string $encryptedName;

    /**
     * The sector where this room is located in.
     */
    public int $sector;

    /**
     * The checksum of the name.
     */
    public string $checksum;

    /**
     * Create new instance of Room.
     */
    public function __construct(string $encryptedName, int $sector, string $checksum)
    {
        $this->encryptedName = $encryptedName;
        $this->sector = $sector;
        $this->checksum = $checksum;
    }

    /**
     * Check to see if the room is real.
     */
    public function isReal(): bool
    {
        return $this->checksum === $this->calculateChecksum();
    }

    /**
     * Check to see if the room is decoy.
     */
    public function isDecoy(): bool
    {
        return ! $this->isReal();
    }

    /**
     * Calculate room checksum.
     */
    public function calculateChecksum(): string
    {
        $chars = str_split(str_replace('-', '', $this->encryptedName));

        $ranks = array_count_values($chars);

        if (count($ranks) < 5) {
            return '';
        }

        $ranks = array_map(function ($rank, $char) {
            return compact('rank', 'char');
        }, $ranks, array_keys($ranks));

        usort($ranks, function ($a, $b) {
            // first sort by rank descending
            if ($a['rank'] > $b['rank']) return -1;
            if ($a['rank'] < $b['rank']) return 1;

            // then sort by char ascending if rank is same
            if ($a['char'] > $b['char']) return 1;
            if ($a['char'] < $b['char']) return -1;

            return 0;
        });

        return implode('', array_slice(array_column($ranks, 'char'), 0, 5));
    }

    /**
     * Decrypt the name of the room.
     */
    public function decrypt(): static
    {
        $name = '';

        foreach (str_split($this->encryptedName) as $char) {
            if ($char == '-') {
                $name .= ' ';

                continue;
            }

            $name .= caesar_char($char, $this->sector);
        }

        $this->name = $name;

        return $this;
    }
}
