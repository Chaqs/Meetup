<?php

namespace senpayeh\meetup;

use pocketmine\entity\Entity;
use pocketmine\item\enchantment\Enchantment;
use pocketmine\item\enchantment\EnchantmentInstance;
use pocketmine\item\Item;
use pocketmine\math\Vector3;
use pocketmine\network\mcpe\protocol\AddActorPacket;
use pocketmine\Player;
use pocketmine\utils\TextFormat;

class MeetupUtils {

    /**
     * @param Vector3 $vector3
     */
    public static function addLightning(Player $player) : void{
        $light = new AddActorPacket();
        $light->type = 93;
        $light->entityRuntimeId = Entity::$entityCount++;
        $light->metadata = array();
        $light->motion = null;
        $light->yaw = $player->getYaw();
        $light->pitch = $player->getPitch();
        $light->position = new Vector3($player->getX(), $player->getY(), $player->getZ());
        Meetup::getInstance()->getServer()->broadcastPacket($player->getLevel()->getPlayers(), $light);
    }

    /**
     * @param Player $player
     */
    public static function setSpectator(Player $player) : void{
        $player->setGamemode(Player::SPECTATOR);
    }

    /**
     * @param string $str
     * @return string|string[]
     */
    public static function getTranslatedMessage(string $str, $player = null, $grace = null, $pvp = null, $start = null) {
        $msg = Meetup::getInstance()->msg[$str];
        return str_replace("&k", TextFormat::OBFUSCATED, str_replace("&r",TextFormat::RESET, str_replace("&l",TextFormat::BOLD, str_replace("&o",TextFormat::ITALIC, str_replace("&f",TextFormat::WHITE, str_replace("&e",TextFormat::YELLOW, str_replace("&d",TextFormat::LIGHT_PURPLE, str_replace("&c",TextFormat::RED, str_replace("&b",TextFormat::AQUA, str_replace("&a",TextFormat::GREEN, str_replace("&0",TextFormat::BLACK, str_replace("&9",TextFormat::BLUE, str_replace("&8",TextFormat::DARK_GRAY, str_replace("&7",TextFormat::GRAY, str_replace("&6",TextFormat::GOLD, str_replace("&5",TextFormat::DARK_PURPLE, str_replace("&4",TextFormat::DARK_RED, str_replace("&3",TextFormat::DARK_AQUA, str_replace("&2",TextFormat::DARK_GREEN, str_replace("&1",TextFormat::DARK_BLUE, str_replace("%player%", $player, str_replace("%pvp%", $pvp, str_replace("%grace%", $grace, str_replace("%start%", $start, $msg))))))))))))))))))))))));
    }

    /**
     * @param string $msg
     * @return string|string[]
     */
    public static function getTranslatedLines(string $msg, $kills = null, $border = null, $time = null, $alive = null, $spectators = null, $online = null) {
        return str_replace("&k", TextFormat::OBFUSCATED, str_replace("&r",TextFormat::RESET, str_replace("&l",TextFormat::BOLD, str_replace("&o",TextFormat::ITALIC, str_replace("&f",TextFormat::WHITE, str_replace("&e",TextFormat::YELLOW, str_replace("&d",TextFormat::LIGHT_PURPLE, str_replace("&c",TextFormat::RED, str_replace("&b",TextFormat::AQUA, str_replace("&a",TextFormat::GREEN, str_replace("&0",TextFormat::BLACK, str_replace("&9",TextFormat::BLUE, str_replace("&8",TextFormat::DARK_GRAY, str_replace("&7",TextFormat::GRAY, str_replace("&6",TextFormat::GOLD, str_replace("&5",TextFormat::DARK_PURPLE, str_replace("&4",TextFormat::DARK_RED, str_replace("&3",TextFormat::DARK_AQUA, str_replace("&2",TextFormat::DARK_GREEN, str_replace("&1",TextFormat::DARK_BLUE, str_replace("%kills%", $kills, str_replace("%border%", $border, str_replace("%spectators%", $spectators, str_replace("%alive%", $alive, str_replace("%time%", $time, str_replace("%online%", $online, $msg))))))))))))))))))))))))));
    }

    /**
     * @param string $string
     * @return Item|null
     */
    public static function getItemData(string $string) : ?Item{
        $e = explode(":", $string);
        if (!isset($e[0]) or !isset($e[1]) or !isset($e[2])) return null;
        $item = Item::get($e[0], $e[1], $e[2]);
        if (isset($e[3]) and isset($e[4])) {
            $ench = new EnchantmentInstance(Enchantment::getEnchantmentByName($e[3]), (int)$e[4]);
            $item->addEnchantment($ench);
        }
        return $item;
    }

    /**
     * @param Player $player
     */
    public static function addKit(Player $player) : void{
        $kit = array_rand(Meetup::getInstance()->kit["kits"]);

        $player->getArmorInventory()->setHelmet(self::getItemData(Meetup::getInstance()->kit["kits"][$kit]["helmet"]));
        $player->getArmorInventory()->setChestplate(self::getItemData(Meetup::getInstance()->kit["kits"][$kit]["chestplate"]));
        $player->getArmorInventory()->setLeggings(self::getItemData(Meetup::getInstance()->kit["kits"][$kit]["leggings"]));
        $player->getArmorInventory()->setBoots(self::getItemData(Meetup::getInstance()->kit["kits"][$kit]["boots"]));
        foreach (Meetup::getInstance()->kit["kits"][$kit]["items"] as $item) {
            $player->getInventory()->addItem(self::getItemData($item));
        }
    }

    /**
     * @param Player $player
     */
    public static function addLobbyItems(Player $player) : void{
        $player->getInventory()->setContents([
            4 => Item::get(Item::DIAMOND_SWORD, 0, 1)->setCustomName(TextFormat::RESET . TextFormat::GOLD . "Join Meetup"),
        ]);
    }

}
