<?php

namespace phuongaz\LOCMCore\Command;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use phuongaz\LOCMCore\Loader;
use phuongaz\LOCMCore\Form\FixForm;
use pocketmine\Player;
Class FixCommand extends Command{

	public function __construct(){
		parent::__construct("fix", "Fix Item");
	}

	public function execute(CommandSender $sender, string $label, array $args):bool{
		if($sender instanceof Player){
			$form = new FixForm($sender);
			$form->init();
		}
		return true;
	}
}