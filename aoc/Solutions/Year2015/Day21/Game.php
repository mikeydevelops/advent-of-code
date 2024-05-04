<?php

namespace Mike\AdventOfCode\Solutions\Year2015\Day21;

use Exception;
use Mike\AdventOfCode\Solutions\Year2015\Day21\Characters\Character;
use Mike\AdventOfCode\Solutions\Year2015\Day21\Inventory\Shop;

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
     * @var \Mike\AdventOfCode\Solutions\Year2015\Day21\Characters\Character
     */
    protected Character $player1;

    /**
     * The second player.
     *
     * @var \Mike\AdventOfCode\Solutions\Year2015\Day21\Characters\Character
     */
    protected Character $player2;

    /**
     * The player who won the game.
     *
     * @var \Mike\AdventOfCode\Solutions\Year2015\Day21\Characters\Character
     */
    protected Character $winner;

    /**
     * The player who lost the game.
     *
     * @var \Mike\AdventOfCode\Solutions\Year2015\Day21\Characters\Character
     */
    protected Character $loser;

    /**
     * The game shop.
     *
     * @var \Mike\AdventOfCode\Solutions\Year2015\Day21\Inventory\Shop
     */
    protected Shop $shop;

    /**
     * Create new instance of Game class.
     *
     * @param  \Mike\AdventOfCode\Solutions\Year2015\Day21\Characters\Character  $player1
     * @param  \Mike\AdventOfCode\Solutions\Year2015\Day21\Characters\Character  $player2
     * @return void
     */
    public function __construct()
    {
        $this->shop = new Shop;
    }

    /**
     * Run the game.
     *
     * @return \Mike\AdventOfCode\Solutions\Year2015\Day21\Characters\Character
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
     * @param  \Mike\AdventOfCode\Solutions\Year2015\Day21\Characters\Character  $character
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
     * @param  \Mike\AdventOfCode\Solutions\Year2015\Day21\Characters\Character  $character
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
     * @return \Mike\AdventOfCode\Solutions\Year2015\Day21\Inventory\Shop
     */
    public function getShop(): Shop
    {
        return $this->shop;
    }

    /**
     * Get the winner character.
     *
     * @return \Mike\AdventOfCode\Solutions\Year2015\Day21\Characters\Character
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
     * @return \Mike\AdventOfCode\Solutions\Year2015\Day21\Characters\Character
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
