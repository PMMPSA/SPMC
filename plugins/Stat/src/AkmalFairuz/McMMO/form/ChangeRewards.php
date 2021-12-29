<?php

namespace AkmalFairuz\McMMO\form;

use pocketmine\Player;
use pocketmine\Server;
use jojoe77777\FormAPI\SimpleForm;
use jojoe77777\FormAPI\CustomForm;
use AkmalFairuz\McMMO\Main;
use pocketmine\command\ConsoleCommandSender;
use pocketmine\item\Item;
Class ChangeRewards{

	/** @var Player */
	private $player;

	public function __construct(Player $player){
		$this->player = $player;
	}

	public function init() :void {

		$form = new SimpleForm(function(Player $player, ?int $data){
			if(is_null($data)){
		        $form = new McmmoForm(Main::getInstance());
		        $form->init($player);
		        return;
			}
			$group = $data+1;
			$this->formRW($group);
		});
		$form->setTitle("§l§6ĐỎI QUÀ");
		foreach(Main::getInstance()->getReward() as $key){
			$form->addButton($key["name"]);
		}
		$form->sendToPlayer($this->player);
	}

	public function formRW(?int $type) :void{
		$rewards = $this->getReward("group_".$type);
		$form = new SimpleForm(function(Player $player, ?int $data) use ($rewards){
			if(is_null($data)){
				$this->init();
				return;
			}
			$reward = $rewards["rewards"][$data];
			$this->confirm($reward, $rewards["types"]);
		});
		$form->setTitle("§l§6ĐỎI QUÀ");
		foreach($rewards["rewards"] as $reward){
			$name = explode(":", $reward)[1];
			$price = explode(":", $reward)[3];
			$form->addButton("§l§f•§0 ".$name. " §f•\n". "§7(§1Cần§0 ".$price."§1 điểm§7)");
		}
		$form->sendToPlayer($this->player);
	}

	public function confirm(string $reward, array $types, $k = false) :void{
		$ex = explode(":", $reward);
		$form = new CustomForm(function(Player $player, ?array $data) use ($reward, $ex, $types, $k){
			if(is_null($data)){
				$this->init();
				return;
			}
			$type = $data[3];
			if($k) $type = $data[4];
			if($this->checKLevel($types[$type], (int)$ex[3])){
				$player->sendMessage("§l§aĐỏi quà thành công");
				if($ex[0] == "cmd"){
					Server::getInstance()->getCommandMap()->dispatch(new ConsoleCommandSender(), str_replace("{player}", $player->getName(), $ex[2]));
				}elseif($ex[0] == "item"){
					$explode = explode(",", $ex[2]);
					$item = Item::get($explode[0], $explode[1], $explode[2]);
					if($player->getInventory()->canAddItem($item)){
						$player->getInventory()->addItem($item);
					}else{
						$player->getLevel()->dropItem($player->asVector3(), $item);
						$player->sendMessage("§l§cVì túi của bạn không còn chỗ trống, vật phẩm đã rơi ra ngoài");
					}
				}
			}else{
				$this->confirm($reward, $types, true);
			}
		});
		$form->setTitle("§l§6ĐỎI QUÀ");
		if($k) $form->addLabel("§l§cPhương thức thanh toán vừa chọn không đủ");
		$form->addLabel("§l§c→ §fQuà:§e ". $ex[1]);
		$form->addLabel("§l§c→ §fSố điểm cần để đỏi:§e ". $ex[3]);
		$form->addLabel("§l§c→ §fSau khi đỏi thành công\n điểm của bạn sẽ bị trừ đi§e ". $ex[3] ." §f!");
		$arr = [];
		$main = Main::getInstance();
		foreach($types as $type){
			$arr[] = "§l".$main->translate($type). " Lｖ.".$main->getLevel(Main::toInterger($type), $this->player);
		}
		$form->addDropDown("§l§c→ §fChọn loại điểm muốn thanh toán", $arr);
		$form->sendToPlayer($this->player);
	}

	public function checkLevel(string $type, int $price) :bool{
		if(Main::getInstance()->getLevel(Main::toInterger($type), $this->player) >= $price){
			Main::getInstance()->reduceLevel(Main::toInterger($type), $price, $this->player);
			return true;
		}
		return false;
	}

	public function getReward(string $group) :array{
		$rewards = Main::getInstance()->getReward();
		return $rewards[$group];
	}
}