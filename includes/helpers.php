<?php

use Mike\AdventOfCode\Console\Application;
use Mike\AdventOfCode\Support\Env;

if (! function_exists('array_wrap')) {
    /**
     * Wrap a value in array.
     * If value is already array return the same array.
     * If value is null return empty array.
     *
     * @param  mixed  $arr
     * @return array
     */
    function array_wrap($arr) : array
    {
        if (is_null($arr)) {
            return [];
        }

        if (is_array($arr)) {
            return $arr;
        }

        return [$arr];
    }
}

if (! function_exists('join_path')) {
    /**
     * Join paths together using the OS directory separator.
     */
    function join_path(string $base, string ...$append): string
    {
        $parts = array_filter([
            rtrim($base, '\\/'),
            ...array_map(fn(string $p) => trim($p, '\\/'), $append)
        ]);

        return implode(DIRECTORY_SEPARATOR, $parts);
    }
}


if (! function_exists('str_kebab')) {
    /**
     * Convert given string to kebab case.
     */
    function str_kebab(string $string): string
    {
        if (! ctype_lower($string)) {
            $string = preg_replace('/\s+/u', '', ucwords($string));

            $string = mb_strtolower(preg_replace('/(.)(?=[A-Z])/u', '$1-', $string));
        }

        return $string;
    }
}

if (! function_exists('value')) {
    /**
     * Return the default value of the given value.
     */
    function value($value, ...$args): mixed
    {
        return $value instanceof Closure ? $value(...$args) : $value;
    }
}

if (! function_exists('env')) {
    /**
     * Gets the value of an environment variable.
     */
    function env(string $key, mixed $default = null): mixed
    {
        return Env::get($key, $default);
    }
}

if (! function_exists('array_get_dot')) {
    /**
     * Access value from multidimensional array using dot notation.
     */
    function array_get_dot(array $array, string $key, $default = null): mixed
    {
        $current = $array;
        $key = strtok($key, '.');

        while ($key !== false) {
            if (! isset($current[$key])) {
                return $default;
            }

            $current = $current[$key];
            $key = strtok('.');
        }

        return $current;
    }
}

if (! function_exists('array_set_dot')) {
    /**
     * Access value from multidimensional array using dot notation.
     */
    function array_set_dot(array &$array, string $key, $value = null): mixed
    {
        $current = &$array;
        $key = strtok($key, '.');

        while ($key !== false) {
            if (! isset($current[$key])) {
                $current[$key] = [];
            }

            $current = &$current[$key];
            $key = strtok('.');


            if ($key === false) {
                $current = $value;
            }
        }

        unset($current);

        return $array;
    }
}

if (! function_exists('array_has_dot')) {
    /**
     * Check if a value in a multidimensional array is set using dot notation.
     */
    function array_has_dot(array $array, string $key): bool
    {
        $current = $array;
        $key = strtok($key, '.');

        while ($key !== false) {
            if (! isset($current[$key])) {
                return false;
            }

            $current = $current[$key];
            $key = strtok('.');
        }

        return true;
    }
}

if (! function_exists('explode_trim')) {
    /**
     * Explode safely, removing empty parts of the array.
     *
     * @param  string  $delimiter
     * @param  string  $input
     * @param  integer  $limit
     * @return array
     */
    function explode_trim(string $delimiter, string $input, int $limit = PHP_INT_MAX) : array
    {
        return array_values(array_filter(array_map('trim', explode($delimiter, $input, $limit))));
    }
}

if (! function_exists('app')) {
    /**
     * Get the available container instance.
     *
     * @param  string|null  $abstract
     * @param  array  $parameters
     * @return \Mike\AdventOfCode\Console\Application|mixed
     */
    function app($abstract = null, array $parameters = [])
    {
        if (is_null($abstract)) {
            return Application::instance();
        }

        return Application::instance()->make($abstract, $parameters);
    }
}

