<?php

namespace RedCraftPE\RedSkyBlock;

use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;
use pocketmine\utils\TextFormat;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\block\BlockFactory;
use pocketmine\block\Block;
use pocketmine\Player;

use RedCraftPE\RedSkyBlock\Commands\Island;
use RedCraftPE\RedSkyBlock\Tasks\Generate;
use RedCraftPE\RedSkyBlock\Blocks\Lava;
use NpcDialog\NpcDialog;

use pocketmine\item\Item;
use pocketmine\item\enchantment\{Enchantment, EnchantmentInstance};

class SkyBlock extends PluginBase {

  private $eventListener;

  private static $instance;

  private $island;

  private $expend;

  public function onEnable(): void {
	NpcDialog::register($this);
    foreach($this->cfg->get("SkyBlockWorlds", []) as $world) {

      if (!$this->getServer()->isLevelLoaded($world)) {

        if ($this->getServer()->loadLevel($world)) {

          $this->getServer()->loadLevel($world);
        }
      }
    }

    if ($this->cfg->get("SkyBlockWorld Base Name") === false) {

      $this->getLogger()->info(TextFormat::RED . "In order for this plugin to function properly, you must set a SkyBlock world in your server.");
      $this->level = null;
    } else {

      $this->level = $this->getServer()->getLevelByName($this->cfg->get("SkyBlockWorld Base Name"));
      if (!$this->level) {

        $this->getLogger()->info(TextFormat::RED . "The level currently set as the SkyBlock base world does not exist.");
        $this->level = null;
      } else {

        $this->getLogger()->info(TextFormat::GREEN . "SkyBlock is running on the base world {$this->level->getFolderName()}");
      }
    }

    $this->eventListener = new EventListener($this, $this->level);
    $this->island = new Island($this);
    self::$instance = $this;
   // BlockFactory::registerBlock(new Lava(), true);
  }
  
  public function onLoad(): void {

    if (!is_dir($this->getDataFolder())) {

      @mkdir($this->getDataFolder());
    }
    if (!file_exists($this->getDataFolder() . "skyblock.yml")) {

      $this->saveResource("skyblock.yml");
      $this->skyblock = new Config($this->getDataFolder() . "skyblock.yml", Config::YAML);
    } else {

      $this->skyblock = new Config($this->getDataFolder() . "skyblock.yml", Config::YAML);
    }
    if (!file_exists($this->getDataFolder() . "config.yml")) {

      $this->saveResource("config.yml");
      $this->cfg = new Config($this->getDataFolder() . "config.yml", Config::YAML);
    } else {

      $this->cfg = new Config($this->getDataFolder() . "config.yml", Config::YAML);
    }

    if (!$this->cfg->exists("PVP")) {

      $this->cfg->set("PVP", "off");
      $this->cfg->save();
    }

    $this->cfg->reload();
    $this->skyblock->reload();
  }
  public function onCommand(CommandSender $sender, Command $command, string $label, array $args): bool {

    switch(strtolower($command->getName())) {

      case "island":

        return $this->island->onIslandCommand($sender, $command, $label, $args);
      break;
    }
    return false;
  }

