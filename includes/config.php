<?php

return [
    /**
     * --------------------------------------------------
     *  Providers
     * --------------------------------------------------
     *
     * Providers that register services for the
     * application can be added here to be run when
     * application starts.
     *
     */
    'providers' => [
        \Mike\AdventOfCode\Providers\WhoopsProvider::class,
        \Mike\AdventOfCode\Providers\AdventOfCodeProvider::class,
    ],

    /**
     * --------------------------------------------------
     *  Advent of Code related configuration
     * --------------------------------------------------
     *
     *  Any configuration related to the advent of code
     *  project.
     *
     */
    'aoc' => [
        /**
         * --------------------------------------------------
         *  Advent of Code Session
         * --------------------------------------------------
         *
         *  The value of the session cookie after logging
         *  in the adventofcode.com platform.
         *
         */
        'session' => env('AOC_SESSION'),
    ],
];
