<?php

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
