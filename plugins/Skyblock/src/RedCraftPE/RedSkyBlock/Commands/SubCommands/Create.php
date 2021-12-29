<?php

namespace RedCraftPE\RedSkyBlock\Commands\SubCommands;

use pocketmine\utils\TextFormat;
use pocketmine\command\CommandSender;
use pocketmine\level\Position;
use pocketmine\math\Vector3;
use pocketmine\item\Item;
use pocketmine\block\Block;

use RedCraftPE\RedSkyBlock\SkyBlock;
use RedCraftPE\RedSkyBlock\Tasks\Generate;
use RedCraftPE\RedSkyBlock\Commands\Island;
use RedCraftPE\RedSkyBlock\Generators\WorldGenerator;

class Create {

  private static $instance;

  public function __construct($plugin) {

    $this->worldGenerator = new WorldGenerator($plugin);
    self::$instance = $this;
  }

  public function onCreateCommand(CommandSender $sender, array $args): bool {

    if ($sender->hasPermission("skyblock.create")) {
      $interval = SkyBlock::getInstance()->cfg->get("Interval");
      $itemsArray = SkyBlock::getInstance()->cfg->get("Starting Items", []);
      $levelName = SkyBlock::getInstance()->cfg->get("SkyBlockWorld");
      $skyblockArray = SkyBlock::getInstance()->skyblock->get("SkyBlock", []);
      $islands = SkyBlock::getInstance()->skyblock->get("Islands");
      $initialSize = SkyBlock::getInstance()->cfg->get("Island Size");
      $senderName = strtolower($sender->getName());
      $worldsArray = SkyBlock::getInstance()->cfg->get("SkyBlockWorlds", []);
      $worldCount = SkyBlock::getInstance()->skyblock->get("worlds");
      $baseName = SkyBlock::getInstance()->cfg->get("SkyBlockWorld Base Name");

      if ($baseName === false) {

        $sender->sendMessage(TextFormat::RED . "You must set a SkyBlock world in order for this plugin to function properly.");
        return true;
      }
      $level = SkyBlock::getInstance()->getServer()->getLevelByName($baseName);
      if (!$level) {

        $sender->sendMessage(TextFormat::RED . "The world currently set as the SkyBlock world does not exist.");
        return true;
      }

      if (array_key_exists($senderName, $skyblockArray)) {
        SkyBlock::getInstance()->getServer()->getCommandMap()->dispatch($sender, "is tp");
        //$sender->sendMessage(TextFormat::RED . "You already have an island.");
        return true;
      } else {

        if ($islands >= SkyBlock::getInstance()->cfg->get("World Island Limit")) {

          $worldCount++;
          $this->worldGenerator->generateWorld($baseName . $worldCount);
          $world = SkyBlock::getInstance()->getServer()->getLevelByName($baseName . $worldCount);
          array_push($worldsArray, $baseName . $worldCount);
          $islands = 0;
          SkyBlock::getInstance()->cfg->set("SkyBlockWorlds", $worldsArray);
          SkyBlock::getInstance()->cfg->save();
          SkyBlock::getInstance()->skyblock->set("Islands", $islands);
          SkyBlock::getInstance()->skyblock->set("worlds", $worldCount);
        } else {

          $world = SkyBlock::getInstance()->getServer()->getLevelByName(end($worldsArray));
          if (!$world) {

            $sender->sendMessage(TextFormat::RED . "The world currently set as the SkyBlock world does not exist.");
            return true;
          }
        }

        if (SkyBlock::getInstance()->skyblock->get("Custom")) {
          if(!isset($args[1])) $args[1] = "double_normal";
          if(isset($args[1])){
           $sender->teleport(new Position($islands * $interval + SkyBlock::getInstance()->getIslandFolder($args[1])->get("CustomX"), 15 + SkyBlock::getInstance()->getIslandFolder($args[1])->get("CustomY"), $islands * $interval + SkyBlock::getInstance()->getIslandFolder($args[1])->get("CustomZ"), $world));            
          }else{
           $sender->teleport(new Position($islands * $interval + SkyBlock::getInstance()->skyblock->get("CustomX"), 15 + SkyBlock::getInstance()->skyblock->get("CustomY"), $islands * $interval + SkyBlock::getInstance()->skyblock->get("CustomZ"), $world));            
          }
        } else {
          $sender->teleport(new Position($islands * $interval + 2, 15 + 3, $islands * $interval + 4, $world));
        }
        $sender->setImmobile(true);
        $world->setBlock(new Vector3((int) $sender->getX(), (int) $sender->getY() - 1, (int) $sender->getZ()), Block::get(Block::STONE), false);
        if(isset($args[1])){
          SkyBlock::getInstance()->getScheduler()->scheduleDelayedTask(new Generate($islands, $world, $interval, $sender, $args[1]), 30);
          var_dump($args[1]);
        }else{
          SkyBlock::getInstance()->getScheduler()->scheduleDelayedTask(new Generate($islands, $world, $interval, $sender), 30);          
        }


/*        foreach($itemsArray as $items) {

          if (count($itemsArray) > 0) {

            $itemArray = explode(" ", $items);
            if (count($itemArray) === 3) {

              $id = intval($itemArray[0]);
              $damage = intval($itemArray[1]);
              $count = intval($itemArray[2]);
             // $sender->getInventory()->addItem(Item::get($id, $damage, $count));
            }
          }
        }*/

        SkyBlock::getInstance()->skyblock->setNested("Islands", $islands + 1);
        $skyblockArray[$senderName] = Array(
          "Name" => $sender->getName() . "'s Island",
          "Members" => [$sender->getName()],
          "Banned" => [],
          "Locked" => false,
          "Value" => 0,
          "World" => $world->getFolderName(),
          "Reset Cooldown" => 0,
          "Challenges" => [],
          "Spawn" => Array(
            "X" => $sender->getX(),
            "Y" => $sender->getY(),
            "Z" => $sender->getZ()
          ),
          "Area" => Array(
            "start" => Array(
              "X" => ($islands * $interval + SkyBlock::getInstance()->getIslandFolder($args[1])->get("CustomX")) - ($initialSize / 2),
              "Y" => 0,
              "Z" => ($islands * $interval + SkyBlock::getInstance()->skyblock->get("CustomZ")) - ($initialSize / 2)
            ),
            "end" => Array(
              "X" => ($islands * $interval + SkyBlock::getInstance()->getIslandFolder($args[1])->get("CustomX")) + ($initialSize / 2),
              "Y" => 256,
              "Z" => ($islands * $interval + SkyBlock::getInstance()->getIslandFolder($args[1])->get("CustomZ")) + ($initialSize / 2)
            )
          ),
          "Settings" => Array(
            "Build" => "on",
            "Break" => "on",
            "Pickup" => "on",
            "Anvil" => "on",
            "Chest" => "on",
            "CraftingTable" => "off",
            "Fly" => "off",
            "Hopper" => "on",
            "Brewing" => "off",
            "Beacon" => "on",
            "Buckets" => "on",
            "PVP" => "off",
            "FlintAndSteel" => "on",
            "Furnace" => "on",
            "EnderChest" => "off"
          )
        );
        SkyBlock::getInstance()->skyblock->set("SkyBlock", $skyblockArray);
        SkyBlock::getInstance()->skyblock->save();
        return true;
      }
    } else {
      $sender->sendMessage(TextFormat::RED . "You do not have the proper permissions to run this command.");
      return true;
    }
  }
}
