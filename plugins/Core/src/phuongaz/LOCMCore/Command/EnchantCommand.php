<?php

namespace phuongaz\LOCMCore\Command;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use phuongaz\LOCMCore\Loader;
use pocketmine\Player;
use phuongaz\LOCMCore\Form\EnchantForm;
Class EnchantCommand extends Command{

	public function __construct(){
		parent::__construct("ec", "Enchantment");
	}

	public function execute(CommandSender $sender, string $label, array $args):bool{
		if($sender instanceof Player){
			$form = new EnchantForm();
			$form->send($sender);
		}
		return true;
	}
}