<?php

namespace senpayeh\meetup\gameplay\tasks;

use pocketmine\scheduler\Task;
use senpayeh\meetup\events\MeetupStopEvent;
use senpayeh\meetup\events\MeetupWinEvent;
use senpayeh\meetup\gameplay\MeetupState;
use senpayeh\meetup\Meetup;
use senpayeh\meetup\MeetupUtils;

class MeetupTask extends Task {

    /** @var Meetup */
    private $plugin;
    /** @var int */
    private $time = 0;

    /** @var int */
    private $grace;
    /** @var int */
    private $pvp;

    public function __construct(Meetup $plugin) {
        $this->plugin = $plugin;
        $this->grace = Meetup::getInstance()->getConfig()->getAll()["gameplay"]["grace"];
        $this->pvp = Meetup::getInstance()->getConfig()->getAll()["gameplay"]["end"];
    }

    /**
     * @param int $currentTick
     */
    public function onRun(int $currentTick) : void{
        switch (Meetup::getMeetupManager()->getState()) {
            case MeetupState::GRACE:
                $time = $this->getTime();
                $this->setTime(++$time);
                if ($time == $this->grace) {
                    Meetup::getInstance()->getServer()->broadcastMessage(MeetupUtils::getTranslatedMessage("message_pvp", null, null, Meetup::getInstance()->getConfig()->getAll()["gameplay"]["end"]));
                    Meetup::getMeetupManager()->setPvP();
                    $this->setTime(0);
                    Meetup::getMeetupManager()->setState(MeetupState::PVP);
                }
                break;
            case MeetupState::PVP:
                $time = $this->getTime();
                $this->setTime(++$time);
                if ($time == $this->pvp) {
                    $this->setTime(0);
                    Meetup::getMeetupManager()->setState(MeetupState::END);
                }
                break;
            case MeetupState::END:
                $this->setTime(0);
                (new MeetupStopEvent(Meetup::getMeetupManager()->getPlayers(), true))->call();
                break;
        }
        if (count(Meetup::getMeetupManager()->getPlayers()) == 1) {
            foreach (Meetup::getInstance()->getServer()->getOnlinePlayers() as $player) {
                if (Meetup::getMeetupManager()->isPlaying($player)) {
                    (new MeetupWinEvent($player))->call();
                }
            }
        }
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
    public function setTime(int $time) : void{
        $this->time = $time;
    }

}