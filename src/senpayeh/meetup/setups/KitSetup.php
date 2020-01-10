<?php

namespace senpayeh\meetup\setups;
use senpayeh\meetup\Meetup;
use pocketmine\Player;
use pocketmine\item\{Item, ItemBlock};
use pocketmine\block\Block;
use pocketmine\item\enchantment\{Enchantment, EnchantmentInstance};

class KitSetup {

    /** @var Meetup */
    private $plugin;

    /**
     * KitSetup constructor.
     * @param Meetup $plugin
     */
    public function __construct(Meetup $plugin) {
        $this->plugin = $plugin;
    }

    /**
     * @param string $raw
     * @return array
     */
    public function getItemsData(string $raw) : array{
        $e = explode(":", $raw);
        return $e;
    }

    /**
     * @return Item
     */
    public function getHelmet() : Item{
        $helmet = $this->plugin->kit->get("helmet");
        $he = $this->getItemsData($helmet);
        $helmet = Item::get($he[0], $he[1], $he[2]);
        if ((int)$he[3] !== 0 or (int)$he[4] !== 0) $helmet->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment($he[3]), $he[4]));
        return $helmet;
    }

    /**
     * @return Item
     */
    public function getChestplate() : Item{
        $chestplate = $this->plugin->kit->get("chestplate");
        $chest = $this->getItemsData($chestplate);
        $chestplate = Item::get($chest[0], $chest[1], $chest[2]);
        if ((int)$chest[3] !== 0 or (int)$chest[4] !== 0) $chestplate->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment($chest[3]), $chest[4]));
        return $chestplate;
    }

    /**
     * @return Item
     */
    public function getLeggings() : Item{
        $legg = $this->plugin->kit->get("leggings");
        $le = $this->getItemsData($legg);
        $legg = Item::get($le[0], $le[1], $le[2]);
        if ((int)$le[3] !== 0 or (int)$le[4] !== 0) $legg->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment($le[3]), $le[4]));
        return $legg;
    }

    /**
     * @return Item
     */
    public function getBoots() : Item{
        $boots = $this->plugin->kit->get("boots");
        $bo = $this->getItemsData($boots);
        $boots = Item::get($bo[0], $bo[1], $bo[2]);
        if ((int)$bo[3] !== 0 or (int)$bo[4] !== 0) $boots->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment($bo[3]), $bo[4]));
        return $boots;
    }

    /**
     * @return array
     */
    public function getItems() : array{
        $items = $this->plugin->kit->getAll()["items"];
        $others = [];
        foreach ($items as $item) {
            $it = $this->getItemsData($item);
            $item = Item::get($it[0], $it[1], $it[2]);
            if ((int)$it[3] !== 0 or (int)$it[4] !== 0) $item->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment($it[3]), $it[4]));
            $others[] = $item;
        }
        return $others;
    }

    /**
     * @return array
     */
    public function getBlocks() : array{
        $blocks = $this->plugin->kit->getAll()["blocks"];
        $others = [];
        foreach ($blocks as $block) {
            $bl = $this->getItemsData($block);
            $block = Item::get($bl[0], $bl[1], $bl[2]);
            if ((int)$bl[3] !== 0 or (int)$bl[4] !== 0) $block->addEnchantment(EnchantmentInstance::getEnchantment((int)$bl[3]))->setLevel((int)$bl[4]);
            $others[] = $block;
        }
        return $others;
    }

    /**
     * @param Player $player
     */
    public function addKit(Player $player) {
        $player->getArmorInventory()->setHelmet($this->getHelmet());
        $player->getArmorInventory()->setChestplate($this->getChestplate());
        $player->getArmorInventory()->setLeggings($this->getLeggings());
        $player->getArmorInventory()->setBoots($this->getBoots());
        foreach ($this->getItems() as $item) {
            $player->getInventory()->addItem($item);
        }
        foreach ($this->getBlocks() as $block) {
            $player->getInventory()->addItem($block);
        }
    }

}