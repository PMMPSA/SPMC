<?php

namespace phuongaz\coin\form;

use pocketmine\Player;

use jojoe77777\FormAPI\CustomForm;

Class Momo extends CustomForm{

	private $player;

	public function __construct(){
		$c = function(Player $player, ?array $data){};
		parent::__construct($c);
	}

	public function __init() :void{
		$this->addLabel("§l§fNạp §dmomo§f luôn luôn khuyến mãi §ex1.5§f giá trị nạp thẻ");
		$this->addLabel("§l§fSố tài khoản:§e 0386473400");
		$this->addLabel("§l§fNội dung:§e Nạp thẻ spmc + (Tên tài khoản cần nạp)");
		$this->addLabel("§l§fDuyệt trong vòng §e24§f giờ");
	}
}