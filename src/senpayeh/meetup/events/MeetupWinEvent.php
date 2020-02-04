<?php

namespace senpayeh\meetup\events;
use pocketmine\event\plugin\PluginEvent;
use pocketmine\Player;
use senpayeh\meetup\Meetup;
use senpayeh\meetup\MeetupUtils;

class MeetupWinEvent extends PluginEvent {

    /** @var string */
    private $winner = "";

    /**
     * MeetupWinEvent constructor.
     * @param Player $winner
     */
    public function __construct(Player $winner) {
        $this->winner = $winner->getName();
        $winner->sendMessage(MeetupUtils::getTranslatedMessage("message_win_winner"));
        $this->managePlayers();
    }

    public function managePlayers() : void{
        foreach (Meetup::getInstance()->getServer()->getOnlinePlayers() as $player) {
            $player->sendMessage(MeetupUtils::getTranslatedMessage("message_win_server", $this->getWinner()));
            (new MeetupStopEvent(Meetup::getInstance()->getServer()->getOnlinePlayers(), false))->call();
        }
    }

    /**
     * @return string
     */
    public function getWinner() : string{
        return $this->winner;
    }

}