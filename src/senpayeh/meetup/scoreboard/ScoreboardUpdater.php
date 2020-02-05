<?php

namespace senpayeh\meetup\scoreboard;
use pocketmine\Player;
use pocketmine\scheduler\Task;
use pocketmine\utils\TextFormat as C;
use senpayeh\meetup\gameplay\MeetupState;
use senpayeh\meetup\Meetup;
use senpayeh\meetup\MeetupUtils;

class ScoreboardUpdater extends Task {

    /** @var Player */
    private $player;

    /**
     * ScoreboardUpdater constructor.
     * @param Player $player
     */
    public function __construct(Player $player) {
        $this->player = $player;
    }

    /**
     * @param int $currentTick
     */
    public function onRun(int $currentTick) : void{
        $player = $this->player;
        $uhcworld = Meetup::getInstance()->getServer()->getLevelByName(Meetup::getInstance()->getConfig()->getAll()["worlds"]["game"]);
        $lobby = Meetup::getInstance()->getServer()->getLevelByName(Meetup::getInstance()->getConfig()->getAll()["worlds"]["hub"]);
        $config = Meetup::getInstance()->msg->getAll();
        $title = $config["scoreboard_title"];
        if ($player->getLevel() == $uhcworld || $player->getLevel() == $lobby) {
            switch (Meetup::getMeetupManager()->getState()) {
                case MeetupState::WAITING:
                    $lines = [];
                    foreach ($config["waiting_scoreboard"] as $line) {
                        $tr = MeetupUtils::getTranslatedLines($line, 0, 0, 0, 0, 0, count(Meetup::getInstance()->getServer()->getOnlinePlayers());
                        $lines[] = $tr;
                    }
                    ScoreboardManager::addScoreboard($player, $player->getName(), $title);
                    foreach ($lines as $line) {
                        for ($i = 0; $i < 15; $i++) {
                            ScoreboardManager::setScoreboardLine($player, $i, $line);
                        }
                    }
                    break;
                case MeetupState::STARTING:
                    $lines = [];
                    foreach ($config["starting_scoreboard"] as $line) {
                        $tr = MeetupUtils::getTranslatedLines($line, 0, 0, , 0, 0, count(Meetup::getInstance()->getServer()->getOnlinePlayers());
                        $lines[] = $tr;
                    }
                    ScoreboardManager::addScoreboard($player, $player->getName(), $title);
                    foreach ($lines as $line => $data) {
                        for ($i = 0; $i < 15; $i++) {
                            ScoreboardManager::setScoreboardLine($player, $i, $data);
                        }
                    }
                    break;
                default:
                    $lines = [
                        " " . C::RED . "Alive: " . C::WHITE . count(Meetup::getInstance()->getServer()->getOnlinePlayers()),
                        " " . C::RED . "Spectators: " . C::WHITE . "{spectators}",
                        " " . C::RED . "Kills: " . C::WHITE . "{kills}",
                    ];
                    ScoreboardManager::addScoreboard($player, $player->getName(), "   " . C::RED . "Mineage Events ");
                    foreach ($lines as $line) {
                        for ($i = 0; $i < 15; $i++) {
                            ScoreboardManager::setScoreboardLine($player, $i, $line);
                        }
                    }
                    break;
            }
        } else {
            ScoreboardManager::removeScoreboard($player);
            $this->getHandler()->cancel();
        }
    }

}