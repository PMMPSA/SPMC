<?php

namespace RedCraftPE\RedSkyBlock\Commands\SubCommands;

use pocketmine\utils\TextFormat;
use pocketmine\command\CommandSender;

use RedCraftPE\RedSkyBlock\SkyBlock;
use RedCraftPE\RedSkyBlock\Commands\Island;
use jojoe77777\FormAPI\CustomForm;
class NewIsland {

  private static $instance;
  private $cached;

  public function __construct() {

    self::$instance = $this;
  }

  public function onNewIslandCommand(CommandSender $sender, array $args): bool {

    if ($sender->hasPermission("skyblock.newisland")) {
    	if(!isset($args[1])){
        Skyblock::getInstance()->getIslandFolder("1");
    		$sender->sendMessage(".");
    	}else{
    		if($args[1] == "create" and isset($args[2])){
    			$this->cached["name"] = $args[2];
    			$sender->sendMessage("Create island cached name: ". $args[2]);
          var_dump($this->cached);
    		}
    		if($args[1] == "Pos1"){
    			$this->cached["Pos1"] = [
    				"x" => round($sender->getX()),
    				"y" => round($sender->getY()),
    				"z" => round($sender->getZ())
    			];
    			$sender->sendMessage("set pos1");
    		}
    		if($args[1] == "Pos2"){
    			$this->cached["Pos2"] = [
    				"x" => round($sender->getX()),
    				"y" => round($sender->getY()),
    				"z" => round($sender->getZ())
    			];
    			$sender->sendMessage("set pos2");
    		}
        if($args[1] == "makespawn"){
          $xPos = ceil($sender->getX());
          $yPos = ceil($sender->getY());
          $zPos = ceil($sender->getZ());

          $x = min(0 + $this->cached["Pos1"]["x"], 0 +  $this->cached["Pos2"]["x"]);
          $y = min( $this->cached["Pos1"]["y"], $this->cached["Pos2"]["y"]);
          $z = min(0 +  $this->cached["Pos1"]["z"], 0 +  $this->cached["Pos2"]["z"]);

          $distanceFromX1 = $xPos - $x;
          $distanceFromY1 = ($yPos - $y) + 1;
          $distanceFromZ1 = $zPos - $z;
          $this->cached["Spawn"]["x"] = $distanceFromX1;
          $this->cached["Spawn"]["y"] = $distanceFromY1;
          $this->cached["Spawn"]["z"] = $distanceFromZ1;
          $sender->sendMessage("make spawn");

        }
    		if($args[1] == "set"){
   				$new = SkyBlock::getInstance()->createIsland($sender, $this->cached);
   				if($new){
   					unset($this->cached);
            $sender->sendMessage("set data to island");
   				}
    		}
    	}
    }
    return true;
  }
}
