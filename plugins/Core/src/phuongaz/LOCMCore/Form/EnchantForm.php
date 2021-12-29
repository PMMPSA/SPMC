<?php

declare(strict_types=1);

namespace phuongaz\LOCMCore\Form;

use jojoe77777\FormAPI\{
	SimpleForm,
	CustomForm
};

use pocketmine\utils\TextFormat as TF;
use pocketmine\Player;


use pocketmine\item\{
	Item,
	enchantment\Enchantment
};
use onebone\economyapi\EconomyAPI;
use phuongaz\LOCMCore\Loader;

Class EnchantForm {

	public function send(Player $player) {
		$form = new SimpleForm(function(Player $player, ?int $data){
			if(is_null($data)) return;
			if(is_null(Loader::$enchantments[$data])) return;
			$this->ConfirmForm(Loader::$enchantments[$data], $player);
		});

		$form->setTitle("§l§dENCHANTMENT");
		$form->setContent("§l§e→ §fChọn phù phép phù hợp!");
		foreach(Loader::$enchantments as $enchant){
			$form->addButton("§l§f•§0 ". $enchant->getName(). " §f•");
		}
		$form->sendToPlayer($player);
	}

	public function ConfirmForm(Enchantment $enchantment, Player $player) {
		$form = new CustomForm(function(Player $player, ?array $data) use ($enchantment){
			if(is_null($data)) return;
			if(EconomyAPI::getInstance()->myMoney($player) >= intval($data[3])*10000){
				if(Loader::getInstance()->EnchantItem($player, $enchantment, intval($data[3]))){
					$item = $player->getInventory()->getItemInHand();
					//EconomyAPI::getInstance()->reduceMoney($player, intval($data[3])*10000);
					$this->CustomForm($player, "§l§e→ §fPhù phép thành công" , $item);
				}else{
					$this->CustomForm($player, "§l§e→ §fVật phẩm phải là công cụ hoặc giáp");
				}
				
			}else $this->CustomForm($player, "§l§cKhông đủ xu");
		});

		$form->setTitle("§l§eConfirm Enchantment");
		$form->addLabel("§l§e→ §fEnchantment:§e ". $enchantment->getName());
		$form->addLabel("§l§e→ §fMax level enchant:§e ".$enchantment->getMaxLevel());
		$form->addLabel("§l§e→ §fYour money :". EconomyAPI::getInstance()->myMoney($player));
		if($player->hasPermission("enchant.5")){
			if($player->isOp()){
				$form->addSlider("§l§e→ §f10000 xu§e/§e 1§f Level", 1, 30 , 1);
			}else $form->addSlider("§l§e→ §f10000 xu§e/§e 1§f Level", 1, 5 , 1);
		}else{
			$form->addSlider("§l§e→ §f10000 xu§e/§e 1§f Level", 1, 2 , 1);
			$form->addLabel("§l§e→ §fKhi bạn có §eVIP3§f trở lên, bạn có thể nâng cấp độ phù phép lên cấp §e5");			
		}
		$form->sendToPlayer($player);
	}

	public function CustomForm(Player $player, string $string, ?Item $item = null) {
		$form = new CustomForm(function(Player $player, $data){ if(is_null($data)) return; });
		$form->setTitle("Notice");
		$form->addLabel($string);
		if($item !== null){
			$form->addLabel("§l§eEnchantments:");
			$form->addLabel("§l§e→ §fName Item: ". $item->getCustomName());
			foreach($item->getEnchantments() as $enchant){
				$enchantm = $enchant->getType();
				$form->addLabel("§e§l»§f Name:§r ". $enchantm->getName(). " §7| §fLevel:§e ". $enchant->getLevel()); 
			}
		}
		$form->sendToPlayer($player);
		return;
	}
}