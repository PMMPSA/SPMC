<?php

namespace phuongaz\LOCMCore\Command;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use phuongaz\LOCMCore\Loader;
use pocketmine\Player;

use phuongaz\LOCMCore\Form\RankForm;
Class RankCommand extends Command{

	public function __construct(){
		parent::__construct("rank", "Rank store");
	}

	public function execute(CommandSender $sender, string $label, array $args):bool{
		if($sender instanceof Player){
			$form = new RankForm();
			$form->send($sender);
		}
		return true;
	}
}