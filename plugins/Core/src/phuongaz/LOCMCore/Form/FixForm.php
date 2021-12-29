<?php

namespace phuongaz\LOCMCore\Form;

use jojoe77777\FormAPI\{SimpleForm, ModalForm};

use pocketmine\item\Item;
use pocketmine\item\{Armor, Tool};
use pocketmine\Player;
use onebone\economyapi\EconomyAPI;

Class FixForm{

	private $player;

	public function __construct(Player $player){
		$this->player = $player;
	}

	public function init() :void {
		$form = new SimpleForm(function(Player $player, ?int $data){
			if(is_null($data)) return;
			if($data == 0) $this->fixHand();
			if($data == 1) $this->fixAll();
		});
		$form->setTitle("§l§bFIX ITEM");
		$inv = $this->player->getInventory();
		$hand = $inv->getItemInHand();
		if($hand instanceof Tool or $hand instanceof Armor){
			$price = $this->getPrice($hand);
			$form->addButton("§l§f•§0 Vật Phẩm Trên Tay §f•\n §l§b(§e".$price ." §fXu§b)");
		}else{
			$form->setContent("§l§eChỉ có thể sửa chữa giáp hoặc công cụ");
			$form->addButton("§l§cX");
		}
		$price_all =  $this->getPrice(null, true);
		$form->addButton("§l§f•§0 Tất cả §f•\n §l§b(§e".$price_all."  §fXu§b)");
		$form->sendToPlayer($this->player);
	}

	public function fixHand() :void{
		$price = $this->getPrice($this->player->getInventory()->getItemInHand());
		$form = new ModalForm(function(Player $player, ?bool $data) use ($price){
			if(is_null($data)){
				$this->init();
				return;
			}
			if($data){
				if(EconomyAPI::getInstance()->myMoney($player) >= $price){
					$item = $player->getInventory()->getItemInHand()->setDamage(0);
					$player->getInventory()->setItemInHand($item);
					EconomyAPI::getInstance()->reduceMoney($player, $price);				
				}else{
					$player->sendMessage("§l§fKhông đủ§e $price §fđể sửa!");
					
				}
			}
		});
		$form->setTitle("§l§bFIX HAND");
		$form->setContent("§l§fBạn có muốn sửa chữa vật phẩm trên tay với giá§e $price §fXu?");
		$form->setButton1("§l§f•§0 Sửa Chữa §f•");
		$form->setButton2("§l§f•§0 Hủy §f•");
		$form->sendToPlayer($this->player);
	}

	public function fixAll() :void{
		$price = $this->getPrice(null, true);
		$form = new ModalForm(function(Player $player, ?bool $data) use ($price){
			if(is_null($data)){
				$this->init();
				return;
			}
			if($data){
				if(EconomyAPI::getInstance()->myMoney($player) >= $price){
	                foreach($player->getInventory()->getContents() as $index => $item) {
	                    if ($item instanceof Tool || $item instanceof Armor) {
	                        $item = $item->setDamage(0);
	                        $player->getInventory()->setItem($index, $item);
	                    }
	                }
					EconomyAPI::getInstance()->reduceMoney($player, $price);					
				}else{
					$player->sendMessage("§l§fKhông đủ§e $price §fđể sửa tất cả!");
				}
			}
		});
		$form->setTitle("§l§bFIX ALL");
		$form->setContent("§l§fSửa chữa tất cả vật phẩm với giá§e $price §fXu?");
		$form->setButton1("§l§f•§0 Sửa Chữa §f•");
		$form->setButton2("§l§f•§0 Hủy §f•");
		$form->sendToPlayer($this->player);
	}

	public function getPrice(?Item $item, $all = false) :int{
		$price = 0;
		if($all){
			foreach($this->player->getInventory()->getContents() as $item){
				if($item instanceof Tool or $item instanceof Armor){
					$price += intval($item->getDamage() *8);
				}
			}
			return $price;
		}else{
			return intval($item->getDamage() *8);
		}
	}
}