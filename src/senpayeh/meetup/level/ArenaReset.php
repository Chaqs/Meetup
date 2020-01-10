<?php

namespace senpayeh\meetup\level;
use senpayeh\meetup\Meetup;
use pocketmine\block\Block;
use pocketmine\math\Vector3;

class ArenaReset {

    /** @var array */
    private $blocks = [];

    public function run() {
        foreach ($this->blocks as $block) {
            $level = Meetup::getInstance()->getServer()->getLevelByName(Meetup::getInstance()->config->get("meetup-world"));
            $level->setBlock(new Vector3($block->getX(), $block->getY(), $block->getZ()), Block::get(Block::AIR));
        }
    }

    /**
     * @return array
     */
    public function getBlocks() : array{
        return $this->blocks;
    }

    /**
     * @param Block $block
     */
    public function setBlock(Block $block) : void{
        $this->blocks[] = $block;
    }

    /**
     * @param Block $block
     */
    public function removeBlock(Block $block) : void{
        if (in_array($block, $this->blocks)) {
            unset($this->blocks[$block]);
        }
    }


}