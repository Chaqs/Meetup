<?php

namespace senpayeh\meetup\listeners;
use pocketmine\event\player\PlayerDeathEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\event\player\PlayerRespawnEvent;
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

    /**
     * @param BlockBreakEvent $event
     */
    public function onBreak(BlockBreakEvent $event) {
        if (Meetup::getInstance()->config->get("reset") == true) {
            if ($this->plugin->getMeetupManager()->isRunning()) {
                Meetup::getInstance()->getReset()->removeBlock($event->getBlock());
            }
        }
    }

    /**
     * @param PlayerDeathEvent $event
     */
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
        if (Meetup::getMeetupManager()->isRunning()) {
            foreach ($player->getLevel()->getPlayers() as $serv) {
                $serv->dataPacket($light);
            }
        }
        if (Meetup::getInstance()->getMeetupManager()->isPlaying($player)) {
            Meetup::getInstance()->getMeetupManager()->removePlayer($player);
        }
    }

    /**
     * @param PlayerRespawnEvent $event
     */
    public function onRespawn(PlayerRespawnEvent $event) {
        $player = $event->getPlayer();
        if ($this->plugin->config->get("spectators") == true) {
            if (Meetup::getMeetupManager()->isPlaying($player)) $player->setGamemode(Player::SPECTATOR);
            if (!empty(Meetup::getMeetupManager()->getPlayers())) {
                $rm = array_rand(Meetup::getMeetupManager()->getPlayers());
                if ($rm instanceof Player) {
                    $position = $rm->getPosition();
                    $player->teleport($position);
                }
            }
        }
    }

    /**
     * @param PlayerQuitEvent $event
     */
    public function onQuit(PlayerQuitEvent $event) {
        $player = $event->getPlayer();
        if (Meetup::getMeetupManager()->isPlaying($player)) {
            Meetup::getMeetupManager()->removePlayer($player);
        }
    }

}