if (! function_exists('base_path')) {
    /**
     * Get the base path of the app.
     */
    function base_path(string ... $append): string
    {
        return app()->basePath(...$append);
    }
}

if (! function_exists('human_filesize')) {
    /**
     * Convert byte file size to human readable size.
     *
     * @param  integer  $bytes
     * @param  integer|true  $precision  IF true only the suffix will be returned.
     * @return string
     */
    function human_filesize(int $bytes, $precision = 2)
    {
        $size   = ['B', 'kB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB'];
        $factor = floor((strlen($bytes) - 1) / 3);

        if ($precision === true) {
            return @$size[$factor];
        }

        return sprintf("%.{$precision}f", $bytes / pow(1024, $factor)) . ' ' . @$size[$factor];
    }
}

if (! function_exists('human_duration')) {
    function human_duration(float $ms, array $units = null, string $delimiter = ' ')
    {
        $_ms = $ms;
        $ns = $ms * 1000 * 1000;
        $units = array_wrap($units ?? ['y', 'mo', 'w', 'd', 'h', 'm', 's', 'ms', 'us', 'ns']);

        $us = 1 * 1000;
        $ms = $us * 1000;
        $s = $ms * 1000;
        $m = $s * 60;
        $h = $m * 60;
        $d = $h * 24;
        $w = $d * 7;
        $mo = floor($d * 30.437);
        $y = $mo * 12;

        $unitValues = compact('y', 'mo', 'w', 'd', 'h', 'm', 's', 'ms', 'us') + ['ns' => 1];

        $remaining = $ns;
        $lastIdx = count($units) - 1;

        $values = [];

        foreach ($units as $idx => $unit) {
            $isLast = $idx == $lastIdx;

            $unitValue = $unitValues[$unit];

            $count = $isLast ? $remaining / $unitValue : floor($remaining / $unitValue);
            $values[$unit] = intval($count);

            $remaining -= $count * $unitValue;
        }

        $values = array_filter($values);
        $values = array_map(fn ($v, $u) => "{$v}$u", $values, array_keys($values));

        return implode($delimiter, $values);
    }
}

if (! function_exists('array_find_repeating')) {
    /**
     * Find repeating items in an array.
     *
     * @param  array  $items
     * @return array
     */
    function array_find_repeating(array $items): array
    {
        $repeating = [];

        $count = count($items);

        foreach ($items as $idx => $item) {
            $next = $idx+1 < $count ? $items[$idx + 1] : false;

            if ($next === false) {
                break;
            }

            if ($item !== $next) {
                continue;
            }

            if (in_array($item, $repeating)) {
                continue;
            }

            $repeating[] = $item;
        }

        return $repeating;
    }
}


if (! function_exists('array_count_consecutive')) {
    /**
     * Find repeating items in an array.
     *
     * @param  array  $items
     * @return array
     */
    function array_count_consecutive(array $items): array
    {
        $repeating = [];

        $current = 0;

        foreach ($items as $idx => $item) {
            $prev = $items[$idx - 1] ?? false;

            if (isset($repeating[$current]) && $repeating[$current][0] === $prev && $item !== $prev) {
                $current++;
            }

            if ($prev !== $item) {
                continue;
            }

            if (! isset($repeating[$current])) {
                $repeating[$current] = [$item, 1];
            }

            $repeating[$current][1] += 1;
        }

        return $repeating;
    }
}


if (! function_exists('grid_make')) {
    /**
     * Make 2d grid and fill it with given default value.
     *
     * @template T
     * @param  integer  $width
     * @param  integer  $height
     * @param  T|callable|null  $fill  if callable is provided, it will be called each time with the x and y and its result will be used for cell value.
     * @return T[][]
     */
    function grid_make(int $width, int $height, $fill = null) : array
    {
        $grid = [];

        for ($y = 0; $y < $height; $y ++) {
            for ($x = 0; $x < $width; $x++) {
                $grid[$y][$x] = is_callable($fill) ? $fill($x, $y) : $fill;
            }
        }

        return $grid;
    }
}

