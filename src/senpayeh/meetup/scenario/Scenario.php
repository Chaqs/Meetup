<?php

namespace senpayeh\meetup\scenario;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use senpayeh\meetup\events\MeetupStartEvent;
use senpayeh\meetup\events\MeetupStopEvent;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\player\PlayerDeathEvent;
use pocketmine\event\player\PlayerMoveEvent;

class Scenario {

    /** @var bool */
    private $enabled = false;

    /**
     * @param PlayerMoveEvent $event
     */
    public function onMove(PlayerMoveEvent $event) : void {}

    /**
     * @param BlockBreakEvent $event
     */
    public function onBreak(BlockBreakEvent $event) : void {}

    /**
     * @param BlockPlaceEvent $event
     */
    public function onPlace(BlockPlaceEvent $event) : void {}

    /**
     * @param PlayerDeathEvent $event
     */
    public function onDeath(PlayerDeathEvent $event) : void {}

    /**
     * @param MeetupStartEvent $event
     */
    public function onStart(MeetupStartEvent $event) : void {}

    /**
     * @param MeetupStopEvent $event
     */
    public function onStop(MeetupStopEvent $event) : void {}

    /**
     * @param EntityDamageEvent $event
     */
    public function onDamage(EntityDamageEvent $event) : void {}

    /**
     * @param EntityDamageByEntityEvent $event
     */
    public function onEntityDamage(EntityDamageByEntityEvent $event) : void {}

    /**
     * @return string
     */
    public function getName() : string{
        return "default";
    }

    /**
     * @return bool
     */
    public function isEnabled() : bool{
        return $this->enabled;
    }

    /**
     * @param bool $enabled
     */
    public function setEnabled(bool $enabled = true) : void{
        $this->enabled = $enabled;
    }


}