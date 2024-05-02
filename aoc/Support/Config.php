<?php

namespace Mike\AdventOfCode\Support;

use ArrayAccess;

class Config implements ArrayAccess
{
    /**
     * The configuration array.
     */
    protected array $config = [];

    /**
     * Create new instance of Config class.
     */
    public function __construct(array $config)
    {
        $this->config = $config;
    }

    /**
     * Get an item from the config array using dot.notation.
     */
    public function get(string $key, $default = null): mixed
    {
        return array_get_dot($this->config, $key, value($default));
    }

    /**
     * Set items in configuration using dot.notation.
     */
    public function set(string|array $key, $value = null): void
    {
        $keys = is_array($key) ? $key : [$key => $value];

        foreach ($keys as $key => $value) {
            array_set_dot($this->config, $key, $value);
        }
    }

    /**
     * Check to see if given key exists in config using dot.notation.
     */
    public function has(string $key): bool
    {
        return array_has_dot($this->config, $key);
    }

    /**
     * Determine if the given configuration option exists.
     */
    public function offsetExists($key): bool
    {
        return $this->has($key);
    }

    /**
     * Get a configuration option.
     */
    public function offsetGet($key): mixed
    {
        return $this->get($key);
    }

    /**
     * Set a configuration option.
     */
    public function offsetSet($key, $value): void
    {
        $this->set($key, $value);
    }

    /**
     * Unset a configuration option.
     */
    public function offsetUnset($key): void
    {
        $this->set($key, null);
    }
}