if (! function_exists('array_sliding')) {
    /**
     * Create chunks representing a "sliding window" view of the items in the array.
     *
     * @template T[]
     * @param  T[] $array
     * @param  int  $size
     * @param  int  $step
     * @return T[][]
     */
    function array_sliding(array $array, int $size = 2, int $step = 1): array
    {
        $chunks = floor((count($array) - $size) / $step) + 1;

        $windows = [];

        foreach (range(1, $chunks) as $number) {
            $windows[] = array_slice($array, ($number - 1) * $step, $size);
        }

        return $windows;
    }
}

if (! function_exists('array_permutations')) {
    /**
     * Calculate permutations of array.
     *
     * @template T
     * @param  T[]  $array
     * @param  integer  $size
     * @return \Generator<T[]>
     */
    function array_permutations(array $array): Generator
    {
        if (count($array) === 1) {
            yield $array;
            return;
        }

        foreach ($array as $key => $value) {
            $remaining = [];

            foreach ($array as $k => $v) {
                if ($k !== $key) {
                    $remaining[] = $v;
                }
            }

            foreach (array_permutations($remaining) as $permutation) {
                $permutation[] = $value;
                yield $permutation;
            }
        }
    }
}

if (! function_exists('iterate_string')) {
    /**
     * Iterate through a string using a generator.
     *
     * @param  string  $string
     * @return \Generator<string>
     */
    function iterate_string(string $string): Generator
    {
        for($i = 0; $i < strlen($string); $i++) {
            yield $string[$i];
        }
    }
}

if (! function_exists('string_has_consecutive_characters')) {
    /**
     * Check to see if a string contains $limit consecutive characters.
     */
    function string_has_consecutive_characters(string $string, int $limit = 1): bool
    {
        if ($limit <= 0) {
            return false;
        }

        $len = strlen($string);
        $matching = 0;

        foreach (str_split($string) as $idx => $letter) {
            if ($matching == $limit) {
                return true;
            }

            if ($idx + $limit >= $len) {
                return false;
            }

            $ord = ord($letter);

            foreach (range(1, $limit) as $next) {
                if (chr($ord + $next) != $string[$idx + $next]) {
                    $matching = 0;

                    continue 2;
                }

                $matching ++;
            }
        }

        return true;
    }
}

if (! function_exists('strpos_any')) {
    /**
     * Search for multiple values in a string.
     */
    function strpos_any(array $needles, string $haystack, bool $strict = false): int|false
    {
        foreach ($needles as $needle) {
            if (($idx = strpos($haystack, $needle, $strict)) !== false) {
                return $idx;
            }
        }

        return false;
    }
}

if (! function_exists('string_increment')) {
    /**
     * Increment one character of given string.
     */
    function string_increment(string $string, int $times = 1) : string
    {
        for ($i = 0; $i < $times; $i++) {
            $string ++;
        }

        return $string;
    }
}

if (! function_exists('array_is_assoc')) {
    /**
     * Check to see if given array is associative or not.
     */
    function array_is_assoc(array $array): bool
    {
        $keys = array_keys($array);

        return array_keys($keys) !== $keys;
    }
}

if (! function_exists('number_combinations')) {
    /**
     * Generate all possible combinations for $size numbers that sum to $sum starting from $min.
     * @return int[][]
     */
    function number_combinations(int $sum, int $size, int $min = 1): array
    {
        if ($size <= 1) {
            return [ [ $sum ] ];
        }

        $combinations = [];

        for ($i = $min; $i < $sum; $i++) {
            $partial = number_combinations($sum - $i, $size - 1, $min);

            $combinations = array_merge($combinations, array_map(function ($combination) use ($i) {
                return array_merge([$i], $combination);
            }, $partial));
        }

        return array_unique($combinations, SORT_REGULAR);
    }
}

