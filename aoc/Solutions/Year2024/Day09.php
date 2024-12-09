<?php

namespace Mike\AdventOfCode\Solutions\Year2024;

use Mike\AdventOfCode\Solutions\Solution;

class Day09 extends Solution
{
    /**
     * The example input to be used to test the solution.
     */
    protected ?string $exampleInput = '2333133121414131402';

    /**
     * Process the input from the challenge.
     */
    public function transformInput(string $input): array
    {
        return array_map('intval', str_split($input));
    }

    /**
     * Decompress a disk.
     *
     * @param  array  $disk
     * @return \Generator<integer|null>
     */
    public function decompress(array $disk): \Generator
    {
        $id = 0;

        foreach ($disk as $idx => $block) {
            // if we are reading free space block, but the free space is 0
            if ($idx % 2 !== 0 && $block === 0) {
                continue;
            }

            foreach (array_fill(0, $block, ($idx % 2 === 0) ? $id++ : null) as $v)
                yield $v;
        }
    }

    /**
     * Calculate checksum of whole disk.
     */
    public function diskChecksum(iterable $disk): int
    {
        $checksum = 0;

        foreach ($disk as $idx => $id) {
            // empty space.
            if (is_null($id)) {
                continue;
            }

            $checksum += $idx * $id;
        }

        return $checksum;
    }

    /**
     * Fragment given disk.
     *
     * @param  array  $disk
     * @return \Generator
     */
    public function fragment(array $disk): \Generator
    {
        $i  = 0;
        $r = count($disk);
        $n = $r - count(array_filter($disk, 'is_null'));

        while (array_key_exists($i, $disk)) {
            if ($i < $n && is_null($disk[$i])) {
                while (true) {
                    if (is_null($disk[-- $r])) {
                        continue;
                    }

                    $disk[$i] = $disk[$r];
                    $disk[$r] = null;
                    break;
                }
            }

            yield $disk[$i];
            $i ++;
        }
    }

    /**
     * Try to move files from the end of the disk to the first free space,
     * if they don't fit skip them and continue until the end.
     *
     * @param  array  $disk
     * @return \Generator
     */
    public function reorderFiles(array $disk): \Generator
    {
        $files = [];

        foreach ($disk as $idx => $id) {
            if (is_null($id)) {
                continue;
            }

            $files[$id][] = $idx;
        }

        $files = array_reverse(array_map(
            fn ($id, $block) => [$id, count($block), $block],
            array_keys($files),
            $files
        ));

        $blocks = count($disk);

        foreach ($files as $file) {
            for ($idx = 0; $idx < $blocks; $idx ++) {
                $block = $disk[$idx];

                if (! is_null($block)) {
                    continue;
                }

                if ($file[2][0] < $idx) {
                    break;
                }

                $f = $idx;
                $freeSpace = 0;

                while ($f < $blocks && is_null($disk[$f])) {
                    $freeSpace++;
                    $f++;
                }

                if ($file[1] > $freeSpace) {
                    $idx += $freeSpace - 1;
                    continue;
                }

                foreach ($file[2] as $i => $bi) {
                    $disk[$bi] = null;
                    $disk[$idx + $i] = $file[0];
                }

                break;
            }
        }

        unset($files);

        yield from $disk;
    }

    /**
     * Run the first part of the challenge.
     */
    public function part1(): int
    {
        $disk = iterator_to_array($this->decompress($this->getInput()));

        return $this->diskChecksum($this->fragment($disk));
    }

    /**
     * Run the second part of the challenge.
     */
    public function part2(): int
    {
        $disk = iterator_to_array($this->decompress($this->getInput()));

        return $this->diskChecksum($this->reorderFiles($disk));
    }
}
