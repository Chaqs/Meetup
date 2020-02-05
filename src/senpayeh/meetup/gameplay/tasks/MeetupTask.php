<?php

namespace senpayeh\meetup\gameplay\tasks;

use pocketmine\scheduler\Task;
use senpayeh\meetup\events\MeetupStateChangeEvent;
use senpayeh\meetup\events\MeetupStopEvent;
use senpayeh\meetup\events\MeetupWinEvent;
use senpayeh\meetup\gameplay\MeetupState;
use senpayeh\meetup\Meetup;
use senpayeh\meetup\MeetupUtils;

class MeetupTask extends Task {

    /** @var Meetup */
    private $plugin;
    /** @var int */
    public static $time = 0;

    /** @var int */
    private $starting;
    /** @var int */
    private $grace;
    /** @var int */
    private $pvp;

    public function __construct(Meetup $plugin) {
        $this->plugin = $plugin;
        $this->starting = Meetup::getInstance()->getConfig()->getAll()["gameplay"]["voting"];
        $this->grace = Meetup::getInstance()->getConfig()->getAll()["gameplay"]["grace"];
        $this->pvp = Meetup::getInstance()->getConfig()->getAll()["gameplay"]["end"];
        $this->setTime(0);
    }

    /**
     * @param int $currentTick
     */
    public function onRun(int $currentTick) : void{
        switch (Meetup::getMeetupManager()->getState()) {
            case MeetupState::STARTING:
                $time = $this->getTime();
                $this->setTime(++$time);
                if ($time == 1) {
                    foreach (Meetup::getInstance()->getServer()->getLevelByName(Meetup::getInstance()->getConfig()->getAll()["worlds"]["hub"])->getPlayers() as $player) {
                        $player->sendMessage(MeetupUtils::getTranslatedMessage("message_seconds_to_start", null, null, null, $this->starting));
                    }
                }
                if ($time == $this->starting / 2) {
                    (new MeetupStateChangeEvent(Meetup::getInstance()->getServer()->getLevelByName(Meetup::getInstance()->getConfig()->getAll()["worlds"]["hub"])->getPlayers(), MeetupState::GRACE))->call();
                    foreach (Meetup::getMeetupManager()->getPlayers() as $player) {
                        $this->plugin->getServer()->getPlayer($player)->sendMessage(MeetupUtils::getTranslatedMessage("message_seconds_to_start", null, null, null, $time));
                    }
                }
                if ($time == $this->starting) {
                    foreach (Meetup::getMeetupManager()->getPlayers() as $player) {
                        $this->plugin->getServer()->getPlayer($player)->setImmobile(false);
                        $this->plugin->getServer()->getPlayer($player)->sendMessage(MeetupUtils::getTranslatedMessage("message_start", null, $this->grace = Meetup::getInstance()->getConfig()->getAll()["gameplay"]["grace"]));
                    }
                    $this->setTime(0);
                    Meetup::getMeetupManager()->setState(MeetupState::GRACE);
                }
                break;
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
                    Meetup::getMeetupManager()->setState(MeetupState::END);
                }
                break;
            case MeetupState::END:
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
        return self::$time;
    }

    /**
     * @param int $time
     */
    public function setTime(int $time) : void{
        self::$time = $time;
    }

}
