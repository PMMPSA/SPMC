<?php

namespace Itzdvbravo\BravoClan\Forms\SubForms;

use pocketmine\Player;

use jojoe77777\FormAPI\CustomForm;
use Itzdvbravo\BravoClan\Main;
use onebone\economyapi\EconomyAPI;
use Itzdvbravo\BravoClan\Forms\ClanForm;

Class CreateSubForm extends CustomForm{

	/*** @var Player*/
	private $player;

	public function __construct(Player $player){
		$this->player = $player;
		$callable = $this->getCallable();
		parent::__construct($callable);
	}
	/**
	* @return void
	*/
	public function setForm() :void {
		$player = $this->player;
		$this->setTitle("§l§6Create new Clan");
		$this->addLabel("§l§e⇁ §fPhí để tạo clan:§e 30000 §fXu");
		$money = EconomyAPI::getInstance()->myMoney($player);
		$this->addLabel("§l§e⇁ §fBạn hiện có: §e". $money ." §fXu");
		if($money >= 30000){
			$this->addInput("§l§e⇁ §fTên clan:", "Nap tien di");
		}else{
			$this->addLabel("§l§fBạn không đủ tiền để tạo clan");
		}
	}

	/**
	* @return callable
	*/
	public function getCallable() :callable{
		$callable = function(Player $player, ?array $data){
			if(is_null($data)){
				$form = new ClanForm($player);
				$form->setForm();
				$player->sendForm($form);
				return;
			}
			if(EconomyAPI::getInstance()->myMoney($player) >= 30000){
				if(!isset($data[2])) return;
				Main::$file->setClan($data[2], $player->getName());
                Main::$clan->player[strtolower($player->getName())] = $data[2];
                EconomyAPI::getInstance()->reduceMoney($player, 30000);
			}
		};
		return $callable;
	}
}