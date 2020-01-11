<?php

namespace senpayeh\meetup\listeners;
use pocketmine\event\player\PlayerDeathEvent;
use senpayeh\meetup\level\ArenaReset;
use senpayeh\meetup\Meetup;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerMoveEvent;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\utils\TextFormat as C;
use pocketmine\Player;
use pocketmine\network\mcpe\protocol\AddActorPacket;
use pocketmine\entity\Entity;

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
        $players = Meetup::getInstance()->getMeetupManager()->getPlayers();
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

    public function onDeath(PlayerDeathEvent $event) {
        $player = $event->getPlayer();
        $light = new AddActorPacket();
        $light->type = 93;
        $light->entityRuntimeId = Entity::$entityCount++;
        $light->metadata = array();
        $light->motion = $player->getMotion();
        $light->yaw = $player->getYaw();
        $light->pitch = $player->getPitch();
        $light->position = $player->getPosition();
        foreach ($player->getLevel()->getPlayers() as $serv) {
            $serv->dataPacket($light);
        }
        if (Meetup::getInstance()->getMeetupManager()->isPlaying($player)) {
            Meetup::getInstance()->getMeetupManager()->removePlayer($player);
        }
            if (count(Meetup::getInstance()->getMeetupManager()->getPlayers()) == 1) {
                if ($player->getLastDamageCause() instanceof EntityDamageByEntityEvent) {
                    $killer = $player->getLastDamageCause()->getDamager();
                    if ($killer instanceof Player) {
                        Meetup::getInstance()->getMeetupManager()->win($killer);
                        return;
                    } elseif (count(Meetup::getInstance()->getMeetupManager()->getPlayers()) == 0) {
                        Meetup::getInstance()->getMeetupManager()->stop();
                        return;
                    }
                } else {
                    foreach (Meetup::getInstance()->getMeetupManager()->getPlayers() as $player) {
                        Meetup::getInstance()->getMeetupManager()->win($player);
                        return;
                    }
                }
            }
    }

}