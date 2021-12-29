<?php

namespace Itzdvbravo\BravoClan\Forms\SubForms;

use pocketmine\Player;

use jojoe77777\FormAPI\ModalForm;
use Itzdvbravo\BravoClan\Forms\ClanForm;
use Itzdvbravo\BravoClan\Main;
use pocketmine\Server;

Class DeleteSubForm extends ModalForm{

	/** @var Player */
	private $player;

	public function __construct(Player $player){
		$this->player = $player;
		$this->setForm();
		$callable = $this->getCallable();
		parent::__construct($callable);
	}

	/**
	* @return void
	*/
	public function setForm() :void{
		$this->setTitle("§l§eDelete Clan");
		$this->setContent("§l§d Có chắc muốn xóa clan?");
		$this->setButton1("§fCó");
		$this->setButton2("§fNo");
	}

	/**
	* @return callable
	*/
	public function getCallable() :callable{
		$callable = function(Player $player, ?bool $data){
			if(is_null($data)){
				$form = new ClanForm($player);
				$form->setForm();
				$player->sendForm($form);				
			}
			if($data){
				Server::getInstance()->getCommandMap()->dispatch($player, "clan delete");
			} 
		};
		return $callable;
	}
}