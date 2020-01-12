<?php

namespace senpayeh\meetup;
use senpayeh\meetup\listeners\MeetupListener;
use senpayeh\meetup\listeners\ScenarioListener;
use senpayeh\meetup\setups\KitSetup;
use senpayeh\meetup\scenario\ScenarioManager;
use senpayeh\meetup\commands\MeetupCommand;
use senpayeh\meetup\tasks\WinTask;
use senpayeh\meetup\level\ArenaReset;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;

class Meetup extends PluginBase {

    public static $instance;
    public static $meetupmanager, $meetupsetup, $kitsetup, $scenariomanager, $reset, $textmanager;

    public function onEnable() : void{
        $this->registerListeners();
        $this->registerCommand();
        $this->setInstance($this);

        $this->saveResources();
        $this->config = new Config($this->getDataFolder() . "config.yml", Config::YAML);
        $this->kit = new Config($this->getDataFolder() . "kit.yml", Config::YAML);
        $this->msg = new Config($this->getDataFolder() . "messages.yml", Config::YAML);
        $this->registerManagers();
    }

    public function registerManagers() : void{
        self::setMeetupManager(new MeetupManager($this));
        self::setKitSetup(new KitSetup($this));
        self::setScenarioManager(new ScenarioManager($this));
        self::setReset(new ArenaReset());
        self::setTextManager(new TextManager($this));
    }

    public function registerListeners() : void{
        new ScenarioListener($this);
        new MeetupListener($this);
    }

    /**
     * @return Meetup
     */
    public static function getInstance() : Meetup{
        return self::$instance;
    }


    /**
     * @param Meetup $instance
     */
    public static function setInstance(Meetup $instance) : void{
        self::$instance = $instance;
    }

    /**
     * @return MeetupManager
     */
    public static function getMeetupManager() : MeetupManager{
        return self::$meetupmanager;
    }

    /**
     * @param MeetupManager $meetupmanager
     */
    public static function setMeetupManager(MeetupManager $meetupmanager) : void{
        self::$meetupmanager = $meetupmanager;
    }

    /**
     * @return KitSetup
     */
    public static function getKitSetup() : KitSetup{
        return self::$kitsetup;
    }

    /**
     * @return KitSetup $kitsetup
     */
    public static function setKitSetup(KitSetup $kitsetup) : void{
        self::$kitsetup = $kitsetup;
    }

    /**
     * @return ScenarioManager
     */
    public static function getScenarioManager() : ScenarioManager{
        return self::$scenariomanager;
    }

    /**
     * ScenarioManager $scenariomanager
     */
    public static function setScenarioManager(ScenarioManager $scenariomanager) : void{
        self::$scenariomanager = $scenariomanager;
    }

    /**
     * @return ArenaReset
     */
    public static function getReset() : ArenaReset{
        return self::$reset;
    }

    /**
     * @param ArenaReset $reset
     */
    public static function setReset(ArenaReset $reset) : void{
        self::$reset = $reset;
    }

    /**
     * @return TextManager
     */
    public static function getTextManager() : TextManager{
        return self::$textmanager;
    }

    /**
     * @param TextManager $textmanager
     */
    public static function setTextManager(TextManager $textmanager) : void{
        self::$textmanager = $textmanager;
    }

    private function saveResources() : void{
        $this->saveResource("config.yml");
        $this->saveResource("scenarios.yml");
        $this->saveResource("kit.yml");
        $this->saveResource("messages.yml");
    }

    private function registerCommand() : void{
        $this->getServer()->getCommandMap()->registerAll($this->getName(), [
            new MeetupCommand($this)
        ]);
    }

}