<?php

namespace Mike\AdventOfCode\Year2015\Day21\Inventory;

use Exception;
use Mike\AdventOfCode\Year2015\Day21\Inventory\Items\Item;
use Mike\AdventOfCode\Year2015\Day21\Inventory\Items\ItemCollection;

class Shop
{
    /**
     * The items that can be sold.
     *
     * @var \Mike\AdventOfCode\Year2015\Day21\Inventory\Items\ItemCollection
     */
    protected ItemCollection $items;

    /**
     * Names of the products out of stock.
     *
     * @var array
     */
    protected array $outOfStock = [];

    /**
     * Create new instance of Shop.
     *
     * @return void
     */
    public function __construct()
    {
        $this->items = clone Item::items();
    }

    /**
     * Purchase an item.
     *
     * @param  string  $itemName
     * @return \Mike\AdventOfCode\Year2015\Day21\Inventory\Items\Item
     * @throws \Exception
     */
    public function purchase(string $itemName): Item
    {
        if (in_array($itemName, $this->outOfStock)) {
            throw new Exception(sprintf(
                'Tried to purchase item that is out of stock. Item name: [%s]',
                $itemName
            ));
        }

        $item = $this->items
            ->filter(fn(Item $i) => $i->getName() == $itemName)
            ->first();

        if (! $item) {
            throw new Exception(sprintf(
                'Tried to purchase invalid item. Item name: [%s]',
                $itemName,
            ));
        }

        $this->outOfStock[] = $item->getName();

        return $item;
    }
}
