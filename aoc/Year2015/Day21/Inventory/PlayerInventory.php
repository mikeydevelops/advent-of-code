<?php

namespace Mike\AdventOfCode\Year2015\Day21\Inventory;

use Exception;
use Mike\AdventOfCode\Year2015\Day21\Inventory\Items\Item;
use Mike\AdventOfCode\Year2015\Day21\Inventory\Items\ItemCollection;

class PlayerInventory extends ItemCollection
{
    /**
     * Add an item to the array.
     *
     * @param  \Mike\AdventOfCode\Year2015\Day21\Inventory\Items\Item  $item
     * @return static
     */
    public function add(Item $item): static
    {
        $count = count($this->filter(fn(Item $i) => $i->getName() == $item->getName()));

        if ($count) {
            throw new Exception(sprintf('Item with name [%s] already in inventory.', $item->getName()));
        }

        return $this->push($item);
    }

    /**
     * Get the total cost of all items in the inventory.
     *
     * @return integer
     */
    public function getCost(): int
    {
        return $this->map(fn (Item $i) => $i->getCost())->sum();
    }

    /**
     * Get the total armor of all items in the inventory.
     *
     * @return integer
     */
    public function getArmor(): int
    {
        return $this->map(fn (Item $i) => $i->getArmor())->sum();
    }

    /**
     * Get the total damage of all items in the inventory.
     *
     * @return integer
     */
    public function getDamage(): int
    {
        return $this->map(fn (Item $i) => $i->getDamage())->sum();
    }
}
