<?php

namespace phuongaz\LOCMCore\Command;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use phuongaz\LOCMCore\Loader;
use pocketmine\Player;
Class PvpCommand extends Command{

	public function __construct(){
		parent::__construct("pvp", "teleport to pvp");
	}

	public function execute(CommandSender $sender, string $label, array $args):bool{
		if($sender instanceof Player){
			//$hub = Loader::getInstance()->getServer()->getLevelByName("world")->getSafeSpawn();
			//Loader::getInstance()->getPlayerManager($sender)->teleport($hub);
			//$sender->teleport($hub);
		}
		return true;
	}
}