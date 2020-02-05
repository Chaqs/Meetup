<?php

namespace senpayeh\meetup\scoreboard;
use pocketmine\Player;
use pocketmine\scheduler\Task;
use pocketmine\utils\TextFormat as C;
use senpayeh\meetup\gameplay\MeetupState;
use senpayeh\meetup\gameplay\tasks\MeetupTask;
use senpayeh\meetup\Meetup;
use senpayeh\meetup\MeetupUtils;

class ScoreboardUpdater extends Task
{

    /** @var Player */
    private $player;

    /**
     * ScoreboardUpdater constructor.
     * @param Player $player
     */
    public function __construct(Player $player)
    {
        $this->player = $player;
    }

    /**
     * @param int $currentTick
     */
    public function onRun(int $currentTick) : void{
        $player = $this->player;
        $uhcworld = Meetup::getInstance()->getServer()->getLevelByName(Meetup::getInstance()->getConfig()->getAll()["worlds"]["game"]);
        $lobby = Meetup::getInstance()->getServer()->getLevelByName(Meetup::getInstance()->getConfig()->getAll()["worlds"]["hub"]);
        $config = Meetup::getInstance()->msg;
        $title = $config["scoreboard_title"];
        if ($player->getLevel()->getFolderName() !== $uhcworld and $player->getLevel()->getFolderName() !== $lobby) {
            ScoreboardManager::removeScoreboard($player);
            $this->getHandler()->cancel();
        }
        switch (Meetup::getMeetupManager()->getState()) {
            case MeetupState::WAITING:
                $lines = [];
                foreach ($config["waiting_scoreboard"] as $line) {
                    $tr = MeetupUtils::getTranslatedLines($line, 0, 0, 0, 0, 0, count(Meetup::getInstance()->getServer()->getOnlinePlayers()));
                    $lines[] = $tr;
                }
                ScoreboardManager::addScoreboard($player, $player->getName(), MeetupUtils::getTranslatedLines($title));
                foreach ($lines as $line) {
                    for ($i = 0; $i < 15; $i++) {
                        ScoreboardManager::setScoreboardLine($player, $i, $line);
                    }
                }
                break;
            case MeetupState::STARTING:
                $lines = [];
                foreach ($config["starting_scoreboard"] as $line) {
                    $tr = MeetupUtils::getTranslatedLines($line, 0, 0, 0, 0, 0, count(Meetup::getInstance()->getServer()->getOnlinePlayers()));
                    $lines[$tr] = $tr;
                }
                ScoreboardManager::addScoreboard($player, $player->getName(), MeetupUtils::getTranslatedLines($title));
                foreach ($lines as $line => $data) {
                    for ($i = 0; $i < 15; $i++) {
                        ScoreboardManager::setScoreboardLine($player, $i, $data);
                    }
                }
                break;
            case MeetupState::GRACE:
                $lines = [];
                foreach ($config["grace_scoreboard"] as $line) {
                    $tr = MeetupUtils::getTranslatedLines($line, 0, 0, MeetupTask::$gametime, Meetup::getMeetupManager()->getAlivePlayers(), Meetup::getMeetupManager()->getSpectators(), count(Meetup::getInstance()->getServer()->getOnlinePlayers()));
                    $lines[$tr] = $tr;
                }
                ScoreboardManager::addScoreboard($player, $player->getName(), MeetupUtils::getTranslatedLines($title));
                foreach ($lines as $line => $data) {
                    for ($i = 0; $i < 15; $i++) {
                        ScoreboardManager::setScoreboardLine($player, $i, $data);
                    }
                }
                break;
            case MeetupState::PVP:
                $lines = [];
                foreach ($config["pvp_scoreboard"] as $line) {
                    $tr = MeetupUtils::getTranslatedLines($line, 0, 0, MeetupTask::$gametime, Meetup::getMeetupManager()->getAlivePlayers(), Meetup::getMeetupManager()->getSpectators(), count(Meetup::getInstance()->getServer()->getOnlinePlayers()));
                    $lines[$tr] = $tr;
                }
                ScoreboardManager::addScoreboard($player, $player->getName(), MeetupUtils::getTranslatedLines($title));
                foreach ($lines as $line => $data) {
                    for ($i = 0; $i < 15; $i++) {
                        ScoreboardManager::setScoreboardLine($player, $i, $data);
                    }
                }
                break;
            default:

                break;
        }
    }

}