if (! function_exists('grid_get_adjacent')) {
    /**
     * Get all adjacent values from a 2d grid given x and y.
     *
     * @param  array  $grid
     * @param  integer  $x
     * @param  integer  $y
     * @param  mixed  $default
     * @return array{top-left:mixed,top:mixed,top-right:mixed,right:mixed,bottom-right:mixed,bottom:mixed,bottom-left:mixed,left:mixed}
     */
    function grid_get_adjacent(array $grid, int $x, int $y, $default = null): array
    {
        // should we set default value if a cell is out of bounds?
        // we should set it if it was provided when the function was called.
        $setDefault = count(func_get_args()) == 4;

        // an arbitrary value to use when there is a missing value
        $missing = '###';

        $default = $setDefault ? $default : $missing;

        $adjacent = [
            'top-left' => $tl = $grid[$y - 1][$x - 1] ?? $default, // top left
            'top' => $t = $grid[$y - 1][$x    ] ?? $default, // top
            'top-right' => $tr = $grid[$y - 1][$x + 1] ?? $default, // top right
            'right' => $r = $grid[$y    ][$x + 1] ?? $default, // right
            'bottom-right' => $br = $grid[$y + 1][$x + 1] ?? $default, // bottom right
            'bottom' => $b = $grid[$y + 1][$x    ] ?? $default, // bottom
            'bottom-left' => $bl = $grid[$y + 1][$x - 1] ?? $default, // bottom left
            'left' => $l = $grid[$y    ][$x - 1] ?? $default, // left
        ];

        if (! $setDefault) {
            $adjacent = array_filter($adjacent, fn($v) => $v !== $missing);
        }

        return $adjacent;
    }
}

if (! function_exists('grid_get_adjacent_xy')) {
    /**
     * Get all adjacent values from a 2d grid given x and y. Returns coordinates and value instead of value only.
     *
     * @param  array  $grid
     * @param  integer  $x
     * @param  integer  $y
     * @param  mixed  $default
     * @return array{top-left:mixed,top:mixed,top-right:mixed,right:mixed,bottom-right:mixed,bottom:mixed,bottom-left:mixed,left:mixed}
     */
    function grid_get_adjacent_xy(array $grid, int $x, int $y, $default = null): array
    {
        // hack based on num args, to not brake intended functionality
        $adj = func_num_args() == 3
            ? grid_get_adjacent($grid, $x, $y)
            : grid_get_adjacent($grid, $x, $y, $default);

        $map = [
            'top-left'     => [-1, -1],
            'top'          => [ 0, -1],
            'top-right'    => [ 1, -1],
            'right'        => [ 1,  0],
            'bottom-right' => [ 1,  1],
            'bottom'       => [ 0,  1],
            'bottom-left'  => [-1,  1],
            'left'         => [-1,  0],
        ];

        foreach ($adj as $dir => $v) {
            [$dX, $dY] = $map[$dir];

            $adj[$dir] = [$x + $dX, $y + $dY, $v];
        }

        return $adj;
    }
}

