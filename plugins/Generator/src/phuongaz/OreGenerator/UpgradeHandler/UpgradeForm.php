<?php

namespace phuongaz\OreGenerator\UpgradeHandler;

use pocketmine\Player;
use pocketmine\Server;
use pocketmine\nbt\tag\IntTag;
use onebone\economyapi\EconomyAPI;
use jojoe77777\FormAPI\{SimpleForm, CustomForm, ModalForm};
use pocketmine\item\Item;

Class UpgradeForm{

	/** @var Player $player*/
	private $player;

	public function __construct(Player $player){
		$this->player = $player;
	}

	public function init() :void{
		$form = new SimpleForm(function(Player $player, ?int $data){
			if(is_null($data));
			if($data == 0) $this->upgradeForm();
			if($data == 1) $this->buyForm();
		});
		$form->setTitle("§l§6GENERATOR ORE");
		$form->addButton("UPGRADE");
		$form->addButton("BUY MORE");
		$form->sendToPlayer($this->player);
	}

	public function upgradeForm() :void{
		$inv = $this->player->getInventory();
		$item = $inv->getItemInHand();
		if($item->getId() !== 245){
			$form = new CustomForm(function(Player $player, ?array $data){});
			$form->setTitle("UPGRADE GENERATOR");
			$form->addLabel("You need to put the ore generator on hand");
			$form->sendToPlayer($this->player);
			return;
		}
		$nbt = $item->getNamedTag();
		if($nbt->hasTag("generator", IntTag::class)){
            $level = $nbt->getInt("generator");
        }else $level = 1;
        $nbt->setInt("generator", $level);
		$next = ($nbt->getInt("generator"))+1;
		$price = (10000 * $next) * $item->getCount();
		$form = new ModalForm(function(Player $player, ?bool $data) use ($inv, $item, $nbt, $next, $price){
			if($data){
				if($nbt->hasTag("generator", IntTag::class)){
					$nbt->setInt("generator", $next);
					if(EconomyAPI::getInstance()->myMoney($player) >= $price){
						if($next == 10){
							$player->sendMessage("Level is max");
							return;
						}
						$item->setNamedTag($nbt);
						$item->setCustomName("ORE Generator (".$next.")");
						$inv->setItemInHand($item);
						EconomyAPI::getInstance()->reduceMoney($player, $price);
						$player->sendMessage("Successful upgrade to : ". $next);
						return;
					}
					$player->sendMessage("You not enough money to upgrade");
				}
			}
		});
		$form->setTitle("§6§lUPGRADE GENERATOR");
		$content = "";
		$content .= "Do you want upgrade ore generator to level : ".$next." ?\n";
		$content .= "Price: $price ";
		$form->setContent($content);
		$form->setButton1("Yes");
		$form->setButton2("No");
		$form->sendToPlayer($this->player);
	}

	public function buyForm() :void{
		$form = new CustomForm(function(Player $player, ?array $data){
			if(is_null($data)) $this->init();
			if(isset($data[0])){
				$modal = new ModalForm(function(Player $player, ?bool $bool) use ($data){
					if(is_null($bool)) $this->buyForm();
					if($bool){
						if(EconomyAPI::getInstance()->myMoney($player) >= $data[0] *1000){
							$inv = $player->getInventory();
							$item = Item::get(245);

							$nbt = $item->getNamedTag();
							$nbt->setInt("generator", 1);
							$item->setNamedTag($nbt);
							$item->setCustomName("ORE Generator (1)");
							if($inv->canAddItem($item)){
								$inv->addItem($item);
								EconomyAPI::getInstance()->reduceMoney($player, $data[0] *1000);
								$player->sendMessage("Done!");	
							}else{
								$player->sendMessage("Inventory is full");
							}
						}
					}
				});
				$modal->setTitle("§l§6COMFIRM BUY");
				$modal->setContent("Do you want buy x". $data[0] . ' ore generator with ' .$data[0] *1000 .' money');
				$modal->setButton1("Yes");
				$modal->setButton2("No");
				$modal->sendToPlayer($this->player);
			}
		});
		$form->setTitle("GENERATOR SHOP");
		$form->addSlider("1000 Money/1", 1, 10);
		$form->sendToPlayer($this->player);
	}
}