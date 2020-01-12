<?php

namespace senpayeh\meetup\tasks;
use senpayeh\meetup\Meetup;
use pocketmine\scheduler\Task;
use pocketmine\Player;

class WinTask extends Task {

    /** @var Meetup */
    private $plugin;
    /** @var Player */
    private $player;

    /**
     * WinTask constructor.
     * @param Meetup $plugin
     * @param Player $player
     */
    public function __construct(Meetup $plugin, Player $player) {
        $this->plugin = $plugin;
        $this->player = $player;
    }

    /**
     * @param int $currentTick
     */
    public function onRun(int $currentTick) : void{
        $players = count(Meetup::getMeetupManager()->getPlayers());
        if ($players == 1) {
            if (Meetup::getMeetupManager()->isPlaying($this->player)) {
                Meetup::getMeetupManager()->win($this->player);
            }
        }
        if (Meetup::getMeetupManager()->isRunning() == false) {
            $this->plugin->getScheduler()->cancelTask($this->getTaskId());
        }
    }

}