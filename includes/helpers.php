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
