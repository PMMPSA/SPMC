<?php

namespace phuongaz\LOCMCore\Form;

use pocketmine\Player;

use jojoe77777\FormAPI\{
	CustomForm,
	SimpleForm
};

Class TutorialForm{

	public function send(Player $player){
		$form = new CustomForm(function(Player $player, ?array $data){});
		$form->setTitle("§l§eＳＰＭＣ§a ＳＫＹＢＬＯＣＫ");
		$form->addLabel("§l§fNếu bạn muốn nạp thẻ hãy sử dụng lệnh (§e/lcoin§f)");
		$form->addLabel("§l§fQuản lý đảo nhanh chóng: (§e/is§f)");
		$form->addLabel("§l§fTới đấu trường (§e/pvp§f)");
		$form->addLabel("§l§fMở chợ đen (§e/ah§f)");
		$form->addLabel("§l§fMở Season pass nhanh (§e/ssp§f)");
		$form->addLabel("§l§fMở hệ thống điểm tích lũy (§e/point§f)");
		$form->sendToPlayer($player);
	}
}