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
    public static function getTranslatedMessage(string $str, $player = null, $grace = null, $pvp = null) {
        $msg = Meetup::getInstance()->msg[$str];
        return str_replace("&k", TextFormat::OBFUSCATED, str_replace("&r",TextFormat::RESET, str_replace("&l",TextFormat::BOLD, str_replace("&o",TextFormat::ITALIC, str_replace("&f",TextFormat::WHITE, str_replace("&e",TextFormat::YELLOW, str_replace("&d",TextFormat::LIGHT_PURPLE, str_replace("&c",TextFormat::RED, str_replace("&b",TextFormat::AQUA, str_replace("&a",TextFormat::GREEN, str_replace("&0",TextFormat::BLACK, str_replace("&9",TextFormat::BLUE, str_replace("&8",TextFormat::DARK_GRAY, str_replace("&7",TextFormat::GRAY, str_replace("&6",TextFormat::GOLD, str_replace("&5",TextFormat::DARK_PURPLE, str_replace("&4",TextFormat::DARK_RED, str_replace("&3",TextFormat::DARK_AQUA, str_replace("&2",TextFormat::DARK_GREEN, str_replace("&1",TextFormat::DARK_BLUE, str_replace("%player%", $player, str_replace("%pvp%", $pvp, str_replace("%grace%", $grace, $msg)))))))))))))))))))))));
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

    public static function addKit(Player $player) : void{
        $player->getArmorInventory()->setHelmet(self::getItemData(Meetup::getInstance()->kit["kit"]["helmet"]));
        $player->getArmorInventory()->setChestplate(self::getItemData(Meetup::getInstance()->kit["kit"]["chestplate"]));
        $player->getArmorInventory()->setLeggings(self::getItemData(Meetup::getInstance()->kit["kit"]["leggings"]));
        $player->getArmorInventory()->setBoots(self::getItemData(Meetup::getInstance()->kit["kit"]["boots"]));
        foreach (Meetup::getInstance()->kit["kit"]["items"] as $item) {
            $player->getInventory()->addItem(self::getItemData($item));
        }
    }

}