if (! function_exists('grid_animate')) {
    /**
     * Animate a 2d grid with given amount of frames.
     * Update cell using the provided callback.
     *
     * @template TGrid
     *
     * @param  TGrid  $grid  The 2d grid.
     * @param  integer|\Generator<integer>  $frames  The amount of frames the grid will be changed.
     *                                               If a generator callback is provided, its value
     *                                               will be used for frame number.
     * @param  callable(array{x: int, y: int, value: mixed, adjacent: array{top-left:mixed,top:mixed,top-right:mixed,right:mixed,bottom-right:mixed,bottom:mixed,bottom-left:mixed,left:mixed}} $cell, int $frame, TGrid $grid): mixed  $callback  Used to update the cell value for each frame.
     * @param  callable(TGrid $grid, int $frame): TGrid|null  $before [optional] Modify the grid before the frame is animated.
     *                             If provided will be called right before a new frame is started animating.
     * @param  callable(TGrid $grid, int $frame): TGrid|null  $after [optional] Modify the grid after the frame has been animated.
     *                            If provided will be called right after a frame has been animated.
     * @return TGrid
     */
    function grid_animate(array $grid, int|\Generator $frames, callable $callback, callable $before = null, callable $after = null): array
    {
        $state = new stdClass;
        $state->break = false;

        $frameGen = is_callable($frames) ? $frames : (fn() => yield from range(0, $frames - 1))();

        foreach ($frameGen as $frame) {
            $newGrid = $before ? call_user_func($before, $grid, $frame) : $grid;

            foreach (grid_walk($grid) as [$x, $y, $value]) {
                $adjacent = grid_get_adjacent($grid, $x, $y);
                $cell = compact('x', 'y', 'value', 'adjacent');

                $newGrid[$y][$x] = call_user_func($callback, $cell, $frame, $grid);
            }

            $grid = $after ? call_user_func($after, $newGrid, $frame) : $newGrid;
        }

        return $grid;
    }
}

if (! function_exists('array_combinations')) {
    /**
     * Create combinations for 2d array.
     * @return \Generator<array>
     */
    function array_combinations(array ...$arrays) : Generator
    {
        if (! empty($arrays)) {
            if ($u = array_pop($arrays)) {
                foreach (array_combinations(...$arrays) as $p) {
                    foreach ($u as $v) {
                        yield array_merge($p, [$v]);
                    }
                }
            }
        } else {
            yield [];
        }
    }
}

if (! function_exists('split_lines')) {
    /**
     * Split string into lines.
     *
     * @param  string  $string
     * @param  callable|null  $map  Map over each line with given callback, after all other filters.
     * @param  boolean  $trim Trim the string before splitting.
     * @param  boolean  $ignoreEmpty  Ignore empty lines.
     * @param  boolean  $trimLines  Trim each line after splitting.
     * @return string[]
     */
    function split_lines(string $string, ?callable $map = null, bool $trim = true, bool $ignoreEmpty = true, bool $trimLines = true): array
    {
        if ($trim) {
            $string = trim($string);
        }

        $result = preg_split('/\r?\n/', $string);

        if ($ignoreEmpty) {
            $result = array_filter($result);
        }

        if ($trimLines) {
            $result = array_map('trim', $result);
        }

        if (! is_null($map)) {
            $result = array_map($map, $result);
        }

        return $result;
    }
}

if (! function_exists('caesar_char')) {
    /**
     * Rotate a character using caesar's cipher.
     *
     * @param  string  $char
     * @param  integer $n  The amount of times to rotate the character.
     * @return string
     */
    function caesar_char(string $char, int $n): string
    {
        $code = ord($char);

        // these help with upper or lower case characters.
        $start = $code > 96 && $code < 123 ? 97 : 65;
        $end = $start == 97 ? 122 : 90;

        for ($i = 0; $i < $n; $i++) {
            $code ++;

            if ($code > $end) {
                $code = $start;
            }
        }

        return chr($code);
    }
}

if (! function_exists('array_rotate')) {
    /**
     * Rotate array elements $amount times to the right.
     */
    function array_rotate(array $array, int $amount = 1): array
    {
        for ($i = 0; $i < $amount; $i ++) {
            array_unshift($array, array_pop($array));
        }

        return $array;
    }
}

if (! function_exists('array_flip_row_column')) {

    /**
     * Flip the rows and columns in 2d array.
     *
     * i.e. if array is accessed as array[y][x] it will become array[x][y].
     *
     * @param  array  $array
     * @return array
     */
    function array_flip_row_column(array $array): array
    {
        $result = [];

        for ($i = 0; $i < count($array[0]); $i++) {
            $result[] = array_column($array, $i);
        }

        return $result;
    }
}

