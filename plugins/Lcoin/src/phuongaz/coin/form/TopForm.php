<?php

namespace phuongaz\coin\form;

use pocketmine\Player;
use jojoe77777\FormAPI\CustomForm;

Class TopForm extends CustomForm{

	public function __construct(){
		$c = function(Player $player, ?array $data){};
		parent::__construct($c);
	}
}