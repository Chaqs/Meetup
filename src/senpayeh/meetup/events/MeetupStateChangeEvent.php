<?php

namespace senpayeh\meetup\events;
use pocketmine\event\plugin\PluginEvent;
use pocketmine\Player;
use senpayeh\meetup\gameplay\MeetupState;
use senpayeh\meetup\gameplay\tasks\MeetupTask;
use senpayeh\meetup\Meetup;
use senpayeh\meetup\MeetupUtils;

class MeetupStateChangeEvent extends PluginEvent {

   /** @var int */
    private $fromState;
    /** @var int */
    private $toState;
    /** @var array */
    private $players = [];

    /**
     * MeetupStateChangeEvent constructor.
     * @param int|null $fromState
     * @param int $toState
     * @param array $players
     */
    public function __construct(array $players, int $toState, int $fromState = null) {
        $this->setPlayers($players);
        $this->fromState = $fromState;
        $this->toState = $toState;
        if ($toState == MeetupState::GRACE) {
            $this->start();
        }
    }

    /**
     * @return array
     */
    public function getPlayers() : array{
        return $this->players;
    }

    /**
     * @param array $players
     */
    public function setPlayers(array $players) : void{
        $this->players = $players;
    }

    public function start() : void{
        Meetup::getMeetupManager()->setRunning();
        var_dump(Meetup::getMeetupManager()->isRunning());
        foreach (Meetup::getInstance()->getServer()->getLevelByName(Meetup::getInstance()->getConfig()->getAll()["worlds"]["hub"])->getPlayers() as $player) {
            Meetup::getMeetupManager()->addPlayer($player);;
            $player->sendMessage(MeetupUtils::getTranslatedMessage("message_start", null, Meetup::getInstance()->getConfig()->getAll()["gameplay"]["grace"]));
            $player->setGamemode(Player::SURVIVAL);
            $player->setFood(20);
            $player->setHealth($player->getMaxHealth());
            $player->removeAllEffects();
            $player->getArmorInventory()->clearAll();
            $player->getInventory()->clearAll();
            MeetupUtils::addKit($player);
        }
        Meetup::getMeetupManager()->setState(MeetupState::GRACE);
        var_dump(Meetup::getMeetupManager()->getState());
        Meetup::getInstance()->getScheduler()->scheduleRepeatingTask(new MeetupTask(Meetup::getInstance()), 20);
    }

    /**
     * @return int
     */
    public function getOriginState() : int{
        return $this->fromState;
    }

    /**
     * @return int
     */
    public function getTargetState() : int{
        return $this->toState;
    }

}
