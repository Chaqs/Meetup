<?php

namespace senpayeh\meetup\setups;
use senpayeh\meetup\Meetup;
use pocketmine\level\Level;
use pocketmine\Player;
use pocketmine\utils\TextFormat as C;

class MeetupSetup {

    /** @var Meetup */
    private $plugin;

    /**
     * MeetupSetup constructor.
     * @param Meetup $main
     */
    public function __construct(Meetup $plugin) {
        $this->plugin = $plugin;
    }

    /**
     * @param string $data
     */
    public function getConfigData(string $data) {
        return $this->plugin->config->get[$data];
    }

    /**
     * @param string $data
     * @param string $value
     */
    public function setConfigData(string $data, string $value) {
        $this->plugin->config->set($data, (bool)$value);
        $this->plugin->config->save();
    }

    /**
     * @param Player $player
     */
    public function sendForm(Player $player) {
        $api = $this->plugin->getServer()->getPluginManager()->getPlugin("FormAPI");
        $form = $api->createCustomForm(function(Player $player, $data) {
            if ($data === null) {
                return;
            }
            if ($data[1] == true) {
                $this->setConfigData("spectators", true);
            } else {
                $this->setConfigData("spectators", false);
            }
            if ($data[2] == null) {
                $player->sendMessage(C::RED . "Set a value in the Game Length Box");
                return;
            }
            if (!is_numeric($data[2])) {
                $player->sendMessage(C::RED . "Game Length must be a number");
                return;
            }
            if ($data[2] > 60) {
                $player->sendMessage(C::RED . "Game Length mustn't be greater than 60");
                return;
            }
            $this->plugin->config->set("length", (int)$data[2]);
            $this->plugin->config->save();

            if (Meetup::getMeetupManager()->isRunning()) {
                $sender->sendMessage(C::RED . "A Meetup game is already running. Stop it to start a new game");
                return;
            }
            Meetup::getMeetupManager()->start($player);
        });
        $form->setTitle(C::DARK_GRAY . "New Meetup - SETUP");
        $form->addLabel("Note: you can skip the setup digiting /meetup forcestart");
        $form->addToggle("Spectators");
        $form->addInput(C::DARK_GRAY . "Game Length in minutes (max. 60)", "30");
        $form->sendToPlayer($player);
    }

}