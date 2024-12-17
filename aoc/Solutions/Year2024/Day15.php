<?php

namespace Mike\AdventOfCode\Solutions\Year2024;

use Mike\AdventOfCode\Solutions\Solution;
use Mike\AdventOfCode\Solutions\Year2015\Day02\Box;
use Symfony\Component\Console\Output\ConsoleSectionOutput;

class Day15 extends Solution
{
    /**
     * The example input to be used to test the solution.
     */
    protected ?string $exampleInput = <<<TXT
    #######
    #...#.#
    #.....#
    #..OO@#
    #..O..#
    #.....#
    #######

    <vv<<^^<<^^
    TXT;
    protected ?string $exampleInput2 = <<<TXT
    ##########
    #..O..O.O#
    #......O.#
    #.OO..O.O#
    #..O@..O.#
    #O#..O...#
    #O..O..O.#
    #.OO.O.OO#
    #....O...#
    ##########

    <vv>^<v^>v>^vv^v>v<>v^v<v<^vv<<<^><<><>>v<vvv<>^v^>^<<<><<v<<<v^vv^v>^
    vvv<<^>^v^^><<>>><>^<<><^vv^^<>vvv<>><^^v>^>vv<>v<<<<v<^v>^<^^>>>^<v<v
    ><>vv>v^v^<>><>>>><^^>vv>v<^^^>>v^v^<^^>v^^>v^<^v>v<>>v^v^<v>v^^<^^vv<
    <<v<^>>^^^^>>>v^<>vvv^><v<<<>^^^vv^<vvv>^>v<^^^^v<>^>vvvv><>>v^<<^^^^^
    ^><^><>>><>^^<<^^v>>><^<v>^<vv>>v>>>^v><>^v><<<<v>>v<v<v>vvv>^<><<>^><
    ^>><>^v<><^vvv<^^<><v<<<<<><^v<<<><<<^^<v<^^^><^>>^<v^><<<^>>^v<v^v<v^
    >^>>^v>vv>^<<^v<>><<><<v<<v><>v<^vv<<<>^^v^>^^>>><<^v>>v^v><^^>>^<>vv^
    <><^^>^^^<><vvvvv^v<v<<>^v<v>v<<^><<><<><<<^^<<<^<<>><<><^^^>^^<>^>v<>
    ^^>vv<^v^v<vv>^<><v<^v>^^^>>>^^vvv^>vvv<>>>^<^>>>>>^<<^v>^vvv<>^<><<v>
    v^^>>><<^^<>>^v^<v^vv<>v^<<>^<^v^v><^<<<><<^<v><v<>vv>>v><v^<vv<>v^<<^
    TXT;

    #region  Warehouse values.
    const WALL = 0;
    const EMPTY = 1;
    const BOX = 2;
    const BOX2 = 3;
    const ROBOT = 4;
    #endregion

    /**
     * The warehouse from the input. In a 2d grid.
     * 0 - wall (#)
     * 1 - free space (.)
     * 2 - box (O)
     * 3 - robot (@)
     *
     * @var array<int[]>|null
     */
    protected ?array $warehouse = null;

    /**
     * Where the robot's moves start in the input.
     * This is helper to make things quicker while reading the input.
     * if null, when reading the input, the sequence will have to be detected.
     *
     * @var integer|null
     */
    protected ?int $sequenceStart = null;

    /**
     * Wether or not the warehouse can be rendered.
     * It may not be rendered if there is no space in the terminal.
     *
     * @var boolean
     */
    protected bool $cantRender = false;

    /**
     * The section used to render the warehouse.
     *
     * @var \Symfony\Component\Console\Output\ConsoleSectionOutput|null
     */
    protected ?ConsoleSectionOutput $renderSection = null;

    /**
     * Get the lanternfish warehouse.
     */
    public function warehouse(): array
    {
        $stream = $this->streamInput();
        $warehouse = [];
        $keys = [
            '#' => static::WALL,
            '.' => static::EMPTY,
            'O' => static::BOX,
            '@' => static::ROBOT,
        ];

        while (true) {
            $row = trim(fgets($stream));

            // found empty line.
            if ($row === '') {
                $this->sequenceStart = ftell($stream);

                break;
            }

            $warehouse[] = array_map(fn($c) => $keys[$c], str_split($row));
        }

        fclose($stream);

        return $warehouse;
    }

    /**
     * Get the lanternfish wide warehouse.
     */
    public function wideWarehouse(): array
    {
        $stream = $this->streamInput();
        $warehouse = [];
        $keys = [
            '#' => static::WALL,
            '.' => static::EMPTY,
            '[' => static::BOX,
            ']' => static::BOX2,
            '@' => static::ROBOT,
        ];

        while (true) {
            $row = trim(fgets($stream));

            // found empty line.
            if ($row === '') {
                $this->sequenceStart = ftell($stream);

                break;
            }

            $row = str_replace(
                ['#',  '.',  'O',  '@'],
                ['##', '..', '[]', '@.'],
                $row
            );

            $warehouse[] = array_map(fn($c) => $keys[$c], str_split($row));
        }

        // for render function to detect which type of warehouse it is rendering.
        $warehouse[0][0] = static::BOX2;

        fclose($stream);

        return $warehouse;
    }

