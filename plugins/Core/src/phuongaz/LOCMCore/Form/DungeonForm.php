<?php

namespace phuongaz\LOCMCore\Form;

use pocketmine\Player;

use phuongaz\LOCMCore\Loader;

use jojoe77777\FormAPI\{SimpleForm, CustomForm};

use phuongaz\LOCMCore\Text\DungeonText;

Class DungeonForm{

	public function send(Player $player){
		$form = new SimpleForm(function(Player $player, ?int $data){
			if(is_null($data)) return;
			//if((int)date("H") == 20){
				$this->sendInfo($player, $data);
				Loader::getInstance()->getDungeonManager()->teleport($player, $data+1);			
			//}
		});
		$form->setTitle("§l§6DUNGEON");
/*		if(date("H") >= 20 and date("H") <= 22){*/
		$form->addButton("§l§f(§1 DUNGEON 1 §f)\n§0Cổng Đền");
/*		}else{
			$time = 0;
			if(date("H") > 22){ //23, 24
				$time = 24 - (date("H") - 22);
			}else{
				$time = 20 - date("H");
			}
			$m = 60 - date("i");
			$form->setContent("§l§fDungeon sẽ mở sau: §e$time §fgiờ§e $m §fphút");
			$form->addButton("Thoát");
		}*/
		$form->sendToPlayer($player);
	}

	public function sendInfo(Player $player, int $round){
		$form = new CustomForm(function(Player $player, ?array $args){});
		$text = DungeonText::$rounds[$round];
		$form->setTitle("§l§0INFO DUNGEON §6");
		$form->addLabel($text);
		$form->sendToPlayer($player);
	}
}