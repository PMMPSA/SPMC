<?php

namespace phuongaz\Heaven\Commands;

use pocketmine\Player;
use pocketmine\Server;
use phuongaz\Heaven\GemsMain;

use jojoe77777\FormAPI\{SimpleForm, CustomForm, ModalForm};
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\command\ConsoleCommandSender;
use phuongaz\Coin\Coin;

Class ChanceGemsCommand extends Command{

	public function __construct(){
		parent::__construct("chancegem", " Mua goi ngoc kham");
	}

	public function execute(CommandSender $sender, string $label, array $args) :bool{
		if($sender instanceof Player){
			$form = new SimpleForm(function(Player $player, ?int $data){
				if(is_null($data)) return;
				$chance = 3;
				$price = 5;
				$count = $data+3;
				if($data !== 0){
					$price = ($data+1)*$price;
					$chance = $chance*($data+2);
				}
				if(Coin::getInstance()->getCoin($player) >= $price){
					$form = new ModalForm(function(Player $player, ?bool $data) use ($price, $chance, $count){
						if(is_null($data)) return false;
						if($data){
							$cmd = "gems chance ".(string)$chance." 1-".(string)$count. " ". $player->getName();
							Server::getInstance()->getCommandMap()->dispatch(new ConsoleCommandSender(), $cmd);
							Coin::getInstance()->reduceCoin($player, $price);
						}
					});
					$form->setContent("§l§6 Bạn có muốn mua gói ngọc khảm §e".$data);
					$form->setButton1("§l§0Thử vận may");
					$form->setButton2("§l§0Thôi không chơi đâu");
					$form->sendToPlayer($player);
				} 
			});
			$form->setTitle("§l§dGÓI VẬN MAY");
			$form->setContent("§l§e§f LCoin hiện có");
			$form->addButton("§l§1Gói§6 1 §f(§e3§a Loại§f) §e|§e 5 §fLcoin\n §l§fTỉ lệ trúng:§e 3%");
			$form->addButton("§l§1Gói§6 2 §f(§e4§a Loại§f) §e|§e 10 §fLcoin\n §l§fTỉ lệ trúng:§e 9%");
			$form->addButton("§l§1Gói§6 3 §f(§e5§a Loại§f) §e|§e 15 §fLcoin\n §l§fTỉ lệ trúng:§e 12%");
			$form->addButton("§l§1Gói§6 4 §f(§e6§a Loại§f) §e|§e 20 §fLcoin\n §l§fTỉ lệ trúng:§e 15%");
			$form->sendToPlayer($sender);
		}
		return true;
	}
}