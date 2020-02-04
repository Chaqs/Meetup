<?php

namespace senpayeh\meetup\command;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\command\PluginIdentifiableCommand;
use pocketmine\Player;
use pocketmine\plugin\Plugin;
use pocketmine\utils\TextFormat;
use senpayeh\meetup\events\MeetupStateChangeEvent;
use senpayeh\meetup\events\MeetupStopEvent;
use senpayeh\meetup\gameplay\MeetupState;
use senpayeh\meetup\Meetup;
use senpayeh\meetup\MeetupUtils;

class MeetupCommand extends Command implements PluginIdentifiableCommand {

    const HOST_PERM = "meetup.host";

    public function __construct() {
        parent::__construct("meetup", "Manage a Meetup game");
    }

    /**
     * @param CommandSender $sender
     * @param string $label
     * @param array $args
     * @return bool
     */
    public function execute(CommandSender $sender, string $label, array $args) : bool{
        if (!$sender instanceof Player) {
            $sender->sendMessage(TextFormat::RED . "In-game only command.");
            return false;
        }
        if (!isset($args[0])) {
            $sender->sendMessage(MeetupUtils::getTranslatedMessage("error_invalid_arguments"));
            return false;
        }
        switch ($args[0]) {
            case "start":
            case "new":
                if (Meetup::getMeetupManager()->isRunning()) {
                    $sender->sendMessage(MeetupUtils::getTranslatedMessage("error_meetup_running"));
                    return false;
                }
                (new MeetupStateChangeEvent(Meetup::getInstance()->getServer()->getLevelByName(Meetup::getInstance()->getConfig()->getAll()["worlds"]["hub"])->getPlayers(), MeetupState::GRACE))->call();
                return true;
                break;
            case "stop":
            case "end":
                if (!Meetup::getMeetupManager()->isRunning()) {
                    $sender->sendMessage(MeetupUtils::getTranslatedMessage("error_meetup_not_running"));
                    return false;
                }
                (new MeetupStopEvent(Meetup::getMeetupManager()->getPlayers(), true))->call();
                return true;
                break;
        }
    }

    /**
     * @return Plugin
     */
    public function getPlugin() : Plugin{
        return Meetup::getInstance();
    }

}