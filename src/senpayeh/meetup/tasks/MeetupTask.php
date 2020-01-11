<?php

namespace senpayeh\meetup\tasks;
use senpayeh\meetup\Meetup;
use pocketmine\Player;
use pocketmine\scheduler\Task;
use pocketmine\utils\TextFormat as C;

class MeetupTask extends Task {

    /** @var Meetup */
    private $plugin;
    /** @var Player */
    private $player;

    const GRACE = 0;
    const PVP = 1;
    const END = 2;

    const GRACE_TIME = 15;
    private $pvp_time = 0;

    private $time = 0;

    /**
     * MeetupTask constructor.
     * @param Meetup $plugin
     */
    public function __construct(Meetup $plugin, Player $player) {
        $this->plugin = $plugin;
        $this->player = $player;
        $this->pvp_time = (int)$this->plugin->config->get("length") * 60;
        $this->setTime(0);
        $this->setState(self::GRACE);
    }

    /**
     * @param int $currentTick
     */
    public function onRun(int $currentTick) : void{
        switch ($this->getState()) {
            case self::GRACE:
                $time = $this->getTime();
                $this->setTime(++$time);
                if ($time == self::GRACE_TIME) {
                    Meetup::getInstance()->getServer()->broadcastMessage(Meetup::getTextManager()->send("pvp.enable"));
                    Meetup::getMeetupManager()->setPvP(true);
                    $this->setState(self::PVP);
                    $this->setTime(0);
                }
                break;
            case self::PVP:
                $time = $this->getTime();
                $this->setTime(++$time);
                if (count(Meetup::getMeetupManager()->getPlayers()) == 1) {
                    Meetup::getMeetupManager()->win($this->player);
                    $this->setState(self::GRACE);
                    $this->setTime(0);
                    $this->plugin->getScheduler()->cancelTask($this->getTaskId());
                } elseif (count(Meetup::getMeetupManager()->getPlayers()) < 1) {
                    Meetup::getMeetupManager()->stop();
                    $this->setState(self::GRACE);
                    $this->setTime(0);
                    $this->plugin->getScheduler()->cancelTask($this->getTaskId());
                }
                if ($time == $this->pvp_time) {
                    $this->setState(self::END);
                    $this->setTime(0);
                }
                break;
            case self::END:
                Meetup::getMeetupManager()->stop();
                $this->setState(self::GRACE);
                $this->setTime(0);
                $this->plugin->getScheduler()->cancelTask($this->getTaskId());
                break;
        }
    }

    /**
     * @return int
     */
    public function getState() : int{
        return Meetup::getMeetupManager()->getState();
    }
    /**
     * @param int $state
     */
    public function setState(int $state) {
        Meetup::getMeetupManager()->setState($state);
    }

    /**
     * @return int
     */
    public function getTime() : int{
        return $this->time;
    }

    /**
     * @param int $time
     */
    public function setTime(int $time) {
        $this->time = $time;
    }

}