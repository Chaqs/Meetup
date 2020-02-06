<?php

namespace senpayeh\meetup;

use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;
use senpayeh\meetup\command\MeetupCommand;
use senpayeh\meetup\gameplay\MeetupManager;
use senpayeh\meetup\listener\MeetupListener;

class Meetup extends PluginBase {

    /** @var Meetup */
    private static $instance;
    private static $manager;

    public function onEnable() : void{
        self::$instance = $this;
        self::$manager = new MeetupManager($this);

        $this->initConfig();

        $this->registerListeners();

        $this->getServer()->getCommandMap()->register($this->getName(), new MeetupCommand());

        $this->loadWorlds();
    }

    /**
     * @return Meetup
     */
    public static function getInstance() : Meetup{
        return self::$instance;
    }

    /**
     * @return MeetupManager
     */
    public static function getMeetupManager() : MeetupManager{
        return self::$manager;
    }

    public function registerListeners() : void{
        new MeetupListener($this);
    }

    public function initConfig() : void{
        $this->kit = (new Config($this->getDataFolder() . "kit.yml", Config::YAML))->getAll();
        $this->msg = (new Config($this->getDataFolder() . "messages.yml", Config::YAML))->getAll();
        $this->config = new Config($this->getDataFolder() . "config.yml", Config::YAML, [
            "gameplay" => [
                "voting" => 60,
                "grace" => 15,
                "end" => 600,
                "border" => 125
            ],
            "worlds" => [
                "hub" => "lobby",
                "game" => "uhc"
            ]
        ]);
    }

    public function loadWorlds() : void{
        $this->getServer()->loadLevel($this->getConfig()->getAll()["worlds"]["hub"]);
        $this->getServer()->loadLevel($this->getConfig()->getAll()["worlds"]["game"]);
    }

}
