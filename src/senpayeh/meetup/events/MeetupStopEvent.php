<?php

namespace senpayeh\meetup\events;
use senpayeh\meetup\Meetup;
use pocketmine\Player;
use pocketmine\event\plugin\PluginEvent;

class MeetupStopEvent extends PluginEvent {

    public static $handerList = null;

    /**
     * MeetupStopEvent constructor.
     * @param Meetup $plugin
     */
    public function __construct(Meetup $plugin) {
        $this->plugin = $plugin;
    }

}