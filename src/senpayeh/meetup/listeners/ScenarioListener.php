<?php

namespace senpayeh\meetup\listeners;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use senpayeh\meetup\Meetup;
use senpayeh\meetup\events\MeetupStartEvent;
use senpayeh\meetup\events\MeetupStopEvent;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\EventPriority;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerDeathEvent;
use pocketmine\event\player\PlayerMoveEvent;
use pocketmine\plugin\MethodEventExecutor;

class ScenarioListener implements Listener {

    /** @var Meetup */
    private $plugin;

    /**
     * ScenarioListener constructor.
     * @param Meetup $plugin
     */
    public function __construct(Meetup $plugin) {
        $this->plugin = $plugin;
        $plugin->getServer()->getPluginManager()->registerEvents($this, $this->plugin);
        $plugin->getServer()->getPluginManager()->registerEvent("senpayeh\\meetup\\events\\MeetupStartEvent", $this, EventPriority::NORMAL, new MethodEventExecutor("onStart"), $this->plugin, true);
        $plugin->getServer()->getPluginManager()->registerEvent("senpayeh\\meetup\\events\\MeetupStopEvent", $this, EventPriority::NORMAL, new MethodEventExecutor("onStop"), $this-> plugin, true);
    }

    /**
     * @param PlayerMoveEvent $event
     */
    public function onMove(PlayerMoveEvent $event) {
        Meetup::getScenarioManager()->doMove($event);
    }

    /**
     * @param BlockBreakEvent $event
     */
    public function onBreak(BlockBreakEvent $event) {
        Meetup::getScenarioManager()->doBreak($event);
    }

    /**
     * @param BlockPlaceEvent $event
     */
    public function onPlace(BlockPlaceEvent $event) {
        Meetup::getScenarioManager()->doPlace($event);
    }

    /**
     * @param PlayerDeathEvent $event
     */
    public function onDeath(PlayerDeathEvent $event) {
        Meetup::getScenarioManager()->doDeath($event);
    }

    /**
     * @param EntityDamageEvent $event
     */
    public function onDamage(EntityDamageEvent $event) {
        Meetup::getScenarioManager()->doDamage($event);
    }

    /**
     * @param MeetupStartEvent $event
     */
    public function onStart(MeetupStartEvent $event) {
        Meetup::getScenarioManager()->doStart($event);
    }

    /**
     * @param MeetupStopEvent $event
     */
    public function onStop(MeetupStopEvent $event) {
        Meetup::getScenarioManager()->doStop($event);
    }

    /**
     * @param EntityDamageByEntityEvent $event
     */
    public function onEntityDamage(EntityDamageByEntityEvent $event) {
        Meetup::getScenarioManager()->doEntityDamage($event);
    }

}