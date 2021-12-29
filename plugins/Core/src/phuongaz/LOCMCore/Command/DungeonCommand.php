<?php

namespace phuongaz\LOCMCore\Command;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use phuongaz\LOCMCore\Loader;
use phuongaz\LOCMCore\Form\DungeonForm;
use pocketmine\Player;
Class DungeonCommand extends Command{

	public function __construct(){
		parent::__construct("dungeon", "Tham gia dungeon");
	}

	public function execute(CommandSender $sender, string $label, array $args):bool{
		if($sender instanceof Player){
			$form = new DungeonForm();
			$form->send($sender);
		}
		return true;
	}
}