  //API FUNCTIONS:
  public static function getInstance(): self {

    return self::$instance;
  }
  public function calcRank(string $name): string {

    $skyblockArray = $this->skyblock->get("SkyBlock", []);
    $users = [];

    if (!array_key_exists($name, $skyblockArray)) {

      return "N/A";
    }

    foreach(array_keys($skyblockArray) as $user) {

      $userValue = $skyblockArray[$user]["Value"];
      $users[$user] = $userValue;
    }

    arsort($users);
    $rank = array_search($name, array_keys($users)) + 1;

    return strval($rank);
  }
  public function getIslandName(Player $player): string {

    $skyblockArray = $this->skyblock->get("SkyBlock", []);
    $name = strtolower($player->getName());

    if (!array_key_exists($name, $skyblockArray)) {

      return "N/A";
    }

    return $skyblockArray[$name]["Name"];
  }
  public function getMembers(Player $player): string {

    $skyblockArray = $this->skyblock->get("SkyBlock", []);
    $name = strtolower($player->getName());

    if (!array_key_exists($name, $skyblockArray)) {

      return "N/A";
    }

    return implode(", ", $skyblockArray[$name]["Members"]);
  }
  public function getValue(Player $player): string {

    $skyblockArray = $this->skyblock->get("SkyBlock", []);
    $name = strtolower($player->getName());

    if (!array_key_exists($name, $skyblockArray)) {

      return "N/A";
    }

    return strval($skyblockArray[$name]["Value"]);
  }
  public function getBanned(Player $player): string {

    $skyblockArray = $this->skyblock->get("SkyBlock", []);
    $name = strtolower($player->getName());

    if (!array_key_exists($name, $skyblockArray)) {

      return "N/A";
    }

    return implode(", ", $skyblockArray[$name]["Banned"]);
  }
  public function getLockedStatus(Player $player): string {

    $skyblockArray = $this->skyblock->get("SkyBlock", []);
    $name = strtolower($player->getName());

    if (!array_key_exists($name, $skyblockArray)) {

      return "N/A";
    }

    if ($skyblockArray[$name]["Locked"]) {

      return "Yes";
    } else {

      return "No";
    }
  }
  public function getSize(Player $player): string {

    $skyblockArray = $this->skyblock->get("SkyBlock", []);
    $name = strtolower($player->getName());

    if (!array_key_exists($name, $skyblockArray)) {

      return "N/A";
    }

    $startX = intval($skyblockArray[$name]["Area"]["start"]["X"]);
    $startZ = intval($skyblockArray[$name]["Area"]["start"]["Z"]);
    $endX = intval($skyblockArray[$name]["Area"]["end"]["X"]);
    $endZ = intval($skyblockArray[$name]["Area"]["end"]["Z"]);

    $length = $endX - $startX;
    $width = $endZ - $startZ;

    return "{$length} x {$width}";
  }
  public function getIslandAt(Player $player) {

    $worldsArray = $this->cfg->get("SkyBlockWorlds", []);

    if (in_array($player->getLevel()->getFolderName(), $worldsArray)) {

      $skyblockArray = $this->skyblock->get("SkyBlock", []);
      $islandOwner = false;
      foreach(array_keys($skyblockArray) as $skyblock) {

        if (((int) $player->getX() >= $skyblockArray[$skyblock]["Area"]["start"]["X"] - 5 && (int) $player->getZ() >= $skyblockArray[$skyblock]["Area"]["start"]["Z"] - 5 && (int) $player->getX() <= $skyblockArray[$skyblock]["Area"]["end"]["X"] + 5 && (int) $player->getZ() <= $skyblockArray[$skyblock]["Area"]["end"]["Z"] + 5) && ($player->getLevel()->getFolderName() === $skyblockArray[$skyblock]["World"])) {

          $islandOwner = $skyblock;
          break;
        }
      }

      return $islandOwner;
    } else {

      return false;
    }
  }
  public function getPlayersOnIsland(Player $player): array {

    $name = strtolower($player->getName());
    $onlinePlayers = $this->getServer()->getOnlinePlayers();
    $skyblockArray = $this->skyblock->get("SkyBlock", []);
    $onIsland = [];

    foreach($onlinePlayers as $p) {

      $pX = (int) $p->getX();
      $pZ = (int) $p->getZ();
      $pWorld = $p->getLevel();

      if ($pWorld->getFolderName() === $skyblockArray[$name]["World"]) {

        if ($pX >= $skyblockArray[$name]["Area"]["start"]["X"] && $pX <= $skyblockArray[$name]["Area"]["end"]["X"] && $pZ >= $skyblockArray[$name]["Area"]["start"]["Z"] && $pZ <= $skyblockArray[$name]["Area"]["end"]["Z"]) {

          array_push($onIsland, $p->getName());
        }
      }
    }

    return $onIsland;
  }

