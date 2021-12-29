<?php

namespace phuongaz\coin\command;

use pocketmine\command\CommandSender;
use pocketmine\command\PluginCommand;
use jojoe77777\FormAPI\SimpleForm;

use phuongaz\coin\form\NapTheForm2;
use phuongaz\coin\Coin;

abstract class CoinCommand extends PluginCommand
{

    final public function execute(CommandSender $sender, string $commandLabel, array $args)
    {
    	if(!isset($args)) $this->mainForm($sender);
        return $this->_execute($sender, $args);
    }

    abstract public function _execute(CommandSender $sender, array $args): bool;

	public function mainForm($player){
		
		if($player instanceof Player){
				$form = new SimpleForm(function (Player $player, ?int $data){
				if(isset($data)){
					switch($data){
						/*case 0:
						$this->getServer()->getCommandMap()->dispatch($player, "paylcoin");
						break;*/
						case 0:
						$this->getServer()->getCommandMap()->dispatch($player, "toplcoin");
						break;
						case 1:
						$this->getServer()->getCommandMap()->dispatch($player, "seelcoin");
						break;
						case 2:
						$form = new NapTheForm2($player);
						$form->__init();
						$form->setTitle("§l§6LOCM DONATE");
						$player->sendForm($form);
						break;
						case 3:
						$this->getServer()->getCommandMap()->dispatch($player, "lcoinshop");
						break;
					}
				}
			});
			$p = Coin::getInstance()->getCoin($player);
			$form->setTitle("§l§eＳＣＯＩＮ");
			$form->setContent("§l§d↦ §6Your Scoin:§e ".$p." $");
			//$form->addButton("§l§f•§0 Chuyển tiền §f•");//0
			$form->addButton("§l§f•§0 BXH Scoin §f•");//1
			$form->addButton("§l§f•§0 Xem Scoin người khác §f•"); //2
			$form->addButton("§l§f•§0 Nạp Scoin §f•"); //3
			$form->addButton("§l§f•§0 Cửa Hàng Scoin §f•"); //4
			$form->sendToPlayer($player);
		}
	}
}