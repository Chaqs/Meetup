<?php

namespace senpayeh\meetup\scenario;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use senpayeh\meetup\Meetup;
use senpayeh\meetup\events\MeetupStartEvent;
use senpayeh\meetup\events\MeetupStopEvent;
use senpayeh\meetup\scenario\scenarios\NoClean;
use pocketmine\plugin\PluginException;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\player\PlayerDeathEvent;
use pocketmine\event\player\PlayerMoveEvent;
use pocketmine\utils\Config;

class ScenarioManager {

    /** @var Meetup */
    private $plugin;
    /** @var array */
    public static $scenarios = [];
    private static $config;

    /**
     * ScenarioManager constructor.
     * @param Meetup $plugin
     */
    public function __construct(Meetup $plugin) {
        $this->plugin = $plugin;
        self::registerScenarios();
        self::setConfig(new Config($this->plugin->getDataFolder() . "scenarios.yml", Config::YAML, [
            "NoClean" => true
        ]));
        foreach (self::getConfig()->getAll() as $scenario => $bool) {
            if (self::isScenario($scenario)) {
                $bool ? self::getScenario($scenario)->setEnabled(true) : self::getScenario($scenario)->setEnabled(false);
            }
        }
    }

    /**
     * @return bool
     */
    public static function registerScenarios() : bool{
        self::registerScenario(new NoClean(), true);
        return true;
    }

    /**
     * @param Scenario $scenario
     * @param bool $force
     */
    public static function registerScenario(Scenario $scenario, bool $force = false) {
        if ($force) {
            self::$scenarios[$scenario->getName()] = $scenario;
        } else {
            if (!isset(self::$scenarios[$scenario->getName()])) {
                self::$scenarios[$scenario->getName()] = $scenario;
            } else {
                throw new PluginException("[ScenarioManager] Scenario is already registered");
            }
        }
    }

    /**
     * @return Config
     */
    public static function getConfig() : Config{
        return self::$config;
    }

    /**
     * @param Config $config
     */
    public static function setConfig(Config $config) : void{
        self::$config = $config;
    }

    /**
     * @return Scenario[]
     */
    public function getScenarios() : array{
        return self::$scenarios;
    }

    /**
     * @param string $name
     * @return Scenario
     */
    public static function getScenario(string $name) : Scenario{
        return self::$scenarios[$name];
    }

    /**
     * @param string $name
     * @return bool
     */
    public static function isScenario(string $name) : bool{
        return isset(self::$scenarios[$name]);
    }

    /**
     * @param PlayerMoveEvent $event
     */
    public function doMove(PlayerMoveEvent $event) : void{
        foreach (self::getScenarios() as $scenario) {
            if ($scenario->isEnabled()) $scenario->onMove($event);
        }
    }

    /**
     * @param BlockBreakEvent $event
     */
    public function doBreak(BlockBreakEvent $event) : void{
        foreach (self::getScenarios() as $scenario) {
            if ($scenario->isEnabled()) $scenario->onBreak($event);
        }
    }

    /**
     * @param BlockPlaceEvent $event
     */
    public function doPlace(BlockPlaceEvent $event) : void{
        foreach (self::getScenarios() as $scenario) {
            if ($scenario->isEnabled()) $scenario->onPlace($event);
        }
    }

    /**
     * @param PlayerDeathEvent $event
     */
    public function doDeath(PlayerDeathEvent $event) : void{
        foreach (self::getScenarios() as $scenario) {
            if ($scenario->isEnabled()) $scenario->onDeath($event);
        }
    }

    /**
     * @param MeetupStartEvent $event
     */
    public function doStart(MeetupStartEvent $event) : void{
        foreach (self::getScenarios() as $scenario) {
            if ($scenario->isEnabled()) $scenario->onStart($event);
        }
    }

    /**
     * @param MeetupStopEvent $event
     */
    public function doStop(MeetupStopEvent $event) : void{
        foreach (self::getScenarios() as $scenario) {
            if ($scenario->isEnabled()) $scenario->onStop($event);
        }
    }

    /**
     * @param EntityDamageEvent $event
     */
    public function doDamage(EntityDamageEvent $event) : void{
        foreach (self::getScenarios() as $scenario) {
            if ($scenario->isEnabled()) $scenario->onDamage($event);
        }
    }

    /**
     * @param EntityDamageByEntityEvent $event
     */
    public function doEntityDamage(EntityDamageByEntityEvent $event) : void{
        foreach (self::getScenarios() as $scenario) {
            if ($scenario->isEnabled()) $scenario->onEntityDamage($event);
        }
    }

}