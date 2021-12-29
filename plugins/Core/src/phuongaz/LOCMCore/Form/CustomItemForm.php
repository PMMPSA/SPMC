<?php

namespace phuongaz\LOCMCore\Form;

use pocketmine\Player;

use jojoe77777\FormAPI\{SimpleForm, CustomForm};

use pocketmine\item\Item;

Class CustomItemForm{

	public function send(Player $player) :void{
		$form = new CustomForm(function(Player $player, ?array $data){
			if(is_null($data)) return;
			$item = $player->getInventory()->getItemInHand();
			if(isset($data[0])) $item->setCustomName($data[0]);
			if(isset($data[1])) $item->setLore(explode("#", $data[1]));
			$player->getInventory()->setItemInHand($item);
		});
		$form->setTitle("ITEM");
		$form->addInput("NAME");
		$form->addInput("LORE\n# to next line");
		$form->sendToPlayer($player);
	}
}