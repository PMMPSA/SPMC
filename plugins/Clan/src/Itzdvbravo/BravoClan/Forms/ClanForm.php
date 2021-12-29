<?php

namespace Itzdvbravo\BravoClan\Forms;

use pocketmine\Player;

use jojoe77777\FormAPI\SimpleForm;
use Itzdvbravo\BravoClan\Main;
use Itzdvbravo\BravoClan\Forms\SubForms\{
	CreateSubForm,
	TopSubForm,
	InfoSubForm,
	ManagerSubForm
};

use onebone\economyapi\EconomyAPI;

Class ClanForm extends SimpleForm{

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
		if(!Main::$file->isInClan(strtolower($this->player->getName()))){
			$this->setContent("§l§e→§f Bạn chưa vào clan nào");
			$this->addButton("§l§f•§0 Tạo mới §f•", 1, "https://img.icons8.com/plasticine/2x/new.png");
			$this->addButton("§l§f•§0 BXH Clan §f•", 1, "https://mlkgwlbllwpx.i.optimole.com/4jEevwY-hX5XTe8-/w:200/h:200/q:100/https://entrinsik.com/wp-content/uploads/2015/11/Top-10-Icon-PNG-02569-e1447687505743.png");
		}else{
			$clan = Main::$file->getClan(Main::$clan->player[strtolower($this->player->getName())]);
			$this->setContent("§l§e→§f Level Clan: ". $clan["level"]);
			$this->addButton("§l§f•§0 Thông tin Clan §f•", 1, "https://upload.wikimedia.org/wikipedia/commons/thumb/2/25/Info_icon-72a7cf.svg/1200px-Info_icon-72a7cf.svg.png");
			$this->addButton("§l§f•§0 BXH Clan §f•", 1, "https://mlkgwlbllwpx.i.optimole.com/4jEevwY-hX5XTe8-/w:200/h:200/q:100/https://entrinsik.com/wp-content/uploads/2015/11/Top-10-Icon-PNG-02569-e1447687505743.png");
			$this->addButton("§l§f•§0 Quản lý Clan §f•", 1, "https://cdn0.iconfinder.com/data/icons/business-office-and-people-in-flat/256/Businessman-2-512.png");
		}
	}


	/**
	* @return callable
	*/
	public function getCallable() :?callable{
		$callable = function(Player $player, ?int $data){
			if(is_null($data)) return;
			$lowname = strtolower($player->getName());
			if(!Main::$file->isInClan($lowname)){
				if($data == 0){
					$form = new CreateSubForm($player);
					$form->setTitle("§l§eCREATE FORM");
					$form->setForm();
					$player->sendForm($form);
				} 
				if($data == 1){
					$form = new TopSubForm($player);
					$form->setForm();
					$form->setTitle("§l§eTOP FORM");
					$player->sendForm($form);
				}
			}else{
				if($data == 0){
					$form = new InfoSubForm($player);
					$form->setForm();
					$form->setTitle("§l§eINFO FORM");
					$player->sendForm($form);
				}
				if($data == 1){
					$form = new TopSubForm($player);
					$form->setTitle("§l§eTOP FORM");
					$form->setForm();
					$player->sendForm($form);
				}
				if($data == 2){
					$form = new ManagerSubForm($player);
					$form->setTitle("§l§eMANAGER FORM");
					$form->setForm();
					$player->sendForm($form);
				}
			}
		};
		return $callable;
	}
}