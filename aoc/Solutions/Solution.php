<?php

namespace Mike\AdventOfCode\Solutions;

use Generator;
use Mike\AdventOfCode\AdventOfCodeDay;
use Mike\AdventOfCode\AdventOfCodeException;
use Mike\AdventOfCode\Console\Application;
use Mike\AdventOfCode\Console\IO;

abstract class Solution
{
    /**
     * The related day for the solution.
     */
    protected AdventOfCodeDay $day;

    /**
     * The console input/output.
     */
    protected IO $io;

    /**
     * The main application instance.
     */
    protected Application $app;

    /**
     * The example input to be used to test the solution.
     */
    protected ?string $exampleInput = null;

    /**
     * Indicating that the solution is running in testing mode.
     */
    protected bool $testing;

    /**
     * The prepared input for the solution.
     */
    protected mixed $input;

    /**
     * Te result of part1 method for the solution.
     */
    protected mixed $part1Result;

    /**
     * Te result of part2 method for the solution.
     */
    protected mixed $part2Result;

    /**
     * Create new solution instance.
     */
    public function __construct(AdventOfCodeDay $day)
    {
        $this->day = $day;
    }

    /**
     * Run the solution.
     */
    public function execute(bool $part1 = true, bool $part2 = true, bool $profile = true): bool
    {
        if ($this->testing) {
            $this->io->warn('Testing Mode.');

            if (! $this->exampleInput) {
                $this->io->error(sprintf(
                    'Solution <white>[%s]</> is in testing mode, but <white>$exampleInput</> is not set.',
                    get_class($this)
                ));

                return false;
            }
        }

        $this->io->info("Advent of Code {$this->day->getYear()}");
        $title = $this->day->info('title');
        $title = $title ? ": $title" : '';
        $this->io->info("Day {$this->day->getDay()}$title");
        $this->io->newLine();

        $this->before();

        if ($part1) {
            $this->runPart('part1', 'Part One', $profile);
        } else {
            // because this part was not run, but part two may use the result from part one,
            // we will load the previously stored results.
            $this->part1Result = $this->day->info('part1.result');

            // maybe silently run part1 just in case it is needed for part2.
            // or find a way to dynamically detect when result from part1 is
            // needed and then run part1 solution silently showing a notice
            // to the user.
        }

        if ($part2) {
            if ($part1) {
                $this->io->newLine();
            }

            $this->day->part2IsUnlocked();

            $this->runPart('part2', 'Part Two', $profile);
        } else {
            $this->part2Result = $this->day->info('part2.result');
        }

        $this->after();

        return true;
    }

    /**
     * Run specified part of the day's solution.
     */
    public function runPart(string $part, string $label = null, bool $profile = true, bool $silent = false): void
    {
        $this->beforeEach($part);

        $part = strtolower($part);
        $parts = ['part1', 'part2'];

        if (! in_array($part, $parts)) {
            throw AdventOfCodeException::invalidSolutionPartProvided($part, $parts);
        }

        $label = $label ?? $part;

        $question = $this->day->info("$part.question");
        $question = $question ? ": $question" : '';
        !$silent && $this->io->line(sprintf('<question>%s%s</>', $label, $question));

        $time = $memory = null;

        $start = microtime(true);
        $mem = memory_get_peak_usage();

        $result = $this->{$part}();

        $time = microtime(true) - $start;
        $memory = memory_get_peak_usage() - $mem;

        $this->day->setInfo("$part.time", $time);
        $this->day->setInfo("$part.memory", $memory);

        $this->{"{$part}Result"} = $result;

        ! $silent && $this->io->line(sprintf('> <white>%s</>', $result));

        $this->day->setInfo("$part.result", $result);

        if ($profile && !$silent) {
            $this->io->newLine();
            $this->io->info(sprintf(
                "$part took <white>%s</> and used <white>%s</> memory.",
                human_duration($time = $time * 1000),
                human_filesize($memory = $memory),
            ));
        }

        $this->afterEach($part, $result);
    }

    /**
     * Get the input of the day.
     *
     * @param  string  $real  Override the input when testing mode is off.
     * @param  string  $example  Override the input when testing mode is on.
     */
    public function getInput(string $real = null, string $example = null): mixed
    {
        return $this->transformInput($this->getRawInput($real, $example));
    }

    /**
     * Get the raw input without transformation applied.
     *
     * @param  string  $real  Override the input when testing mode is off.
     * @param  string  $example  Override the input when testing mode is on.
     */
    public function getRawInput(string $real = null, string $example = null)
    {
        return $this->testing ? $example ?? $this->exampleInput : $real ?? $this->day->getInput();
    }

    /**
     * Process the input from the challenge.
     */
    public function transformInput(string $input): mixed
    {
        return $input;
    }

