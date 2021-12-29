<?php

namespace phuongaz\LOCMCore\form;

use pocketmine\Player;
use pocketmine\Server;
use jojoe77777\FormAPI\{SimpleForm, ModalForm};
use phuongaz\LOCMCore\Loader;
use pocketmine\item\Item;
use onebone\economyapi\EconomyAPI;

Class FunctionForm{

	/** @var Player */
	private $player;

	/** @var array */
	private static $form_data;

	public function __construct(Player $player) {
		$this->player = $player;
		self::$form_data = Loader::getInstance()->getFunctionData();
	}

	public function send() :void{
		$form = $this->praseForm();
		$form->sendToPlayer($this->player);
	}

	public function praseForm() :SimpleForm{
		$form = new SimpleForm(function(Player $player, ?int $data){
			if(is_null($data)) return;
			$form_data = self::$form_data;
			if(isset($form_data[$data])){
				if(isset($form_data[$data]["xu"])){
					$this->BuyItem($data);
					return;
				}
				Server::getInstance()->getCommandMap()->dispatch($player, $form_data[$data]["cmd"]);
			}
		});
		$form->setTitle("§l§6SPMC FUNCTIONS");
		foreach(self::$form_data as $data){
			$form->addButton("§l§f• §0". $data["name"] ." §f•", 1, $data["url"]);
		}
		return $form;
	}

	public function BuyItem(int $data) :void{
		$form_data = self::$form_data;
		$form = new ModalForm(function(Player $player, ?bool $bool) use ($data, $form_data){
			if(is_null($bool)) $this->send();
			if($bool){
				$ex_id = explode(":", $form_data[$data]["id"]);
				$item = Item::get($ex_id[0], $ex_id[1], $ex_id[2]);
				$item->setCustomName($form_data[$data]["name"]);
				if(EconomyAPI::getInstance()->myMoney($player) >= (int)$form_data[$data]["xu"]){
					EconomyAPI::getInstance()->reduceMoney($player, (int)$form_data[$data]["xu"]);
					$player->getInventory()->addItem($item);
					$player->sendMessage("Mua thành công vật phẩm ".$form_data[$data]["name"]);
				}
			}
		});
		$form->setTitle("§l§6BUY ITEM");
		$form->setContent("§l§fBạn có chắc muốn mua vật phẩm §e".$form_data[$data]["name"]. " §fvới giá§e ".$form_data[$data]["xu"]. " §fXu");
		$form->setButton1("§l§eCó tôi sẽ mua");
		$form->setButton2("§l§eTôi không mua");
		$form->sendToPlayer($this->player);
	}
}