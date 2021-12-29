<?php

/*
 *
 *              _                             _        ______             _
 *     /\      | |                           | |      |  ____|           (_)
 *    /  \     | | __   _ __ ___      __ _   | |      | |__       __ _    _    _ __    _   _    ____
 *   / /\ \    | |/ /  | '_ ` _ \    / _` |  | |      |  __|     / _` |  | |  | '__|  | | | |  |_  /
 *  / ____ \   |   <   | | | | | |  | (_| |  | |      | |       | (_| |  | |  | |     | |_| |   / /
 * /_/    \_\  |_|\_\  |_| |_| |_|   \__,_|  |_|      |_|        \__,_|  |_|  |_|      \__,_|  /___|
 *
 * Discord: akmal#7191
 * GitHub: https://github.com/AkmalFairuz
 *
 */
declare(strict_types=1);
namespace AkmalFairuz\McMMO;

use AkmalFairuz\McMMO\command\McmmoCommand;
use AkmalFairuz\McMMO\command\McmmoSetupCommand;
use AkmalFairuz\McMMO\entity\FloatingText;
use pocketmine\block\Solid;
use pocketmine\entity\Entity;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\entity\EntityShootBowEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerItemConsumeEvent;
use pocketmine\event\player\PlayerLoginEvent;
use pocketmine\item\Item;
use pocketmine\Player;
use pocketmine\plugin\PluginBase;
use RedCraftPE\RedSkyBlock\Skyblock;

class Main extends PluginBase implements Listener
{

    public const LUMBERJACK = 0;
    public const FARMER = 1;
    public const MINER = 3;
    //public const EXCAVATION = 2;
    public const COMBAT = 5;
    public const KILLER = 4;
    public const BUILDER = 6;
    public const CONSUMER = 7;
    public const ARCHER = 8;
    public const LAWN_MOWER = 9;


    /** @var array */
    public $database;

    /** @var Main */
    public static $instance;

    /** @var array */
    public $rewards;

    public function onEnable()
    {
        $this->saveResource("database.yml");
        $this->saveResource("rewards.yml");
        $this->getServer()->getCommandMap()->register("point", new McmmoCommand("point", $this));
        $this->getServer()->getCommandMap()->register("mcmmoadmin", new McmmoSetupCommand("mcmmoadmin", $this));
        $this->database = yaml_parse(file_get_contents($this->getDataFolder() . "database.yml"));
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
        Entity::registerEntity(FloatingText::class, true);
        self::$instance = $this;

        $this->rewards = yaml_parse_file($this->getDataFolder(). "rewards.yml");
    }

    public static function getInstance() : Main {
        return self::$instance;
    }

    public function onDisable()
    {
        file_put_contents($this->getDataFolder() . "database.yml", yaml_emit($this->database));
        sleep(3); // save database delay
    }

    public function getXp(int $type, Player $player) : int {
        return $this->database["xp"][$type][strtolower($player->getName())];
    }

    public function getLevel(int $type, Player $player) : int {
        return $this->database["level"][$type][strtolower($player->getName())];
    }

    public function addXp(int $type = 1, Player $player) {
        $this->database["xp"][$type][strtolower($player->getName())]++;
        if($this->database["xp"][$type][strtolower($player->getName())] >= ($this->getLevel($type, $player) * 100)) {
            $this->database["xp"][$type][strtolower($player->getName())] = 0;
            $this->addLevel($type, $player);
        }
        $a = ["Lumberjack", "Farmer", "", "Miner", "Killer", "Combat", "Builder", "", "Archer", "","Lawn Mower"];
        $string = $this->translate($a[$type]);
        $player->sendPopup("§l§a+1 §eXP:§b ".$string);
        //$player->sendTip("".$a[$type].": xp is ".$this->getXp($type, $player));
    }

    public function addLevel(int $type, Player $player) {
        $this->database["level"][$type][strtolower($player->getName())]++;
        $a = ["Lumberjack", "Farmer", "", "Miner", "Killer", "Combat", "Builder", "", "Archer", "","Lawn Mower"];
        $stat = $this->translate($a[$type]);
        $player->sendMessage("§l§e+1 §fđiểm §e". $stat."§f, điểm hiện tại§e:  ".$this->getLevel($type, $player));
    }

    public function reduceLevel(int $type, int $level, Player $player){
        $level = $this->database["level"][$type][strtolower($player->getName())] - $level;
        $this->database["level"][$type][strtolower($player->getName())] = $level;
    }

    public function getAll(int $type) : array {
        return $this->database["level"][$type];
    }

    public static function toInterger(string $string){
        $a = ["Lumberjack" => 0, "Farmer" => 1, "Miner" => 3, "Killer" => 4, "Combat"=> 5, "Builder" => 6, "Archer" => 8, "Lawn Mower" => 9];
        return $a[$string];
    }

