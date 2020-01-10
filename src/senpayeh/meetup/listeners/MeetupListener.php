<?php

namespace senpayeh\meetup\listeners;
use senpayeh\meetup\level\ArenaReset;
use senpayeh\meetup\Meetup;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerMoveEvent;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\utils\TextFormat as C;

class MeetupListener implements Listener {

    /** @var Meetup */
    private $plugin;

    /**
     * MeetupListener constructor.
     * @param Meetup $plugin
     */
    public function __construct(Meetup $plugin) {
        $plugin->getServer()->getPluginManager()->registerEvents($this, $plugin);
        $this->plugin = $plugin;
    }

    /**
     * @param EntityDamageByEntityEvent $event
     */
    public function onEntityDamage(EntityDamageByEntityEvent $event) {
        $entity = $event->getEntity();
        if ($entity->getLevel() == $this->plugin->getServer()->getLevelByName($this->plugin->config->get("meetup-world"))) {
            if ($this->plugin->getMeetupManager()->getPvP() == false) {
                $event->setCancelled();
            }
        }
    }

    /**
     * @param BlockPlaceEvent $event
     */
    public function onPlace(BlockPlaceEvent $event) {
        if (Meetup::getInstance()->config->get("reset") == true) {
            if ($this->plugin->getMeetupManager()->isRunning()) {
                Meetup::getInstance()->getReset()->setBlock($event->getBlock());
            }
        }
    }

    public function onBreak(BlockBreakEvent $event) {
        if (Meetup::getInstance()->config->get("reset") == true) {
            if ($this->plugin->getMeetupManager()->isRunning()) {
                Meetup::getInstance()->getReset()->removeBlock($event->getBlock());
            }
        }
    }

}