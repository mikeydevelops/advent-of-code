<?php

namespace Mike\AdventOfCode\Year2015\Day21\Inventory\Items;

use Countable;
use Mike\AdventOfCode\Year2015\Day21\Inventory\Items\Item;

class ItemCollection implements Countable
{
    /**
     * The items.
     *
     * @var \Mike\AdventOfCode\Year2015\Day21\Inventory\Items\Item[]
     */
    protected array $items = [];

    /**
     * The current number of items in the collection
     *
     * @var integer
     */
    protected $count = 0;

    /**
     * Create new instance of Item Collection.
     *
     * @param  array  $items
     * @return void
     */
    public function __construct(array $items = [])
    {
        $this->items = $items;
        $this->count = count($this->items);
    }

    /**
     * Filter the collection.
     * @param  callable|null  $callback
     * @return static
     */
    public function filter(callable $callback = null): static
    {
        return new static(array_filter($this->items, $callback, ARRAY_FILTER_USE_BOTH));
    }

    /**
     * Map over the collection items.
     *
     * @param  callable  $callback
     * @return static
     */
    public function map(callable $callback): static
    {
        return new static(array_map($callback, $this->items, array_keys($this->items)));
    }

    /**
     * Add new item to the array.
     *
     * @param  \Mike\AdventOfCode\Year2015\Day21\Inventory\Items\Item  $item
     * @return static
     */
    public function push(Item $item): static
    {
        $this->items[] = $item;
        $this->count ++;

        return $this;
    }

    /**
     * Get the first item in the collection.s
     *
     * @param  mixed  $default
     * @return \Mike\AdventOfCode\Year2015\Day21\Inventory\Items\Item|null|mixed
     */
    public function first($default = null): mixed
    {
        if (empty($this->items)) {
            return $default;
        }

        reset($this->items);

        return current($this->items);
    }

    /**
     * Sum all of the items of the collection.
     *
     * @return integer
     */
    public function sum(): int
    {
        return array_sum($this->items);
    }

    /**
     * Get the underlying array.
     *
     * @return array
     */
    public function toArray(): array
    {
        return $this->items;
    }

    /**
     * Get number of items in the collection.
     *
     * @return integer
     */
    public function count(): int
    {
        return $this->count;
    }
}
