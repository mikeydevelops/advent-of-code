<?php

namespace Mike\AdventOfCode\Solutions\Year2015\Day21\Inventory\Items;

use Exception;

use Mike\AdventOfCode\Solutions\Year2015\Day21\Inventory\Items\Armor;
use Mike\AdventOfCode\Solutions\Year2015\Day21\Inventory\Items\Ring;
use Mike\AdventOfCode\Solutions\Year2015\Day21\Inventory\Items\Weapon;

abstract class Item
{
    /**
     * The name of the item.
     *
     * @var string
     */
    protected string $name;

    /**
     * The amount of damage the item provides.
     *
     * @var integer
     */
    protected $damage = 0;

    /**
     * The amount of armor the item provides.
     *
     * @var integer
     */
    protected $armor = 0;

    /**
     * The amount of gold the item costs.
     *
     * @var integer
     */
    protected $cost = 0;

    /**
     * The available items.
     *
     * @var \Mike\AdventOfCode\Solutions\Year2015\Day21\Inventory\Items\ItemCollection<\Mike\AdventOfCode\Solutions\Year2015\Day21\Inventory\Items\Item>
     */
    protected static ItemCollection $items;

    /**
     * Create new instance of an Item.
     *
     * @param  string  $name
     * @param  integer  $cost
     * @param  integer  $damage
     * @param  integer  $armor
     * @return void
     * @throws \Exception
     */
    public function __construct(string $name, int $cost, int $damage = 0, int $armor = 0)
    {
        $this->name = $name;
        $this->cost = $cost;

        if (! $damage && ! $armor) {
            throw new Exception('Tried to create new Item without damage or armor set.');
        }

        $this->damage = $damage;
        $this->armor = $armor;
    }

    /**
     * Get the name of the item.
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Get the cost of the item.
     *
     * @return integer
     */
    public function getCost(): int
    {
        return $this->cost;
    }

    /**
     * Get the damage of the item.
     *
     * @return integer
     */
    public function getDamage(): int
    {
        return $this->damage;
    }

    /**
     * Get the armor of the item.
     *
     * @return integer
     */
    public function getArmor(): int
    {
        return $this->armor;
    }

    /**
     * Get the names of available weapons.
     *
     * @return string[]
     */
    public static function listWeapons(): array
    {
        return static::items()
            ->filter(fn(Item $i) => $i instanceof Weapon)
            ->map(fn(Weapon $i) => $i->getName())
            ->toArray();
    }

    /**
     * Get the names of available armor.
     *
     * @return string[]
     */
    public static function listArmor(): array
    {
        return static::items()
            ->filter(fn(Item $i) => $i instanceof Armor)
            ->map(fn(Armor $i) => $i->getName())
            ->toArray();
    }

    /**
     * Get the names of available ringss.
     *
     * @return string[]
     */
    public static function listRings(): array
    {
        return static::items()
            ->filter(fn(Item $i) => $i instanceof Ring)
            ->map(fn(Ring $i) => $i->getName())
            ->toArray();
    }

    public static function items() : ItemCollection
    {
        if (! isset(static::$items)) {
            $items = new ItemCollection([
                new Weapon('Dagger', cost: 8, damage: 4),
                new Weapon('Short Sword', cost: 10, damage: 5),
                new Weapon('War Hammer', cost: 25, damage: 6),
                new Weapon('Long Sword', cost: 40, damage: 7),
                new Weapon('Great Axe', cost: 74, damage: 8),

                new Armor('Leather', cost: 13, armor: 1),
                new Armor('Chain mail', cost: 31, armor: 2),
                new Armor('Iron', cost: 53, armor: 3),
                new Armor('Diamond', cost: 75, armor: 4),
                new Armor('Netherite', cost: 102, armor: 5),

                new Ring('Damage +1', cost: 25, damage: 1),
                new Ring('Damage +2', cost: 50, damage: 2),
                new Ring('Damage +3', cost: 100, damage: 3),
                new Ring('Armor +1', cost: 20, armor: 1),
                new Ring('Armor +2', cost: 40, armor: 2),
                new Ring('Armor +3', cost: 80, armor: 3),
            ]);

            static::$items = $items;
        }

        return static::$items;
    }
}
