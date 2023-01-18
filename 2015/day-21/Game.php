<?php

namespace RPGSimulator20XX;

require_once __DIR__ . '/Characters/Character.php';
require_once __DIR__ . '/Characters/Boss.php';
require_once __DIR__ . '/Characters/Player.php';

require_once __DIR__ . '/Inventory/Items/ItemCollection.php';
require_once __DIR__ . '/Inventory/Items/Item.php';
require_once __DIR__ . '/Inventory/Items/Armor.php';
require_once __DIR__ . '/Inventory/Items/Ring.php';
require_once __DIR__ . '/Inventory/Items/Weapon.php';

require_once __DIR__ . '/Inventory/Shop.php';
require_once __DIR__ . '/Inventory/PlayerInventory.php';


use Exception;
use RPGSimulator20XX\Characters\Character;
use RPGSimulator20XX\Inventory\Shop;

class Game
{
    /**
     * The current turn for the game.
     *
     * @var integer
     */
    protected $turn = 0;

    /**
     * The first player.
     *
     * @var \RPGSimulator20XX\Characters\Character
     */
    protected Character $player1;

    /**
     * The second player.
     *
     * @var \RPGSimulator20XX\Characters\Character
     */
    protected Character $player2;

    /**
     * The player who won the game.
     *
     * @var \RPGSimulator20XX\Characters\Character
     */
    protected Character $winner;

    /**
     * The player who lost the game.
     *
     * @var \RPGSimulator20XX\Characters\Character
     */
    protected Character $loser;

    /**
     * The game shop.
     *
     * @var \RPGSimulator20XX\Inventory\Shop
     */
    protected Shop $shop;

    /**
     * Create new instance of Game class.
     *
     * @param  \RPGSimulator20XX\Characters\Character  $player1
     * @param  \RPGSimulator20XX\Characters\Character  $player2
     * @return void
     */
    public function __construct()
    {
        $this->shop = new Shop;
    }

    /**
     * Run the game.
     *
     * @return \RPGSimulator20XX\Characters\Character
     */
    public function run() : Character
    {
        if (is_null($this->player1)) {
            throw new Exception('Player 1 has not been set.');
        }

        if (is_null($this->player2)) {
            throw new Exception('Player 2 has not been set.');
        }

        do {
            $this->turn();
        } while (! $this->isCompleted());

        $this->winner = $this->player1->isAlive() ? $this->player1 : $this->player2;
        $this->loser = $this->player1->isDead() ? $this->player1 : $this->player2;

        return $this->winner;
    }

    /**
     * Check to see if the game has concluded.
     *
     * @return boolean
     */
    public function isCompleted() : bool
    {
        return $this->player1->isDead() || $this->player2->isDead();
    }

    /**
     * Do a game turn.
     *
     * @return $this
     */
    protected function turn() : static
    {
        $attacker = $this->turn % 2 ?  $this->player2 : $this->player1;
        $defender = $this->turn % 2 ? $this->player1 : $this->player2;

        $damage = $attacker->getDamage() - $defender->getArmor();
        $damage = $damage < 1 ? 1 : $damage;

        $defender->decreaseHp($damage);

        $this->turn ++;

        return $this;
    }

    /**
     * Set the first player for the game.
     *
     * @param  \RPGSimulator20XX\Characters\Character  $character
     * @return $this
     */
    public function setPlayer1(Character $character): static
    {
        $this->player1 = $character;

        return $this;
    }

    /**
     * Set the second player for the game.
     *
     * @param  \RPGSimulator20XX\Characters\Character  $character
     * @return $this
     */
    public function setPlayer2(Character $character): static
    {
        $this->player2 = $character;

        return $this;
    }

    /**
     * Get the shop.
     *
     * @return \RPGSimulator20XX\Inventory\Shop
     */
    public function getShop(): Shop
    {
        return $this->shop;
    }

    /**
     * Get the winner character.
     *
     * @return \RPGSimulator20XX\Characters\Character
     * @throws \Exception
     */
    public function getWinner(): Character
    {
        if (is_null($this->winner)) {
            throw new Exception('Cannot get winner, the game has not been run, yet!');
        }

        return $this->winner;
    }

    /**
     * Get the loser character.
     *
     * @return \RPGSimulator20XX\Characters\Character
     * @throws \Exception
     */
    public function getLoser(): Character
    {
        if (is_null($this->loser)) {
            throw new Exception('Cannot get loser, the game has not been run, yet!');
        }

        return $this->loser;
    }
}
