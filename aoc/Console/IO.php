<?php

namespace Mike\AdventOfCode\Console;

use Mike\AdventOfCode\Console\Traits\InputOutput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class IO
{
    use InputOutput;

    /**
     * Create new instance of IO class.
     */
    public function __construct(InputInterface $input = null, OutputInterface $output = null)
    {
        $this->input = $input;
        $this->output = $output;
    }
}
