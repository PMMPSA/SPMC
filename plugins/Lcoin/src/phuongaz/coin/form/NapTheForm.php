<?php

declare(strict_types=1);

namespace phuongaz\coin\form;

use AltayForm\
{
	form\CustomForm,
	form\Form,
	form\element\Dropdown,
	form\element\Input,
	form\element\Label,
};
use pocketmine\Player;
use phuongaz\coin\Coin;

class NapTheForm extends CustomForm{

	private $plugin; 

	public function __construct(Coin $plugin, Player $player){
		$this->plugin = $plugin;
		parent::__construct("§l§fDONATE", [
		new Label("Lcoin của bạn: ", $plugin->getCoin($player)),
		new Dropdown("§a§lLoại thẻ", ["Viettel", "Mobifone", "Vinaphone", "Zing (Khuyến Khích)"]),
		new Dropdown("§a§lMệnh giá §f(Sai mệnh giá mất thẻ)", ["10000", "20000", "50000", "100000", "200000", "500000"]),
		new Input("§l§ePIN", "(Mã thẻ)"),
		new Input("§l§eSERI", "(MÃ SERI)"),
		]);
	}
	    
	public function onSubmit(Player $player) : ?Form{
		$ten = $this->getElement(0)->getOptions()[$this->getElement(0)->getValue()];
		$card_value = $this->getElement(1)->getOptions()[$this->getElement(1)->getValue()];
		$pin = (string) $this->getElement(2)->getValue();
		$seri = (string) $this->getElement(3)->getValue();
		if(!is_numeric($pin) || !is_numeric($seri)){
			$player->sendMessage("§r•§6 Số liệu bạn vừa điền không phải là số");
			return null;
		}
		switch($ten){
			case "Viettel":
			$mang = 1;
			$ten = "Viettel";
			$this->plugin->xuLyCard($pin, $seri, $card_value, $mang, $ten, $player);
			break;
			case "Mobifone":
			$mang = 2;
			$ten = "Mobifone";
			$this->plugin->xuLyCard($pin, $seri, $card_value, $mang, $ten, $player);
			break;
			case "Vinaphone":
			$mang = 3;
			$ten = "Vinaphone";
			$this->plugin->xuLyCard($pin, $seri, $card_value, $mang, $ten, $player);
			break;
			case "Zing (Khuyến Khích)":
			$mang = 4;
			$ten = "Zing";
			$this->plugin->xuLyCard($pin, $seri, $card_value, $mang, $ten, $player);
			break;
		}
		return null;
	}
}