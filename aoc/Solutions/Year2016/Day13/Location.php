<?php

namespace Mike\AdventOfCode\Solutions\Year2016\Day13;

class Location
{
    /**
     * The X coordinate.
     */
    public int $x = 0;

    /**
     * The Y coordinate.
     */
    public int $y = 0;

    /**
     * The office designer's favorite number.
     */
    public static int $favorite = 0;

    /**
     * The bits of the location.
     */
    protected int|null $bits = null;

    /**
     * Create new location instance.
     */
    public function __construct(int $x = 0, int $y = 0)
    {
        $this->x = $x;
        $this->y = $y;
    }

    /**
     * Check to see if the coordinates are in a wall.
     */
    public function isWall(): bool
    {
        return $this->getBits() % 2 !== 0;
    }

    /**
     * Check to see if the coordinates are in an open space.
     */
    public function isOpenSpace(): bool
    {
        return $this->getBits() % 2 === 0;
    }

    /**
     * Get the bits for the location.
     */
    public function getBits(): int
    {
        if ($this->bits === null) {
            $x = $this->x;
            $y = $this->y;

            $sum = $x*$x + 3*$x + 2*$x*$y + $y + $y*$y + static::$favorite;

            $this->bits = array_sum(str_split(decbin($sum)));
        }

        return $this->bits;
    }

    /**
     * Check to see if this location is the same as the given one.
     */
    public function is(Location $other): bool
    {
        return $this->x === $other->x && $this->y == $other->y;
    }

    /**
     * Create a new instance of location from given array of coordinates.
     *
     * @param  int[]  $coordinates
     * @return static
     */
    public static function fromArray(array $coordinates): static
    {
        return new static($coordinates[0] ?? 0, $coordinates[1] ?? 0);
    }

    /**
     * Create new instance of location from string coordinates.
     */
    public static function fromString(string $coordinates): static
    {
        return static::fromArray(explode(',', $coordinates));
    }

    /**
     * Convert the location to string.
     */
    public function __toString(): string
    {
        return "$this->x,$this->y";
    }
}
