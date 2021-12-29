<?php

namespace phuongaz\LOCMCore\Command;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use phuongaz\LOCMCore\Loader;

Class HubCommand extends Command{

	public function __construct(){
		parent::__construct("hub", "Back to hub");
	}

	public function execute(CommandSender $sender, string $label, array $args):bool{
		if($sender instanceof Player){
			$hub = Loader::getInstance()->getServer()->getDefaultLevel()->getSafeSpawn();
			Loader::getInstance()->getPlayerManager($sender)->teleport($hub);
		}
		return true;
	}
}