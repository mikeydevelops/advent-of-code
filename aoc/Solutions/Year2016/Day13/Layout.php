<?php

namespace Mike\AdventOfCode\Solutions\Year2016\Day13;

use Generator;
use Mike\AdventOfCode\Solutions\Year2016\Day13\Location;

class Layout
{
    /**
     *
     * @return \Generator|\Mike\AdventOfCode\Solutions\Year2016\Day13\Location[]
     */
    public function traverse(): Generator
    {

        yield new Location(0, 0);

        $x = 1;

        while (true) {
            for ($y = 0; $y < $x; $y ++) {
                yield new Location($x, $y);
                yield new Location($y, $x);
            }

            yield new Location($x, $x);

            $x ++;
        }
    }
}
