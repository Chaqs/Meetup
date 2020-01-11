<?php

namespace senpayeh\meetup;
use senpayeh\meetup\tasks\MeetupTask;
use senpayeh\meetup\events\MeetupStartEvent;
use senpayeh\meetup\events\MeetupStopEvent;
use senpayeh\meetup\level\ArenaReset;
use pocketmine\Player;
use pocketmine\utils\TextFormat as C;

class MeetupManager {

    const DEFAULT_NAME = "MEETUP";

    private $plugin, $name = self::DEFAULT_NAME, $players = [], $lastwinner = "", $state = MeetupTask::GRACE, $gameRunning = false, $pvp = false;

    /**
     * MeetupManager constructor.
     * @param Meetup $plugin
     */
    public function __construct(Meetup $plugin) {
        $this->plugin = $plugin;
    }

    /**
     * @return bool
     */
    public function isRunning() : bool{
        return $this->gameRunning;
    }

    /**
     * @param bool $value
     */
    public function setRunning(bool $value = true) : void{
        $this->gameRunning = $value;
    }

    /**
     * @return string
     */
    public function getName() : string{
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name) {
        $this->name = $name;
    }

    /**
     * @param Player $player
     */
    public function addPlayer(Player $player) {
        $this->players[$player->getName()] = $player->getName();
    }

    /**
     * @param Player $player
     */
    public function removePlayer(Player $player) {
        unset($this->players[$player->getName()]);
    }

    /**
     * @param Player $player
     * @return bool
     */
    public function isPlaying(Player $player) : bool{
        return isset($this->players[$player->getName()]);
    }

    /**
     * @return array
     */
    public function getPlayers() : array{
        return $this->players;
    }

    /**
     * @param string $message
     */
    public function broadcastMessage(string $message) {
        foreach ($this->getPlayers() as $player) {
            $player = $this->plugin->getServer()->getPlayer($player);
            if ($player->isOnline()) {
                $player->sendMessage($message);
            }
        }
    }

    public function start(Player $sender) {
        if ($this->isRunning()) {
            $sender->sendMessage(C::RED . "A Meetup game is already running. Stop it to start a new game");
            return;
        }
        $this->setRunning();
        $players = $this->plugin->getServer()->getLevelByName($this->plugin->config->get("meetup-world"))->getPlayers();
        foreach ($players as $player) {
            if ($player instanceof Player) {
                if ($player->getGamemode() == Player::SURVIVAL) {
                    $this->addPlayer($player);
                    $player->teleport($this->plugin->getServer()->getLevelByName($this->plugin->config->get("meetup-world"))->getSafeSpawn());
                    $player->getInventory()->clearAll();
                    $player->getArmorInventory()->clearAll();
                    $player->removeAllEffects();
                    Meetup::getKitSetup()->addKit($player);
                    $this->plugin->getScheduler()->scheduleRepeatingTask(new MeetupTask($this->plugin, $player), 20);
                }
            }
        }
        $this->broadcastMessage(TextManager::send("start.game"));
        $this->plugin->getServer()->getPluginManager()->callEvent(new MeetupStartEvent($this->plugin, $this->getName(), $this->getPlayers()));
    }

    public function stop() {
        $this->setRunning(false);
        $players = $this->plugin->getServer()->getLevelByName($this->plugin->config->get("meetup-world"))->getPlayers();
        foreach ($players as $player) {
            if ($player instanceof Player) {
                $player->sendMessage(TextManager::send("stop.game"));
                if ($this->isPlaying($player)) $this->removePlayer($player);
                $player->getInventory()->clearAll();
                $player->getArmorInventory()->clearAll();
                $player->removeAllEffects();
                $player->setGamemode(Player::SURVIVAL);
                $player->teleport($this->plugin->getServer()->getDefaultLevel()->getSpawnLocation());
                $player->setGamemode(Player::SURVIVAL);
            }
        }
        $this->plugin->getScheduler()->cancelAllTasks();
        $this->setPvP(false);
        if (Meetup::getInstance()->config->get("reset") == true) {
            Meetup::getInstance()->getReset()->run();
        }
        $this->plugin->getServer()->getPluginManager()->callEvent(new MeetupStopEvent($this->plugin));
    }

    public function win(Player $winner) {
        $this->setRunning(false);
        $this->setLastWinner($winner->getName());
        $players = $this->plugin->getServer()->getLevelByName($this->plugin->config->get("meetup-world"))->getPlayers();
        foreach ($players as $player) {
            if ($player instanceof Player) {
                if ($this->isPlaying($player)) $this->removePlayer($player);
                $player->getInventory()->clearAll();
                $player->getArmorInventory()->clearAll();
                $player->setGamemode(Player::SURVIVAL);
                $player->removeAllEffects();
                $player->teleport($this->plugin->getServer()->getDefaultLevel()->getSpawnLocation());
                $player->setGamemode(Player::SURVIVAL);
                if ($player !== $winner) {
                    $player->sendMessage(TextManager::send("win.game", $winner));
                }
            }
        }
        $winner->sendMessage(TextManager::send("win.game.player"));
        $this->setPvP(false);
        if (Meetup::getInstance()->config->get("reset") == true) {
            Meetup::getInstance()->getReset()->run();
        }
        $this->plugin->getServer()->getPluginManager()->callEvent(new MeetupStopEvent($this->plugin));
        $this->plugin->getScheduler()->cancelAllTasks();
    }

    /**
     * @return bool
     */
    public function getPvP() : bool{
        return $this->pvp;
    }

    /**
     * @param bool $value
     */
    public function setPvP(bool $value) : void{
        $this->pvp = $value;
    }

    /**
     * @return string
     */
    public function getLastWinner() : string{
        return $this->lastwinner;
    }

    /**
     * @param string $lastwinner
     */
    public function setLastWinner(string $lastwinner) {
        $this->lastwinner = $lastwinner;
    }

    /**
     * @return int
     */
    public function getState() : int{
        return $this->state;
    }
    /**
     * @param int $state
     */
    public function setState(int $state) {
        $this->state = $state;
    }

}