    public function onLogin(PlayerLoginEvent $event) {
        $player = $event->getPlayer();
        if(!isset($this->database["xp"][0][strtolower($player->getName())])) {
            for($i = 0; $i < 10; $i++) {
                $this->database["xp"][$i][strtolower($player->getName())] = 0;
                $this->database["level"][$i][strtolower($player->getName())] = 1;
            }
        }
    }

    public function checkIS($player, $block) :bool{
        return Skyblock::getInstance()->checkIsland($player, $block);
    }

    /**
     * @priority LOWEST
     */
    public function onBreak(BlockBreakEvent $event) {
        if($event->getPlayer()->getLevel()->getName() == "newvid") $event->setCancelled(true);
        if($event->isCancelled()) {
            return;
        }
        $player = $event->getPlayer();
        $block = $event->getBlock();
        if(!$this->checkIS($player, $block)){
            $event->setCancelled(true);
            return;
        }
        if($event->getPlayer()->getLevel()->getName() == "skyblock"){
            switch($block->getId()) {
                case Item::WHEAT_BLOCK:
                case Item::BEETROOT_BLOCK:
                case Item::PUMPKIN_STEM:
                case Item::PUMPKIN:
                case Item::MELON_STEM:
                case Item::MELON_BLOCK:
                case Item::CARROT_BLOCK:
                case Item::POTATO_BLOCK:
                case Item::SUGARCANE_BLOCK:
                    $this->addXp(self::FARMER, $player);
                    return;
                case 4:
                $rand = mt_rand(1,3);
                if($rand == 1) $this->addXp(self::MINER, $player);
                break;
                case Item::DIAMOND_ORE:
                case Item::GOLD_ORE:
                case Item::REDSTONE_ORE:
                case Item::IRON_ORE:
                case Item::COAL_ORE:
                case Item::EMERALD_ORE:
                case Item::OBSIDIAN:
                    $this->addXp(self::MINER, $player);
                    return;
                case Item::LOG:
                case Item::LOG2:
                case Item::LEAVES:
                case Item::LEAVES2:
                    $this->addXp(self::LUMBERJACK, $player);
                    return;
                case Item::DIRT:
                case Item::GRASS:
                case Item::GRASS_PATH:
                case Item::FARMLAND:
                case Item::SAND:
                case Item::GRAVEL:
                  ///  $this->addXp(self::EXCAVATION, $player);
                    return;
                case Item::TALL_GRASS:
                case Item::YELLOW_FLOWER:
                case Item::RED_FLOWER:
                case Item::CHORUS_FLOWER:
                    $this->addXp(self::LAWN_MOWER, $player);

                    return;
            }            
        }

    }

    /**
     * @priority LOWEST
     */
    public function onPlace(BlockPlaceEvent $event) {
        if($event->isCancelled()) {
            return;
        }
        $player = $event->getPlayer();
        $block = $event->getBlock();
        if(!$this->checkIS($player, $block)){
            $event->setCancelled(true);
            return;
        }
        if($block instanceof Solid and $event->getPlayer()->getLevel()->getName() == "skyblock") {
                $rand = mt_rand(1,3);
                if($rand == 1) $this->addXp(self::BUILDER, $player);
            return;
        }
    }

    /**
     * @priority LOWEST
     */
    public function onDamage(EntityDamageEvent $event) {
        if($event->isCancelled()) {
            return;
        }
        if($event->getEntity() instanceof FloatingText) {
            $event->setCancelled();
            return;
        }
        if($event instanceof EntityDamageByEntityEvent) {
            $entity = $event->getEntity();
            if(!$entity instanceof Player) return;
            if($entity->getLevel()->getName() !== "world"){
                $event->setCancelled();
                return;
            }
            if(!$this->getServer()->getPluginManager()->getPlugin("iProtector")->canGetHurt($entity)){
                $event->setCancelled();
                return;
            } 
            $damager = $event->getDamager();
            if($damager instanceof Player) {
                if (($entity->getHealth() - $event->getFinalDamage()) <= 0) {
                    $this->addXp(self::KILLER, $damager);
                }
                $this->addXp(self::COMBAT, $damager);
            }
        }
    }

    /**
     * @priority LOWEST
     */
    public function onShootBow(EntityShootBowEvent $event) {
        if($event->isCancelled()) {
            return;
        }
        $entity = $event->getEntity();
        if($entity instanceof Player) {
            $this->addXp(self::ARCHER, $entity);
        }
    }

    /**
    * @param string $string
    * @return string
    */
    public function translate(string $string) :string{
        $string = str_replace("Killer", "Kẻ giết người", $string);
        $string = str_replace("Builder", "Thợ hồ", $string);
        $string = str_replace("Lawn Mower", "Thợ cắt cỏ", $string);
        $string = str_replace("Archer", "Cung thủ", $string);
        $string = str_replace("Combat", "Đánh nhau", $string);
        $string = str_replace("Miner", "Thợ mỏ", $string);
        $string = str_replace("Farmer", "Nông dân", $string);
        $string = str_replace("Lumberjack", "Tiều phu", $string);
        return $string;
    }

    public function getReward():array{
        return $this->rewards;
    }
}