if (! function_exists('array_group_by')) {
    /**
     * Group items using provided key.
     */
    function array_group_by(array $array, callable|string|int $key): array
    {
        $result = [];

        foreach ($array as $idx => $item) {
            $k = $key;

            if (is_callable($k)) {
                $k = $k($item, $idx, $array);
            }

            $result[$k][] = $item;
        }

        return $result;
    }
}

if (! function_exists('array_flat')) {
    /**
     * Flatten multidimensional array.
     */
    function array_flat(array $array): array
    {
        return iterator_to_array(
            new RecursiveIteratorIterator(new RecursiveArrayIterator($array)),
            false,
        );
    }
}

if (! function_exists('array_merge_alternating')) {
    /**
     * Combine arrays into one alternating their values.
     */
    function array_merge_alternating(array ...$arrays): array
    {
        $result = [];

        $lengths = array_map('count', $arrays);
        $maxLength = max($lengths);

        for ($i = 0; $i < $maxLength; $i++) {
            foreach ($arrays as $idx => $array) {
                if ($i >= $lengths[$idx]) {
                    continue;
                }

                $result[] = $array[$i];
            }
        }

        return $result;
    }
}

if (! function_exists('grid_walk'))
{
    /**
     * Iterate over 2d grid array.
     *
     * @return \Generator<array>
     */
    function grid_walk(array $grid): \Generator
    {
        $y = count($grid);
        $x = count($grid[0]);

        for ($row = 0; $row < $y; $row++) {
            for ($col = 0; $col < $x; $col++) {
                yield [$col, $row, $grid[$row][$col]];
            }
        }
    }
}

if (! function_exists('word_search'))
{

    /**
     * Find all occurrences of given word in a 2d grid of characters.
     *
     * @param  array<string[]>
     * @return array<int[]>
     */
    function word_search(array $grid, string $search): array
    {
        $searchLen = strlen($search);
        $occurrences = [];

        $directions = [
            [-1, -1, 'diag-top-left'],
            [-1,  0, 'right-left'],
            [-1,  1, 'diag-bottom-left'],
            [ 0, -1, 'bottom-top'],
            [ 0,  1, 'top-bottom'],
            [ 1, -1, 'diag-top-right'],
            [ 1,  0, 'left-right'],
            [ 1,  1, 'diag-bottom-right'],
        ];

        foreach (grid_walk($grid) as [$col, $row, $char]) {
            // skip if the first char does not match
            if ($char !== $search[0]) continue;

            foreach ($directions as $dir) {
                $x = $col + $dir[0];
                $y = $row + $dir[1];

                for ($i = 1; $i < $searchLen; $i++) {
                    if (($grid[$y][$x] ?? null) !== $search[$i]) break;

                    $x += $dir[0];
                    $y += $dir[1];
                }

                if ($i === $searchLen) {
                    $occurrences[] = [$row, $col, $dir[2]];
                }
            }
        }

        return $occurrences;
    }
}

if (! function_exists('combinations'))
{
    /**
     * Generate combinations of array items with given limit.
     *
     * @see https://docs.python.org/3/library/itertools.html#itertools.combinations
     */
    function combinations(array $items, int $r): \Generator {
        $n = count($items);

        if ($r > $n || $r < 0) {
            return; // No combinations possible
        }

        $indices = range(0, $r - 1);

        yield array_map(fn($i) => $items[$i], $indices);

        while (true) {
            $i = $r - 1;

            while ($i >= 0 && $indices[$i] == $i + $n - $r) {
                $i --;
            }

            if ($i < 0) {
                return;
            }

            $indices[$i] ++;

            for ($j = $i + 1; $j < $r; $j ++) {
                $indices[$j] = $indices[$j - 1] + 1;
            }

            yield array_map(fn($i) => $items[$i], $indices);
        }
    }
}

