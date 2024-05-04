<?php

namespace Mike\AdventOfCode\Solutions\Year2015\Day21\Characters;

use Exception;
use Mike\AdventOfCode\Solutions\Year2015\Day21\Characters\Character;
use Mike\AdventOfCode\Solutions\Year2015\Day21\Inventory\Shop;
use Mike\AdventOfCode\Solutions\Year2015\Day21\Inventory\PlayerInventory;

class Player extends Character
{
    /**
     * The shop from where the player can buy items.
     *
     * @var \Mike\AdventOfCode\Solutions\Year2015\Day21\Inventory\Shop
     */
    protected Shop $shop;

    /**
     * The inventory where the player will store items.
     *
     * @var \Mike\AdventOfCode\Solutions\Year2015\Day21\Inventory\PlayerInventory
     */
    protected PlayerInventory $inventory;

    /**
     * Create new Player instance.
     *
     * @return void
     */
    public function __construct(int $hp = 100, int $armor = 0, int $damage = 0)
    {
        $this->inventory = new PlayerInventory;

        parent::__construct($hp, $armor, $damage);
    }

    /**
     * Get the inventory of the player.
     *
     * @return \Mike\AdventOfCode\Solutions\Year2015\Day21\Inventory\PlayerInventory
     */
    public function getInventory(): PlayerInventory
    {
        return $this->inventory;
    }

    /**
     * Get the player shop.
     *
     * @return \Mike\AdventOfCode\Solutions\Year2015\Day21\Inventory\Shop
     * @throws \Exception
     */
    public function getShop(): Shop
    {
        $this->ensureShopAvailable();

        return $this->shop;
    }

    /**
     * Set the shop where the player can buy items from.
     *
     * @param  \Mike\AdventOfCode\Solutions\Year2015\Day21\Inventory\Shop  $shop
     * @return static
     */
    public function setShop(Shop $shop): static
    {
        $this->shop = $shop;

        return $this;
    }

    /**
     * Make sure the shop has been set.
     *
     * @return $this
     * @throws \Exception
     */
    protected function ensureShopAvailable(): static
    {
        if (is_null($this->shop)) {
            throw new Exception('Tried to get shop from Player, but shop has not been set.');
        }

        return $this;
    }

    /**
     * Buy an item and place it in the inventory.
     *
     * @param  string  $itemName
     * @return static
     * @throws \Exception
     */
    public function buy(string $itemName): static
    {
        $this->ensureShopAvailable();

        $this->inventory->add($this->shop->purchase($itemName));

        return $this;
    }

    /**
     * Get the current armor of the character.
     *
     * @return integer
     */
    public function getArmor(): int
    {
        return $this->inventory->getArmor();
    }

    /**
     * Get the current damage of the character.
     *
     * @return integer
     */
    public function getDamage(): int
    {
        return $this->inventory->getDamage();
    }
}
