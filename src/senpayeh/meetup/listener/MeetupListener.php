<?php

namespace senpayeh\meetup\listener;

use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\entity\EntityLevelChangeEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerDeathEvent;
use pocketmine\event\player\PlayerGameModeChangeEvent;
use pocketmine\event\player\PlayerMoveEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\Player;
use senpayeh\meetup\Meetup;
use senpayeh\meetup\MeetupUtils;

class MeetupListener implements Listener {

    /** @var Meetup */
    private $plugin;

    /**
     * MeetupListener constructor.
     * @param Meetup $plugin
     */
    public function __construct(Meetup $plugin) {
        $this->plugin = $plugin;
        $plugin->getServer()->getPluginManager()->registerEvents($this, $plugin);
    }

    /**
     * @param EntityDamageEvent $event
     * @priority Monitor
     */
    public function onDamage(EntityDamageEvent $event) : void{
        $entity = $event->getEntity();
        if (Meetup::getMeetupManager()->isRunning()) {
            if ($entity instanceof Player) {
                if ($entity->getGamemode() == Player::SURVIVAL) {
                    if ($event->getFinalDamage() >= $entity->getHealth()) {
                        if (Meetup::getMeetupManager()->isPlaying($entity)) {
                            Meetup::getMeetupManager()->removePlayer($entity);
                            MeetupUtils::addLightning($entity);
                            $event->setCancelled();
                            foreach ($entity->getInventory()->getContents() as $item) {
                                $entity->dropItem($item);
                            }
                            foreach ($entity->getArmorInventory()->getContents() as $armor) {
                                $entity->dropItem($armor);
                            }
                            MeetupUtils::setSpectator($entity);
                            (new PlayerDeathEvent($entity, [$entity->getInventory()->getContents(), $entity->getArmorInventory()->getContents()]))->call();
                        }
                    }
                }
            }
        }
    }

    /**
     * @param EntityDamageByEntityEvent $event
     */
    public function onEntityDamageByEntity(EntityDamageByEntityEvent $event) : void{
        $entity = $event->getEntity();
        $damager = $event->getDamager();
        if (Meetup::getMeetupManager()->getPvP() == false) {
            $event->setCancelled();
        }
        if ($damager instanceof Player and $entity instanceof Player) {
            if (Meetup::getMeetupManager()->isRunning()) {
                if (Meetup::getMeetupManager()->isPlaying($damager) and Meetup::getMeetupManager()->isPlaying($entity)) {
                    if ($event->getFinalDamage() >= $entity->getHealth()) {
                        Meetup::getMeetupManager()->removePlayer($entity);
                        MeetupUtils::addLightning($entity);
                        foreach ($entity->getInventory()->getContents() as $item) {
                            $entity->dropItem($item);
                        }
                        foreach ($entity->getArmorInventory()->getContents() as $armor) {
                            $entity->dropItem($armor);
                        }
                        MeetupUtils::setSpectator($entity);
                        (new PlayerDeathEvent($entity, [$entity->getInventory()->getContents(), $entity->getArmorInventory()->getContents()]))->call();
                        $event->setCancelled();
                    }
                }
            }
        }
    }

    /**
     * @param PlayerQuitEvent $event
     */
    public function onPlayerQuit(PlayerQuitEvent $event) : void{
        $player = $event->getPlayer();
        if ($player instanceof Player) {
            if (Meetup::getMeetupManager()->isRunning()) {
                if (Meetup::getMeetupManager()->isPlaying($player)) {
                    Meetup::getMeetupManager()->removePlayer($player);
                }
            }
        }
    }

    /**
     * @param PlayerGameModeChangeEvent $event
     */
    public function onGamemodeChange(PlayerGameModeChangeEvent $event) : void{
        $player = $event->getPlayer();
        if (Meetup::getMeetupManager()->isPlaying($player)) {
            Meetup::getMeetupManager()->removePlayer($player);
        }
    }

    /**
     * @param EntityLevelChangeEvent $event
     */
    public function onLevelChange(EntityLevelChangeEvent $event) : void{
        $entity = $event->getEntity();
        if ($entity instanceof Player) {
            if ($event->getTarget() == $this->plugin->getServer()->getLevelByName($this->plugin->getConfig()->getAll()["worlds"]["hub"])) {
                MeetupUtils::addLobbyItems($entity);
            }
        }
    }

    /**
     * @param PlayerMoveEvent $event
     */
    public function onMove(PlayerMoveEvent $event) : void{
        $player = $event->getPlayer();
        if ($player->isImmobile()) {
            $event->setCancelled();
        }
    }

}