if (! function_exists('grid_search'))
{
    /**
     * Searches the 2d grid for a given value and returns the first corresponding x, y if successful
     *
     * @param  array<mixed[]>  $haystack  The grid.
     * @param  mixed[]|mixed  $needle  The searched value. If the needle is an array.
     *                         If needle is a string, the comparison is done in a case-sensitive manner.
     * @param  boolean  $includeValue  If true, value of cell will be appended to position.
     * @param  boolean  $strict [optional] If the third parameter strict is set to true then the
     *                          grid_search function will also check the types of the needle in the haystack.
     *
     * @return \Generator<array{int,int}> an array of containing the X and Y position for needle
     *                        if it is found in the grid, empty array otherwise.
     */
    function grid_search(array $haystack, $needle, bool $includeValue = true, bool $strict = false): \Generator
    {
        $needles = is_array($needle) || is_callable($needle) ? $needle : [$needle];
        $matches = 0;

        if (is_callable($needles)) {
            foreach (grid_walk($haystack) as [$x, $y, $v]) {
                if ($needles($v, $x, $y) === true) {
                    $matches++;
                    yield $includeValue ? [$x, $y, $v] : [$x, $y];
                }
            }

            goto ret;
        }

        foreach (grid_walk($haystack) as [$x, $y, $v]) {
            foreach ($needles as $needle) {
                if ((!$strict && $v == $needle) || ($strict && $v === $needle)) {
                    $matches ++;
                    yield $includeValue ? [$x, $y, $v] : [$x, $y];
                }
            }
        }

        ret:
        if (! $matches) {
            yield [];
        }
    }
}


if (! function_exists('array_cartesian'))
{
    /**
     * Generate cartesian product of array items.
     *
     * @param  array  ...$arrays
     * @return Generator<array>
     */
    function array_cartesian(array ...$arrays)
    {
        $results = [[]];

        foreach ($arrays as $index => $array) {
            $append = [];

            foreach ($results as $product) {
                foreach ($array as $item) {
                    $product[$index] = $item;

                    $append[] = $product;
                }
            }

            $results = $append;
        }

        yield from $results;
    }
}

if (! function_exists('grid_count_values'))
{
    /**
     * Count the values of a 2d grid.
     *
     * @param  array<string[]|integer[]>  $grid
     * @param  string|integer|array|null  $needle  [optional]. If provided, only the cells containing
     *                                             this value will be counted. if array is provided
     *                                             only cells matching the values of the array are counted.
     * @return  array<string|integer,integer>|integer  If needle was provided, only the count for
     * that needle is returned, else all unique values and their counts.
     */
    function grid_count_values(array $grid, string|int|array $needle = null): array|int
    {
        $needles = is_array($needle) ? $needle : (is_null($needle) ? [] : [$needle]);
        $result = [];
        $args = func_num_args();

        if ($args === 2) {
            $result = array_combine($needles, array_fill(0, count($needles), 0));
        }

        foreach (grid_walk($grid) as [$x, $y, $v]) {
            if ($args === 2 && ! in_array($v, $needles)) {
                continue;
            }

            $result[$v] = ($result[$v] ?? 0) + 1;
        }

        if ($args === 2 && ! is_array($needle)) {
            return $result[$needle];
        }

        return $result;
    }
}