  public function getIslandFolder($data){
    $file = new Config($this->getDataFolder(). "islands/". $data.".island", Config::YAML);
    return $file;
  }

  public function CreateIsland(Player $player, array $data){
    $x1 = $data["Pos1"]["x"];
    $x2 = $data["Pos2"]["x"];
    $y1 = $data["Pos1"]["y"];
    $y2 = $data["Pos2"]["y"];
    $z1 = $data["Pos1"]["z"];
    $z2 = $data["Pos2"]["z"];
    $level = $player->getLevel();
    $blocksArray = [];
    for ($x = min($x1, $x2); $x <= max($x1, $x2); $x++) {

      for ($y = min($y1, $y2); $y <= max($y1, $y2); $y++) {

        for ($z = min($z1, $z2); $z <= max($z1, $z2); $z++) {

          $block = $level->getBlockAt($x, $y, $z, true, false);
          $blockID = $block->getID();
          $blockDamage = $block->getDamage();

          if ($blockID === BlockFactory::get(Block::LEAVES)->getID() || $blockID === BlockFactory::get(Block::LEAVES2)->getID()) {

            $oakNoDecay = [0, 4, 12];
            $spruceNoDecay = [1, 5, 13];
            $birchNoDecay = [2, 6, 14];
            $jungleNoDecay = [3, 7, 15];
            $acaciaNoDecay = [0, 4, 12];
            $darkNoDecay = [1, 5, 13];

            if (in_array($blockDamage, $oakNoDecay) && $blockID === BlockFactory::get(Block::LEAVES)->getID()) $blockDamage = 8;
            if (in_array($blockDamage, $spruceNoDecay) && $blockID === BlockFactory::get(Block::LEAVES)->getID()) $blockDamage = 9;
            if (in_array($blockDamage, $birchNoDecay) && $blockID === BlockFactory::get(Block::LEAVES)->getID()) $blockDamage = 10;
            if (in_array($blockDamage, $jungleNoDecay) && $blockID === BlockFactory::get(Block::LEAVES)->getID()) $blockDamage = 11;
            if (in_array($blockDamage, $acaciaNoDecay) && $blockID === BlockFactory::get(Block::LEAVES2)->getID()) $blockDamage = 8;
            if (in_array($blockDamage, $darkNoDecay) && $blockID === BlockFactory::get(Block::LEAVES2)->getID()) $blockDamage = 9;
          }
          array_push($blocksArray, $blockID . " " . $blockDamage);
        }
      }
    }
    @mkdir($this->getDataFolder()."islands/");
    $file = new Config($this->getDataFolder()."islands/".$data["name"].".island", Config::YAML, [
      "x1" => $data["Pos1"]["x"],
      "x2" => $data["Pos2"]["x"],
      "y1" => $data["Pos1"]["y"],
      "y2" => $data["Pos2"]["y"],
      "z1" => $data["Pos1"]["z"],
      "z2" => $data["Pos2"]["z"],
      "URL" => "https://i.imgur.com/86W7Wh5.jpg",
      "description" => "Đảo trên trời, free nha bà con, vừa đẹp vừa miễn phí",
      "CustomX" => $data["Spawn"]["x"],
      "CustomY" => $data["Spawn"]["y"],
      "CustomZ" => $data["Spawn"]["z"],
      "Blocks" => $blocksArray
    ]);
    return true;
  }

  public function ImageIsland($data){
    $file = new Config($this->getDataFolder(). "islands/". $data, Config::YAML);
    return $file->get("URL");
  }

  public function getIslands(){
    $extensions = "island";
    $folder = $this->getDataFolder(). 'islands/';
    $folder = trim($folder);
    $folder = ($folder == '') ? './' : $folder;
    if (!is_dir($folder)){
      return false;
    }
    $files = array();
    if ($dir = @opendir($folder)){
      while($file = readdir($dir)){
        if (!preg_match('/^\.+$/', $file) and
          preg_match('/\.('.$extensions.')$/', $file)){
          $files[] = $file;        
        }      
      }   
      closedir($dir);  
    }else{
      return false;
    }
    if (count($files) == 0){
      return false;
    }
    return $files;
  }

