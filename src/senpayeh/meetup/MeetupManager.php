<?php

namespace senpayeh\meetup;

use pocketmine\Player;
use senpayeh\meetup\gameplay\MeetupState;

class MeetupManager {

    /** @var Meetup */
    private $plugin;
    private $players = [], $state = MeetupState::WAITING, $running = false, $pvp = false;

    /**
     * MeetupManager constructor.
     * @param Meetup $plugin
     */
    public function __construct(Meetup $plugin) {
        $this->plugin = $plugin;
    }

    /**
     * Adds player to the game
     * @param Player $player
     */
    public function addPlayer(Player $player) : void{
        $this->players[strtolower($player->getName())] = strtolower($player->getName());
    }

    /**
     * Removes player from the game
     * @param Player $player
     */
    public function removePlayer(Player $player) : void{
        unset($this->players[strtolower($player->getName())]);
    }

    /**
     * Returns an array of the current players in game
     * @return array
     */
    public function getPlayers() : array{
        return $this->players;
    }

    /**
     * Checks whether the player is in the game or not
     * @param Player $player
     * @return bool
     */
    public function isPlaying(Player $player) : bool{
        return isset($this->getPlayers()[strtolower($player->getName())]);
    }

    /**
     * Toggles meetup to started/not started
     * @param bool $bool
     */
    public function setRunning(bool $bool = true) : void{
        $this->running = $bool;
    }

    /**
     * Checks if there's a meetup running
     * @return bool
     */
    public function isRunning() : bool{
        return $this->running;
    }

    /**
     * Changes state of the meetup
     * @param int $state
     */
    public function setState(int $state) : void{
        $this->state = $state;
    }

    /**
     * Returns state of the meetup
     * @return int
     */
    public function getState() : int{
        return $this->state;
    }

    /**
     * Enables/disables pvp
     * @param bool $bool
     */
    public function setPvP(bool $bool = true) : void{
        $this->pvp = $bool;
    }

    /**
     * Returns pvp status
     * @return bool
     */
    public function getPvP() : bool{
        return $this->pvp;
    }

    public function restoreChanges() : void{
        $this->setPvP(false);
        $this->setState(MeetupState::WAITING);
    }

}