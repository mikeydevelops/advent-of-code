<?php

namespace Mike\AdventOfCode\Solutions\Year2015\Day02;

class Box
{
    /**
     * The length of the box.
     */
    public int $length = 0;

    /**
     * The width of the box.
     */
    public int $width = 0;

    /**
     * The height of the box.
     */
    public int $height = 0;

    /**
     * Create new box size instance.
     */
    public function __construct(int $length = 0, int $width = 0, int $height = 0)
    {
        $this->length = $length;
        $this->width = $width;
        $this->height = $height;
    }

    /**
     * Get the surface area of the box.
     */
    public function area(): int
    {
        $sides = $this->sides();

        return (2 * $sides[0]) + (2 * $sides[1]) + (2 * $sides[2]);
    }

    /**
     * Get the volume of the box.
     */
    public function volume(): int
    {
        return $this->length * $this->width * $this->height;
    }

    /**
     * The sides of the box.
     *
     * @return int[]
     */
    public function sides(): array
    {
        return [
            $this->length * $this->width,
            $this->width * $this->height,
            $this->height * $this->length,
        ];
    }

    /**
     * Get the perimeters of the box.
     *
     * @return int[]
     */
    public function perimeters(): array
    {
        $l2 = $this->length * 2;
        $w2 = $this->width * 2;
        $h2 = $this->height * 2;

        return [
            $l2 + $w2,
            $w2 + $h2,
            $h2 + $l2,
        ];
    }

    /**
     * Create new box size instance from array.
     */
    public static function fromArray(array $size): static
    {
        return new static(
            $size['length'] ?? 0,
            $size['width'] ?? 0,
            $size['height'] ?? 0,
        );
    }
}
