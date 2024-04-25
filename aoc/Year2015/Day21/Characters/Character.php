<?php

namespace Mike\AdventOfCode\Year2015\Day21\Characters;

abstract class Character
{
    /**
     * The current health of the character.
     *
     * @var integer
     */
    protected int $hp = 100;

    /**
     * The current armor of the character.
     *
     * @var integer
     */
    protected int $armor = 0;

    /**
     * The current damage of the character.
     *
     * @var integer
     */
    protected int $damage = 0;

    /**
     * The initial stats for the character.
     *
     * @var array
     */
    protected array $initial = [];

    /**
     * Create new instance of the character.
     *
     * @param  integer  $hp
     * @param  integer  $armor
     * @param  integer  $damage
     * @return void
     */
    public function __construct(int $hp = 100, int $armor = 0, int $damage = 0)
    {
        $this->hp = $hp;
        $this->armor = $armor;
        $this->damage = $damage;

        $this->syncInitial();
    }

    /**
     * Check to see if the character still has health left.
     *
     * @return boolean
     */
    public function isAlive(): bool
    {
        return $this->hp > 1;
    }

    /**
     * Check to see if the character has died.
     *
     * @return boolean
     */
    public function isDead(): bool
    {
        return $this->hp <= 0;
    }

    /**
     * Get the current health of the character.
     *
     * @return integer
     */
    public function getHp(): int
    {
        return $this->hp;
    }

    /**
     * Decrease the hit points of the character by the given amount.
     *
     * @param  integer  $hp
     * @return $this
     */
    public function decreaseHp(int $hp): static
    {
        $this->hp -= $hp;

        return $this;
    }

    /**
     * Get the current armor of the character.
     *
     * @return integer
     */
    public function getArmor(): int
    {
        return $this->armor;
    }

    /**
     * Get the current damage of the character.
     *
     * @return integer
     */
    public function getDamage(): int
    {
        return $this->damage;
    }

    /**
     * Synchronize the initial state of the character with the current state.
     *
     * @return $this
     */
    public function syncInitial(): static
    {
        $props = [
            'hp', 'armor', 'damage',
        ];

        foreach ($props as $prop) {
            $this->initial[$prop] = $this->{'get'.ucfirst($prop)}();
        }

        return $this;
    }
}
