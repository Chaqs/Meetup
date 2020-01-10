<?php

namespace senpayeh\meetup;
use pocketmine\utils\Config;
use pocketmine\Player;

class TextManager {

    /** @var Meetup */
    private $plugin;

    const START_GAME = "start.game";
    const STOP_GAME = "stop.game";
    const WIN_GAME = "win.game";
    const WIN_GAME_PLAYER = "win.game.player";
    const PVP_ENABLE = "pvp.enable";

    /**
     * TextManager constructor.
     * @param Meetup $plugin
     */
    public function __construct(Meetup $plugin) {
        $this->plugin = $plugin;
    }

    /**
     * @param string $str
     * @param Player|null $player
     * @return string
     */
    public static function send(string $str, Player $player = null): string{
        $type = Meetup::getInstance()->msg->get($str);
        if ($player !== null) {
            $name = $player-getName();
            switch ($str) {
                default:
                    return Utils::filter($type, $name);
                    break;
            }
        } else {
           switch ($str) {
             default:
                return Utils::filter($type);
                break;
            }
        }
    }

}