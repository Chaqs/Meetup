<?php

namespace senpayeh\meetup\scenario\scenarios;
use senpayeh\meetup\Meetup;
use senpayeh\meetup\scenario\Scenario;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\player\PlayerDeathEvent;
use pocketmine\entity\Effect;
use pocketmine\entity\EffectInstance;

class NoClean extends Scenario {

    /** @var array */
    private $noclean = [];

    /**
     * @param PlayerDeathEvent $event
     */
    public function onDeath(PlayerDeathEvent $event) : void{
        $player = $event->getPlayer();
        if ($player->getLastDamageCause() instanceof EntityDamageByEntityEvent) {
            $killer = $player->getLastDamageCause()->getDamager();
            $killer->addEffect(new EffectInstance(Effect::getEffect(Effect::REGENERATION), 10 * 20, 1));
        }
    }

    /**
     * @return string
     */
    public function getName(): string{
        return "NoClean";
    }

}