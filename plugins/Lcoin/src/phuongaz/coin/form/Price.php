<?php

namespace phuongaz\coin\form;

use pocketmine\Player;

use jojoe77777\FormAPI\CustomForm;

Class Price extends CustomForm{

	private $player;

	public function __construct(){
		$c = function(Player $player, ?array $data){};
		parent::__construct($c);
	}

	public function __init() :void{
		$this->addLabel("§l§d10.000 §fVND§f →§e 10§f Scoin");
		$this->addLabel("§l§d20.000 §fVND§f →§e 25§f Scoin");
		$this->addLabel("§l§d50.000 §fVND§f →§e 65§f Scoin");
		$this->addLabel("§l§d100.000 §fVND§f →§e 135§f Scoin");
		$this->addLabel("§l§d200.000 §fVND§f →§e 270§f Scoin");
		$this->addLabel("§l§d500.000 §fVND§f →§e 700§f Scoin");
	}
}