<?php

use DI\Container;
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

if (! function_exists('aoc')) {
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

if (! function_exists('array_2d_grid')) {
    /**
     * Make 2d grid and fill it with given default value.
     *
     * @template T
     * @param  integer  $height
     * @param  integer  $width
     * @param  T|null  $fill
     * @return T[][]
     */
    function array_2d_grid(int $height, int $width, $fill = null) : array
    {
        return array_fill(0, $height, array_fill(0, $width, $fill));
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
        foreach ($array as $key => $firstItem) {
            $remaining = $array;

            array_splice($remaining, $key, 1);

            if (0 === count($remaining)) {
                yield [$firstItem];

                continue;
            }

            foreach (array_permutations($remaining) as $permutation) {
                array_unshift($permutation, $firstItem);

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
     * @return array
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
            $grid[$y - 1][$x - 1] ?? $default,
            $grid[$y - 1][$x    ] ?? $default,
            $grid[$y - 1][$x + 1] ?? $default,
            $grid[$y    ][$x + 1] ?? $default,
            $grid[$y + 1][$x + 1] ?? $default,
            $grid[$y + 1][$x    ] ?? $default,
            $grid[$y + 1][$x - 1] ?? $default,
            $grid[$y    ][$x - 1] ?? $default,
        ];

        if (! $setDefault) {
            $adjacent = array_filter($adjacent, function ($v) use ($missing) {
                return $v !== $missing;
            });
        }

        return $adjacent;
    }
}

if (! function_exists('grid_animate')) {
    /**
     * Animate a 2d grid with given amount of frames.
     * Update cell using the provided callback.
     *
     * @param  array  $grid
     * @param  integer  $frames
     * @param  callable  $callback
     * @return array
     */
    function grid_animate(array $grid, int $frames, callable $callback, callable $before = null, callable $after = null): array
    {
        foreach (range(0, $frames - 1) as $frame) {
            $newGrid = $before ? call_user_func($before, $grid) : $grid;

            foreach ($grid as $y => $row) {
                foreach ($row as $x => $cell) {
                    $newGrid[$y][$x] = call_user_func($callback, [
                        'x' => $x,
                        'y' => $y,
                        'value' => $cell,
                        'adjacent' => grid_get_adjacent($grid, $x, $y),
                    ], $frame, $grid);
                }
            }

            $grid = $after ? call_user_func($after, $newGrid) : $newGrid;
        }

        return $grid;
    }
}

if (! function_exists('array_combinations')) {
    /**
     * Create combinations for 2d array.
     * @return \Generator<array>
     */
    function array_combinations(array $array) : Generator
    {
        if (! empty($array)) {
            if ($u = array_pop($array)) {
                foreach (array_combinations($array) as $p) {
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
     * @param  bool  $trim Trim the string before splitting.
     * @param  bool  $ignoreEmpty  Ignore empty lines.
     * @param  bool  $trimLines  Trim each line after splitting.
     * @return string[]
     */
    function split_lines(string $string, bool $trim = true, bool $ignoreEmpty = true, bool $trimLines = true): array
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