    /**
     * Stream the input, instead of loading it to memory.
     *
     * @param  string  $real  Override the input when testing mode is off.
     * @param  string  $example  Override the input when testing mode is on.
     *
     * @param string $mode The mode parameter specifies the type of access you require to the stream.
     * It may be any of the following:
     *
     * 'r'  Open for reading only; place the file pointer at the beginning of the file.
     * 'r+' Open for reading and writing; place the file pointer at the beginning of the file.
     * 'w'  Open for writing only; place the file pointer at the beginning of the file and truncate the file to zero length. If the file does not exist, attempt to create it.
     * 'w+' Open for reading and writing; otherwise it has the same behavior as 'w'.
     * 'a'  Open for writing only; place the file pointer at the end of the file. If the file does not exist, attempt to create it. In this mode, fseek() has no effect, writes are always appended.
     * 'a+' Open for reading and writing; place the file pointer at the end of the file. If the file does not exist, attempt to create it. In this mode, fseek() only affects the reading position, writes are always appended.
     * 'x'  Create and open for writing only; place the file pointer at the beginning of the file. If the file already exists, the fopen() call will fail by returning false and generating an error of level E_WARNING. If the file does not exist, attempt to create it. This is equivalent to specifying O_EXCL|O_CREAT flags for the underlying open(2) system call.
     * 'x+' Create and open for reading and writing; otherwise it has the same behavior as 'x'.
     * 'c'  Open the file for writing only. If the file does not exist, it is created. If it exists, it is neither truncated (as opposed to 'w'), nor the call to this function fails (as is the case with 'x'). The file pointer is positioned on the beginning of the file. This may be useful if it's desired to get an advisory lock (see flock()) before attempting to modify the file, as using 'w' could truncate the file before the lock was obtained (if truncation is desired, ftruncate() can be used after the lock is requested).
     * 'c+' Open the file for reading and writing; otherwise it has the same behavior as 'c'.
     * 'e'  Set close-on-exec flag on the opened file descriptor. Only available in PHP compiled on POSIX.1-2008 conform systems.
     * @param  resource|null  $context  A stream context resource.
     * @return resource
     */
    public function streamInput(string $real = null, string $example = null, string $mode = 'r', $context = null)
    {
        if ($this->testing) {
            $example = $example ?? $this->exampleInput;

            return fopen("data://text/plain,$example", $mode, false, $context);
        }

        return $real
            ? fopen("data://text/plain,$real", $mode, false, $context)
            : $this->day->streamInput($mode, $context);
    }

    /**
     * Stream the input, instead of loading it to memory.
     *
     * @param  string  $real  Override the input when testing mode is off.
     * @param  string  $example  Override the input when testing mode is on.
     * @param  callable|null  $map  Map over each line with given callback, after all other filters.
     * @param  boolean  $trim Trim each line after splitting.
     * @param  boolean  $ignoreEmpty  Ignore empty lines.
     * @param  resource|null  $context  A stream context resource.
     * @return \Generator
     */
    public function streamLines(string $real = null, string $example = null, ?callable $map = null, bool $trim = true, bool $ignoreEmpty = true): Generator
    {
        $stream = $this->streamInput($real, $example);

        while ($line = fgets($stream)) {
            $end = strlen($line) - 1;

            if ($line[$end] === "\n") {
                $line = substr($line, 0, -1);

                if ($line[$end-1] === "\r") {
                    $line = substr($line, 0, -1);
                }
            }

            if ($trim) {
                $line = trim($line);
            }

            if ($ignoreEmpty && $line == '') {
                continue;
            }

            if (! is_null($map)) {
                $line = call_user_func($map, $line);
            }

            yield $line;
        }

        fclose($stream);
    }

    /**
     * Run the first part of the challenge.
     */
    abstract function part1();

    /**
     * Run the second part of the challenge.
     */
    abstract function part2();

    /**
     * Hook before all parts are run.
     */
    protected function before(): void
    {
        //
    }

    /**
     * Hook after all parts are run.
     */
    protected function after(): void
    {
        //
    }

    /**
     * Hook before each part is run.
     */
    protected function beforeEach(string $part): void
    {
        //
    }

    /**
     * Hook after each part is run.
     */
    protected function afterEach(string $part, $result): void
    {
        //
    }

    /**
     * Make the solution in testing mode.
     */
    public function testing(bool $value = true): static
    {
        $this->testing = $value;

        return $this;
    }

    /**
     * Get the result of part1 method.
     */
    public function getPart1Result(): mixed
    {
        return $this->part1Result;
    }

    /**
     * Get the result of part2 method.
     */
    public function getPart2Result(): mixed
    {
        return $this->part2Result;
    }


    /**
     * Get the console instance.
     */
    public function getIO(): IO
    {
        return $this->io;
    }

    /**
     * Set the console input/output instance.
     */
    public function setIO(IO $io): static
    {
        $this->io = $io;

        return $this;
    }

    /**
     * Get the main application instance.
     *
     * @return \Mike\AdventOfCode\Console\Application
     */
    public function app(): Application
    {
        return $this->app;
    }

    /**
     * Set the main application instance.
     *
     * @param  \Mike\AdventOfCode\Console\Application  $app
     * @return $this
     */
    public function setApplication(Application $app): self
    {
        $this->app = $app;

        return $this;
    }
}
