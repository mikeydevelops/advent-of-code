<?php

namespace Mike\AdventOfCode\Solutions\Year2015\Day09;

use Exception;

class Distance
{
    /**
     * The starting location of the distance.
     *
     * @var string
     */
    protected string $from;

    /**
     * The end location for the distance.
     *
     * @var string
     */
    protected string $to;

    /**
     * The length of the distance.
     *
     * @var integer
     */
    protected int $length;

    /**
     * Lookup array of distances.
     *
     * @var static[]
     */
    protected static array $distances = [];

    /**
     * Create new instance of Distance.
     *
     * @param  string  $from
     * @param  string  $to
     * @param  integer  $length
     * @return void
     */
    public function __construct(string $from, string $to, int $length)
    {
        $this->from = $from;
        $this->to = $to;
        $this->length = $length;
    }

    /**
     * Get the starting location.
     *
     * @return string
     */
    public function getFrom() : string
    {
        return $this->from;
    }

    /**
     * Get the end location.
     *
     * @return string
     */
    public function getTo() : string
    {
        return $this->to;
    }

    /**
     * Get the length between the locations.
     *
     * @return integer
     */
    public function getLength() : int
    {
        return $this->length;
    }

    /**
     * Get or create new instance of a route regardless of which location is first or second.
     *
     * @param  string  $first
     * @param  string  $second
     * @param  integer|null  $distance
     * @return \Route
     * @throws \Exception
     */
    public static function make(string $first, string $second, int $distance = null) : Distance
    {
        $name = [$first, $second];

        sort($name);

        $name = implode(':', $name);

        if (isset(static::$distances[$name])) {
            return static::$distances[$name];
        }

        if (is_null($distance)) {
            throw new Exception(sprintf(
                'Unable to create distance from %s to %s. No length was provided to %s::instance',
                $first,
                $second,
                static::class
            ));
        }

        return static::$distances[$name] = new static($first, $second, $distance);
    }

    /**
     * Get the length between two locations regardless their order.
     *
     * @param  string  $first
     * @param  string  $second
     * @return integer
     * @throws \Exception
     */
    public static function length(string $first, string $second) : int
    {
        return static::make($first, $second)->getLength();
    }

    /**
     * Check to see if given distance is valid.
     *
     * @param  string  $first
     * @param  string  $second
     * @return integer
     */
    public static function isValid(string $first, string $second) : int
    {
        $name = [$first, $second];

        sort($name);

        $name = implode(':', $name);

        return isset(static::$distances[$name]);
    }
}
