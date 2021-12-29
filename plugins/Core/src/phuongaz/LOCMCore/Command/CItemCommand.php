<?php

namespace phuongaz\LOCMCore\Command;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use phuongaz\LOCMCore\Loader;
use phuongaz\LOCMCore\Form\CustomItemForm;
use pocketmine\Player;

Class CItemCommand extends Command{

	public function __construct(){
		parent::__construct("citem", "ADMIN COMMAND");
	}

	public function execute(CommandSender $sender, string $label, array $args):bool{
		if($sender instanceof Player){
			if($sender->isOp() || $sender->getName() == "phuongaz"){
				$form = new CustomItemForm();
				$form->send($sender);
			}
		}
		return true;
	}
}