if (! function_exists('grid_line'))
{
    /**
     * Get all points between two points that form a straight line in a 2d grid.
     * Using Bresenham's line algorithm
     *
     * @see https://en.wikipedia.org/wiki/Bresenham%27s_line_algorithm
     *
     * @param  array<string[]|integer[]>  $grid
     * @param  array{int,int}  $start  The x and y coordinates of the starting point.
     * @param  array{int,int}  $end  The x and y coordinates of the end point.
     * @param  boolean  $includeStartAndEnd  [optional] Wether to include the starting and end points.
     * @return array<array{int,int,mixed}>
     */
    function grid_line(array $grid, array $start, array $end, bool $includeStartAndEnd = true): array
    {
        [$x, $y] = $start;
        $xDiff = abs($end[0] - $x);
        $xStep = $x < $end[0] ? 1 : -1;
        $yDiff = -abs($end[1] - $y);
        $yStep = $y < $end[1] ? 1 : -1;
        $err = $xDiff + $yDiff;
        $points = [];

        while (true) {

            $points[] = [$x, $y, $grid[$y][$x]];

            if (! $includeStartAndEnd && $x == $start[0] && $y == $start[1])
                array_pop($points);

            if (! $includeStartAndEnd && $x == $end[0] && $y == $end[1])
                array_pop($points);

            if ($x == $end[0] && $y == $end[1])
                break;

            $err2 = 2 * $err;

            if ($err2 >= $yDiff) {
                $err = $err + $yDiff;
                $x = $x + $xStep;
            }

            if ($err2 <= $xDiff) {
                $err = $err + $xDiff;
                $y = $y + $yStep;
            }
        }

        return $points;
    }
}

if (! function_exists('grid_print'))
{
    /**
     * Print a grid to the cli output.
     *
     * @param  array  $grid  The grid.
     * @param  array  $replacers  Associative array to replace the value when printing.
     * @param  callable|null  $rowRenderer  Custom print hook.
     * @return void
     */
    function grid_print(array $grid, array $replacers = null, ?callable $rowRenderer = null): void
    {
        $replacers = $replacers ?? [];

        if (! is_callable($rowRenderer)) {
            $echo = function ($line) {
                echo $line . PHP_EOL;
            };

            $void = fn() => null;

            $rowRenderer = is_null($rowRenderer) ? $echo : $void;
        }

        foreach ($grid as $row) {
            $line = '';

            foreach ($row as $pixel) {
                $line .= $replacers[$pixel] ?? $pixel;
            }

            $line;

            $rowRenderer($line);
        }
    }
}

if (! function_exists('grid_parse'))
{
    /**
     * Parse a 2d grid from string.
     *
     * @param  string  $grid  The grid to parse.
     * @param  callable|null  $cellCallback  [optional]  The callback to run on cell parse. Default: intval
     * @param  callable|null  $rowCallback  [optional]  The callback to run on row parse. Default: str_split
     * @return array[]
     */
    function grid_parse(string $grid, ?callable $cellCallback = null, ?callable $rowCallback = null): array
    {
        return array_map(
            fn (array $row) => array_map($cellCallback ?? 'intval', $row),
            array_map($rowCallback ?? 'str_split', split_lines($grid))
        );
    }
}

if (! function_exists('grid_group_by'))
{
    /**
     * Group items using provided key.
     */
    function grid_group_by(array $grid, callable|string|int $key, bool $preserve_keys = false): array
    {
        $result = [];

        foreach ($grid as $idx => $item) {
            $k = $key;

            if (is_callable($k)) {
                $k = $k($item, $idx, $grid);
            }

            if ($preserve_keys) {
                $result[$item[$k]][$idx] = $item;
            } else {
                $result[$item[$k]][] = $item;
            }
        }

        return $result;
    }
}

if (! function_exists('grid_set'))
{
    /**
     * Set values of given points in grid to given value.
     *
     * @template T
     * @param  array<T[]>  $grid  The grid.
     * @param  array<array{int,int}>|array{int,int}  $points  The grid points to update.
     * @param  T|callable  $value  The new value.
     * @return array<T[]>
     */
    function grid_set(array $grid, array $points, $value): array
    {
        // possible just one point instead of set of points.
        if (count($points) === 2 && is_int($points[0]) && is_int($points[1])) {
            $points = [$points];
        }

        foreach ($points as $point) {
            [$x, $y] = $point;
            $grid[$y][$x] = is_callable($value) ? $value($point, $grid[$y][$x]) : $value;
        }

        return $grid;
    }
}