    /**
     * Get the moves from the input.
     *
     * @return \Generator<array{int,int}>
     */
    public function moves(): \Generator
    {
        $stream = $this->streamInput();

        if (is_null($this->sequenceStart)) {
            while (true) {
                if (in_array(fgetc($stream), ['^', '>', 'v', '<'])) {
                    $this->sequenceStart = ftell($stream) - 1;
                    break;
                }
            }
        }

        // jump to move sequence
        fseek($stream, $this->sequenceStart);

        $directions = [
            '^' => [0, -1, '^'],
            '>' => [1, 0, '>'],
            'v' => [0, 1, 'v'],
            '<' => [-1, 0, '<'],
        ];

        while (! feof($stream)) {
            $char = fgetc($stream);

            if (! $char || $char === "\r" || $char === "\n") {
                continue;
            }

            yield $directions[$char];
        }

        fclose($stream);
    }

    /**
     * Run all the given moves of the robot.
     *
     * @param  array<int[]>  $warehouse
     * @param  iterable<array{int,int}>  $moves
     * @return array<int[]>
     */
    protected function runSequence(array $warehouse, iterable $sequence)
    {
        [$x, $y] = grid_search($warehouse, static::ROBOT)->current();
        $isWide = $warehouse[0][0] === static::BOX2;

        foreach ($sequence as $idx => [$dx, $dy, $d]) {
            [$nx, $ny] = [$x + $dx, $y + $dy];

            if ($warehouse[$ny][$nx] === static::WALL) {
                $this->render($warehouse, "Move $d:");
                continue;
            }

            if ($warehouse[$ny][$nx] === static::EMPTY) {
                $warehouse[$y][$x] = static::EMPTY;
                $warehouse[$ny][$nx] = static::ROBOT;
                $x = $nx;
                $y = $ny;
                $this->render($warehouse, "Move $d:");

                continue;
            }

            $next = [];
            $reverse = false;

            // moving horizontally
            if ($dx != 0) {
                $next = array_slice(
                    $warehouse[$y],
                    $dx < 0 ? 0 : $x+1,
                    $dx < 0 ? $x : null,
                    preserve_keys: true
                );

                if ($dx < 0) {
                    $next = array_reverse($next, preserve_keys: true);
                    $reverse = true;
                }
            // moving vertically
            } else {
                $next = array_slice(
                    array_column($warehouse, $x),
                    $dy < 0 ? 0 : $y+1,
                    $dy < 0 ? $y : null,
                    preserve_keys: true
                );

                if ($dy < 0) {
                    $next = array_reverse($next, preserve_keys: true);
                    $reverse = true;
                }
            }

            $end = array_search(static::EMPTY, $next);
            $wall = array_search(static::WALL, $next);

            // no free space found, boxes are next to wall, skip
            if ($end === false) {
                $this->render($warehouse, "Move $d:");
                continue;
            }

            // we found free space but is behind a wall, so cannot move there.
            if ((!$reverse && $end > $wall) || ($reverse && $end < $wall)) {
                $this->render($warehouse, "Move $d:");
                continue;
            }

            // set the following
            foreach (range(($dx != 0 ? $x : $y) + ($reverse ? -1 : 1), $end) as $i) {
                $warehouse[$dx != 0 ? $y : $i][$dx != 0 ? $i : $x] = static::BOX;
            }

            // move the bot.
            $warehouse[$y][$x] = static::EMPTY;
            $warehouse[$ny][$nx] = static::ROBOT;
            $x = $nx;
            $y = $ny;

            $this->render($warehouse, "Move $d:");
        }

        return $warehouse;
    }

    /**
     * Render the space with the robots.
     *
     * @param  array<integer[]>
     * @param  string|null  $status  Additional end line to display something at the start of the grid.
     * @return void
     */
    protected function render(array $warehouse, ?string $status = null): void
    {
        if (! $this->getIO()->getOutput()->isVerbose() || $this->cantRender) {
            return;
        }

        [$w, $h] = [count($warehouse[0]), count($warehouse)];
        [$tw, $th] = [$this->app()->terminal->getWidth(), $this->app()->terminal->getHeight()];

        if ($tw < $w) {
            $this->getIO()->warn("Unable to render warehouse, needed terminal width of <white>$w</> characters. Got <white>$tw</>.");
            $this->cantRender = true;
        }

        // if ($th < $h) {
        //     $this->getIO()->warn("Unable to render warehouse, needed terminal height of <white>$h</> lines. Got <white>$th</>.");
        //     $this->cantRender = true;
        // }

        if ($this->cantRender) {
            return;
        }

        if (! isset($this->renderSection)) {
            $this->renderSection = $this->getIO()->section();

            $this->renderSection->setMaxHeight($h+1); // +1 for status
        }

        $section = $this->renderSection;

        $section->clear();

        if ($status) {
            $section->writeln($status);
        }

        $isWide = $warehouse[0][0] == static::BOX2;

        if ($isWide) {
            $warehouse[0][0] = static::WALL;
        }

        grid_print($warehouse, [
            static::WALL => '█',
            static::EMPTY => ' ',
            static::BOX => $isWide ? '[' : 'O',
            static::BOX2 => ']',
            static::ROBOT => '@',
        ], fn($line) => $section->writeln($line));

        // prevent vscode terminal flickering
        usleep(1);
    }

    /**
     * Run the first part of the challenge.
     */
    public function part1(): int
    {
        $warehouse = $this->runSequence($this->warehouse(), $this->moves());

        $sum = 0;

        foreach (grid_search($warehouse, static::BOX) as [$x, $y]) {
            $sum += $x + $y * 100;
        }

        return $sum;
    }

    /**
     * Run the second part of the challenge.
     */
    public function part2(): int
    {
        $warehouse = $this->runSequence($this->wideWarehouse(), $this->moves());

        $sum = 0;

        foreach (grid_search($warehouse, static::BOX) as [$x, $y]) {
            $sum += $x + $y * 100;
        }

        return $sum;
    }
}
