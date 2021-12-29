<?php

namespace Itzdvbravo\BravoClan\Forms\SubForms;

use pocketmine\Player;

use jojoe77777\FormAPI\SimpleForm;
use Itzdvbravo\BravoClan\Main;

Class ManagerSubForm extends SimpleForm{

	/** @var Player*/
	private $player; 

	public function __construct(Player $player){
		$this->player = $player;
		$callable = $this->getCallable();
		parent::__construct($callable);
	}

	/**
	* @return void
	*/
	public function setForm() :void{
		$this->addButton("§l§f•§0 Invite Member §f•", 1, "https://cdn4.iconfinder.com/data/icons/general-business/150/Invite-512.png");
		if($this->isLeader($this->player)){
			$this->addButton("§l§f•§0 Kick §l§f•", 1, "https://i.dlpng.com/static/png/6346066_preview.png");
			$this->addButton("§l§f•§0 Delete §f•", 1, "https://cdn1.iconfinder.com/data/icons/color-bold-style/21/56-512.png");			
		}else{
			$this->addButton("§l§f•§0 Leave §f", 1, "https://img.icons8.com/plasticine/2x/left.png");
		}
	}


	/**
	* @return callable
	*/
	public function getCallable() :callable{
		$callable = function(Player $player, ?int $data){
			if(is_null($data)) return;
			$lowname = strtolower($player->getName());
			$form = $this;
			if($data == 0){
				$form = new InviteSubForm($player);
				$form->setForm();
				$form->setTitle("§l§6Invite Form");
				$player->sendForm($form);
			}
			if($this->isLeader($player)){
				if($data == 1){
					$form = new KickSubForm($player);
					$form->setTitle("§l§6Kick Form");
					$form->setForm();
					$player->sendForm($form);
				}
				if($data == 2){
					$form = new DeleteSubForm($player);
					$form->setTitle("§l§6Delete");
					$form->setForm();
					$player->sendForm($form);
				} 
			}else{
				if($data == 1){
					$form = new LeaveSubForm($player);
					$form->setForm();
					$form->setTitle("§l§6Leave");
					$player->sendForm($form);
				} 
			}
			$player->sendForm($form);
		};
		return $callable;
	}

	/**
	* @param Player $player
	*
	* @return bool
	*/
	private function isLeader(Player $player) :bool{
		if(Main::$file->getClan(Main::$file->getMember(strtolower($player->getName()))["clan"])["leader"] === strtolower($player->getName())){
			return true;			
		}
		return false;
	}
}