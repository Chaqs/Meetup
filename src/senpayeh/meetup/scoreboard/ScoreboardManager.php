<?php


namespace senpayeh\meetup\scoreboard;
use pocketmine\network\mcpe\protocol\RemoveObjectivePacket;
use pocketmine\network\mcpe\protocol\SetDisplayObjectivePacket;
use pocketmine\network\mcpe\protocol\SetScorePacket;
use pocketmine\network\mcpe\protocol\types\ScorePacketEntry;
use pocketmine\Player;

class ScoreboardManager {

    /** @var array */
    public static $scoreboard = [];

    /**
     * @param Player $player
     * @param string $objective
     * @param string $display
     */
    public static function addScoreboard(Player $player, string $objective, string $display) : void{
        if (isset(self::$scoreboard[$player->getName()])) {
            self::removeScoreboard($player);
        }
        $score = new SetDisplayObjectivePacket();
        $score->displaySlot = "sidebar";
        $score->objectiveName = $objective;
        $score->displayName = $display;
        $score->criteriaName = "dummy";
        $score->sortOrder = 0;
        $player->sendDataPacket($score);
        self::$scoreboard[$player->getName()] = $objective;
    }

    /**
     * @param Player $player
     */
    public static function removeScoreboard(Player $player) : void{
        if (isset(self::$scoreboard[$player->getName()])) {
            $objective = self::getObjectiveName($player);
            $score = new RemoveObjectivePacket();
            $score->objectiveName = $objective;
            $player->sendDataPacket($score);
            unset(self::$scoreboard[$player->getName()]);
        }
    }

    /**
     * @param Player $player
     * @param int $line
     * @param string $message
     */
    public static function setScoreboardLine(Player $player, int $line, string $message) {
        if (!isset(self::$scoreboard[$player->getName()])) {
            return;
        }
        if ($line > 15 or $line < 1) {
            return;
        }
        $objective = self::getObjectiveName($player);
        $entry = new ScorePacketEntry();
        $entry->objectiveName = $objective;
        $entry->type = $entry::TYPE_FAKE_PLAYER;
        $entry->customName = $message;
        $entry->score = $line;
        $entry->scoreboardId = $line;
        $score = new SetScorePacket();
        $score->type = $score::TYPE_CHANGE;
        $score->entries[] = $entry;
        $player->sendDataPacket($score);
    }

    /**
     * @param Player $player
     * @return |null
     */
    public static function getObjectiveName(Player $player) {
        return isset(self::$scoreboard[$player->getName()]) ? self::$scoreboard[$player->getName()] : null;
    }

}