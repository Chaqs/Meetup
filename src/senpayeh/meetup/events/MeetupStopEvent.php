<?php

namespace senpayeh\meetup\events;
use pocketmine\event\plugin\PluginEvent;
use pocketmine\Player;
use senpayeh\meetup\Meetup;
use senpayeh\meetup\MeetupUtils;

class MeetupStopEvent extends PluginEvent {

    /** @var array */
    private $players = [];
    /** @var bool */
    private $message = false;

    /**
     * MeetupStopEvent constructor.
     * @param array $players
     * @param bool $message
     */
    public function __construct(array $players, bool $message = false) {
        $this->message = $message;
        Meetup::getMeetupManager()->setRunning(false);
        $this->setPlayers($players);
        $this->managePlayers();
    }

    /**
     * @param array $players
     */
    public function managePlayers() : void{
        Meetup::getInstance()->getScheduler()->cancelAllTasks();
        foreach (Meetup::getInstance()->getServer()->getOnlinePlayers() as $player) {
            if (Meetup::getMeetupManager()->isPlaying($player)) {
                Meetup::getMeetupManager()->removePlayer($player);
            }
            Meetup::getMeetupManager()->restoreChanges();
            if ($this->message === true) {
                $player->sendMessage(MeetupUtils::getTranslatedMessage("message_stop"));
            }
            $player->setGamemode(Player::SURVIVAL);
            $player->setFood(20);
            $player->setHealth($player->getMaxHealth());
            $player->removeAllEffects();
            $player->getArmorInventory()->clearAll();
            $player->getInventory()->clearAll();
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

}