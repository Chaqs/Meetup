<?php

namespace senpayeh\meetup\events;
use senpayeh\meetup\Meetup;
use pocketmine\event\plugin\PluginEvent;
use pocketmine\Player;

class MeetupStartEvent extends PluginEvent {

    /** @var string */
    private $name = "";
    /** @var array */
    private $players = [];

    public static $handerList = null;

    /**
     * MeetupStartEvent constructor.
     * @param Meetup $plugin
     * @param string $name
     * @param array $players
     */
    public function __construct(Meetup $plugin, string $name, array $players) {
        $this->plugin = $plugin;
        $this->setName($name);
        $this->setPlayers($players);
    }

    /**
     * @return string
     */
    public function getName() : string{
        $this->name = $name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name) {
        $this->name = $name;
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
    public function setPlayers(array $players) {
        $this->players = $players;
    }

}