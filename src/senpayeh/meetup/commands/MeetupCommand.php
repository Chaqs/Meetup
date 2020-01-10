<?php

namespace senpayeh\meetup\commands;
use senpayeh\meetup\Meetup;
use pocketmine\Player;
use pocketmine\plugin\Plugin;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\command\PluginIdentifiableCommand;
use pocketmine\utils\TextFormat as C;

class MeetupCommand extends Command implements PluginIdentifiableCommand {

    /** @var Meetup */
    private $plugin;

    /**
     * MeetupCommand constructor.
     * @param Meetup $plugin
     */
    public function __construct(Meetup $plugin)
    {
        parent::__construct("meetup", "Meetup main command", "Usage: /meetup");
        $this->plugin = $plugin;
    }

    /**
     * @param CommandSender $sender
     * @param string $label
     * @param array $args
     * @return bool
     */
    public function execute(CommandSender $sender, string $label, array $args): bool{
        if (!$sender instanceof Player) {
            $sender->sendMessage(C::RED . "This command is only available in-game");
            return false;
        }
        if (!$sender->hasPermission("meetup.command")) {
            $sender->sendMessage(C::RED . "Insufficient permissions to run this command");
            return false;
        }
        if (!isset($args[0])) {
            $sender->sendMessage(C::RED . "Usage: /meetup [start|stop]");
            return false;
        }
        switch ($args[0]) {
            case "start":
                if ($this->plugin->config->get("meetup-world") !== null) {
                    if ($this->plugin->getServer()->getLevelByName($this->plugin->config->get("meetup-world")) == null) {
                        $sender->sendMessage(C::RED . "Meetup world hasn't been found, isn't loaded or doesn't exist");
                        return false;
                    }
                    Meetup::getMeetupManager()->start($sender);
                    return true;
                } else {
                    $sender->sendMessage(C::RED . "Meetup world hasn't been found, isn't loaded or doesn't exist");
                    return false;
                }
                return true;
                break;
            case "stop":
                if (Meetup::getMeetupManager()->isRunning() == false) {
                    $sender->sendMessage(C::RED . "There's no meetup game running");
                    return false;
                }
                Meetup::getMeetupManager()->stop();
                return true;
                break;
            default:
                $sender->sendMessage(C::RED . "Usage: /meetup [start|stop]");
                return false;
                break;
        }
    }

    /**
     * @return Meetup
     */
    public function getPlugin(): Plugin{
        return $this->plugin;
    }

}
 