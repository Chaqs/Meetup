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
        switch ($toState) {
            case MeetupState::GRACE:
                $this->start();
                break;
            case MeetupState::STARTING:
                $this->prestart();
                break;
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

    public function prestart() : void{
        Meetup::getMeetupManager()->setRunning();
       
        Meetup::getMeetupManager()->setState(MeetupState::STARTING);
       
        Meetup::getInstance()->getScheduler()->scheduleRepeatingTask(new MeetupTask(Meetup::getInstance()), 20);
    }

    public function start() : void{
        foreach (Meetup::getInstance()->getServer()->getLevelByName(Meetup::getInstance()->getConfig()->getAll()["worlds"]["hub"])->getPlayers() as $player) {

            Meetup::getMeetupManager()->addPlayer($player);
            
            $player->setGamemode(Player::SURVIVAL);

            $player->setFood($player->getMaxFood());

            $player->setHealth($player->getMaxHealth());

            $player->removeAllEffects();

            $player->getArmorInventory()->clearAll();

            $player->getInventory()->clearAll();

            MeetupUtils::addKit($player);

            $player->teleport(Meetup::getInstance()->getServer()->getLevelByName(Meetup::getInstance()->getConfig()->getAll()["worlds"]["game"])->getSafeSpawn());

            $player->setImmobile();
            
            $player->setSneaking();
        }
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