  public function getDesIsland(string $file) :?String{
    $file = new Config($this->getDataFolder(). "islands/". $file, Config::YAML);
    return $file->get("Description");
  }

  public function getIslandNamea(string $file) :?String{
    $file = new Config($this->getDataFolder(). "islands/". $file, Config::YAML);
    return $file->get("name");
  }

  public function checkIsland($player, $block){
    $level = $this->level;
    $valuableBlocks = $this->cfg->get("Valuable Blocks", []);
    $worldsArray = $this->cfg->get("SkyBlockWorlds", []);

    if (in_array($player->getLevel()->getFolderName(), $worldsArray)) {

      $skyblockArray = $this->skyblock->get("SkyBlock", []);
      $blockX = $block->getX();
      $blockY = $block->getY();
      $blockZ = $block->getZ();
      $islandOwner = "";

      foreach (array_keys($skyblockArray) as $skyblocks) {

        $startX = $skyblockArray[$skyblocks]["Area"]["start"]["X"];
        $startY = $skyblockArray[$skyblocks]["Area"]["start"]["Y"];
        $startZ = $skyblockArray[$skyblocks]["Area"]["start"]["Z"];
        $endX = $skyblockArray[$skyblocks]["Area"]["end"]["X"];
        $endY = $skyblockArray[$skyblocks]["Area"]["end"]["Y"];
        $endZ = $skyblockArray[$skyblocks]["Area"]["end"]["Z"];

        if (($blockX > $startX && $blockY > $startY && $blockZ > $startZ && $blockX < $endX && $blockY < $endY && $blockZ < $endZ) && ($player->getLevel()->getFolderName() === $skyblockArray[$skyblocks]["World"])) {

          $islandOwner = $skyblocks;
          break;
        }
      }
      if ($islandOwner === "") {

        if ($player->hasPermission("skyblock.bypass")) {
          return true;
        }

        return false;
      } else if (in_array($player->getName(), $skyblockArray[$islandOwner]["Members"])) {

        if (array_key_exists($block->getID(), $valuableBlocks)) {

          $skyblockArray[$islandOwner]["Value"] += $valuableBlocks[$block->getID()];
          $this->skyblock->set("SkyBlock", $skyblockArray);
          $this->skyblock->save();
        }
        return true;
      } else {

        if ($player->hasPermission("skyblock.bypass") || $skyblockArray[$islandOwner]["Settings"]["Build"] === "off") {

          if (array_key_exists($block->getID(), $valuableBlocks)) {

            $skyblockArray[$islandOwner]["Value"] += $valuableBlocks[$block->getID()];
            $this->skyblock->set("SkyBlock", $skyblockArray);
            $this->skyblock->save();
          }
          return true;
        }
        return false;
      }
      return false;
    }
    if($player->getLevel()->getName() !== "skyblock") return false;
  }

  public function getIslandItem(string $file) :array{
    $pickaxe = Item::get(257, 0 ,1);
    $pickaxe->setCustomName("Cúp khởi đầu");
    $pickaxe->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment(15), 1));
    $axe = Item::get(275, 0 ,1);
    $axe->setCustomName("Rìu khởi đầu");
    $axe->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment(15), 1));
    $glowingob = Item::get(Item::STONECUTTER);
    $glowingob->setCustomName("Ore Generator");
    return [
      $glowingob,
      Item::get(85, 0, 2),
/*      Item::get(10, 0, 2),
      Item::get(8, 0, 2),*/
      Item::get(295, 0 , 10),
      Item::get(361, 0, 10),
      Item::get(103, 0, 1),
      Item::get(6, 0, 2),
      $pickaxe,
      $axe
    ];
  }
}
