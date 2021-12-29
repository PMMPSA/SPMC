<?php

namespace phuongaz\LOCMCore\Command;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use phuongaz\LOCMCore\Loader;
use phuongaz\LOCMCore\Form\FunctionForm;
use pocketmine\Player;

Class FunctionCommand extends Command{

	public function __construct(){
		parent::__construct("function", "functions of server");
	}

	public function execute(CommandSender $sender, string $label, array $args):bool{
		if($sender instanceof Player){
			$form = new FunctionForm($sender);
			$form->send();
		}
		return true;
	}
}