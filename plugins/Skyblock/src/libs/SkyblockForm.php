<?php

namespace libs;

use jojoe77777\FormAPI\{CustomForm, SimpleForm, ModalForm};
use pocketmine\plugin\PluginBase;
use pocketmine\command\{CommandSender, Command, ConsoleCommandSender};
use pocketmine\Player;
use RedCraftPE\RedSkyBlock\SkyBlock;
use pocketmine\utils\TextFormat;
use libs\npc\NPC;
use pocketmine\Server;
use onebone\economyapi\EconomyAPI;
use phuongaz\Coin\Coin;

class SkyblockForm{


	public function islandsForm(Player $player) :void{
		$islands = SkyBlock::getInstance()->getIslands();
		$form = new SimpleForm(function(Player $player, ?int $data)use($islands){
			if(is_null($data)) return;
			$is = $islands[$data];
			$modal = new ModalForm(function(Player $player, ?bool $data) use ($is){
				if ($data) {
					SkyBlock::getInstance()->getServer()->dispatchCommand($player, "is create ". explode(".", $is)[0]);
				} else $this->islandsForm($player);
			});
			$modal->setTitle(explode(".", $is)[0]);
			$modal->setContent("Mô tả: ". SkyBlock::getInstance()->getDesIsland($is));
			$modal->setButton1("Tôi sẽ nhận đảo này");
			$modal->setButton2("Đảo này không vừa ý");
			$modal->sendToPlayer($player);
		});
		$form->setTitle("§l§eＬｏｃｍ§a Ｉｓｌａｎｄ");
		$form->setContent("§e§l→§f bất kì đảo nào để bắt đầu sinh tồn!");
		$islands = SkyBlock::getInstance()->getIslands();
		//$islands = array_keys($islands);
		$sb = SkyBlock::getInstance();
		foreach($islands as $is){
			$form->addButton($sb->getIslandNamea($is),1, $sb->ImageIsland($is));
		}
		$form->sendToPlayer($player);
	}

	public function onForm(Player $player){
		$skyblockArray = SkyBlock::getInstance()->skyblock->get("SkyBlock", []);
		$senderName = strtolower($player->getName());
		if(array_key_exists($senderName, $skyblockArray)){
			$this->startForm($player);
		}else{
			$this->islandsForm($player);
		}
	}

	public function startForm(Player $player) :void
	{
		$form = new SimpleForm(function(Player $player, ?int $data)
			{
				if(is_null($data)) return;
				switch($data)
				{
					case 0:
						$this->execCommand($player, "is create");
						break;
					case 1:
						$this->ManagerForm($player);
						break;
					case 2:
						$this->TopForm($player);
						break;
					case 3:
						$this->WarpForm($player);
						break;
				}
			});
		$form->setTitle("§l§eSKYBLOCK");
		$form->setContent("§l§e↦§f  Island Top:§e ". SkyBlock::getInstance()->calcRank(strtolower($player->getName())));
		$form->addButton("§l§f• §l§0JOIN§l§f•", 1, "https://www.pinclipart.com/picdir/big/15-153816_gone-cliparts-let-go-icon-png-transparent-png.png");
		$form->addButton("§l§f• §l§0Manager Island §l§f•", 1, "https://icons-for-free.com/iconfiles/png/512/gear+options+part+preferences+seo+setting+icon-1320162087593540600.png");
		$form->addButton("§l§f• §l§0TOP ISLAND §l§f•", 1, "https://icon-library.com/images/top-5-icon/top-5-icon-12.jpg");
		$form->addButton("§l§f• §l§0WARP ISLAND §l§f•", 1, "https://image.flaticon.com/icons/png/512/1636/1636923.png");
		$form->sendToPlayer($player);
	}

	public function ManagerForm(Player $player) :void 
	{
		$form = new SimpleForm(function(Player $player, ?int $data)
			{
				if(is_null($data)) $this->startForm($player);
				switch($data)
				{
					case 0:
						$this->InfoForm($player);
						break;
					case 1:
						$this->sizeForm($player);
						break;
					case 2:
						$this->lockForm($player);
						break;
					case 3:
						$this->banForm($player);
						break;
					case 4:
						$this->helperForm($player);
						break;
					case 5:
						$this->kickForm($player);
						break;
				}
			});
		$form->setTitle("§l§6Manager Island");
		$form->addButton("§l§f• §0Info Island §l§f•", 1, "https://cdn3.iconfinder.com/data/icons/bold-blue-glyphs-free-samples/32/Info_Circle_Symbol_Information_Letter-512.png");
		$form->addButton("§l§f• §0Size Island §l§f•", 1, "https://cdn.onlinewebfonts.com/svg/img_549062.png");
		$form->addButton("§l§f• §0Lock/Unlock §l§f•", 1, "https://image.flaticon.com/icons/png/512/891/891399.png");
		$form->addButton("§l§f• §0Ban/UnBan §l§f•", 1, "https://upload-icon.s3.us-east-2.amazonaws.com/uploads/icons/png/3248817221557740369-512.png");
		$form->addButton("§l§f• §0AddHelper/RemoveHelper §l§f•", 1, "https://image.flaticon.com/icons/png/512/891/891399.png");
		$form->addButton("§l§f• §0Kick §l§f•", 1, "https://i.dlpng.com/static/png/6346066_preview.png");
		$form->sendToPlayer($player);
	}

	public function sizeForm(Player $player) :void {
		$form = new SimpleForm(function(Player $player, ?int $data){
			if(is_null($data)) return;
			if($data == 0){
				if(EconomyAPI::getInstance()->myMoney($player) >= 500000){
					Server::getInstance()->getCommandMap()->dispatch(new ConsoleCommandSender(), "is increase ".$player->getName()." 2");
					EconomyAPI::getInstance()->reduceMoney($player, 500000);
				}else{
					$player->sendMessage("§l§f• Không đủ tiền để nâng giới hạn đảo");
				}

			}
			if($data == 1){
				if(Coin::getInstance()->getCoin($player) >= 50){
					Server::getInstance()->getCommandMap()->dispatch(new ConsoleCommandSender(), "is increase ".$player->getName()." 5");
					Coin::getInstance()->reduceCoin($player, 50);
				}else{
					$player->sendMessage("§l§f• Không đủ tiền để nâng giới hạn đảo");
				}
			}
		});
		$form->setTitle("SIZE ISLAND");
		$form->addButton("§l§f• §0XU §f• \n §7(§e2 §eBLOCK§e 500000§f XU§7)");
		$form->addButton("§l§f• §0LCOIN §f•\n §7(§e3§f BLOCK§e 30§f LCOIN§7)");
		$form->sendToPlayer($player);
	}

	public function TopForm(Player $player) :void 
	{
		$top = $this->getSkyblockTop();
		$form = new CustomForm(function(Player $player, ?array $data){});
		$form->setTitle("§l§6TOP ISLAND");
		$form->addLabel($top);
		$form->sendToPlayer($player);
	}

	public function WarpForm(Player $player) :void {
		$form = new CustomForm(function(Player $player, ?array $data)
			{
				if(is_null($data)) $this->startForm($player);
				$this->execCommand($player, "is teleport ".$data[0]);
			});
		$form->setTitle("§l§6WARP ISLAND");
		$form->addInput("§l§e☛§f §Warp:", "name player");
		$form->sendToPlayer($player);
	}

	public function infoForm(Player $player) :void {
		$sb = SkyBlock::getInstance();
		$form = new CustomForm(function(Player $player, $data)
		{
			if(is_null($data)) $this->startForm($player);
		});
		$form->setTitle("§l§6INFO ISLAND");
		$form->addLabel("§l§e⇥ §fName:§e ". $sb->getIslandName($player));
		$form->addLabel("§l§e⇥ §fMembers:§e ". $sb->getMembers($player));
		$form->addLabel("§l§e⇥ §fLocked:§e ". $sb->getLockedStatus($player));
		$form->addLabel("§l§e⇥ §fSize:§e ". $sb->getSize($player));
		$form->addLabel("§l§e⇥§f Rank:§e ". $sb->calcRank(strtolower($player->getName())));
		$form->sendToPlayer($player);
	}

	public function lockForm(Player $player) :void {
		$status = Skyblock::getInstance()->getLockedStatus($player);
		$form = new CustomForm(function(Player $player, ?array $data) use ($status)
		{
			if(is_null($data)){
				$this->startForm($player);
			}else $this->startForm($player);

			$statusa = $data[1];

			if($statusa == false and $status == "Yes"){
				$this->execCommand($player, "is lock");
			}elseif($statusa == true and $status == "No"){
				$this->execCommand($player, "is lock");
			}
		});
		$form->setTitle("§l§6LOCK FORM");
		$form->addLabel("");
		if($status == "Yes"){
			$form->addToggle("§l§f• §aUnlock§f/§aLock §l§f•",true);
		}else {
			$form->addToggle("§l§f• §aUnlock§f/§aLock §l§f•", false);
		}
		$form->sendToPlayer($player);
	}

	public function banForm(Player $player) :void
	{
		$form = new CustomForm(function(PLayer $player, ?array $data)
		{
			if(is_null($data)) $this->startForm($player);
			if(!isset($data[2])) $this->startForm($player);
			if($data[1] == true)	
			{
				$this->execCommand($player, "is ban ". $data[2]);
			}
			else 
			{
				$this->execCommand($player, "is unban ". $data[2]);
			}
		});
		$form->addLabel("§1Baned:§f ". Skyblock::getInstance()->getBanned($player));
		$form->addToggle("§l§f• §a§lUNBAN§f/§aBAN");
		$form->addInput("§l§f• §l§aPlayer Name");
		$form->sendToPlayer($player);
	}

	public function kickForm(Player $player ) :void 
	{
		$form = new CustomForm(function(PLayer $player, ?array $data)
			{
				if(is_null($data)) $this->startForm($player);
				if(empty($data[0])) return;
				$this->execCommand($player, "is kick ". $data[0]);
			});
		$form->setTitle("§l§6KICK");
		$form->addInput("§l• §1Kick", "player name");
		$form->sendToPlayer($player);
	}

	public function helperForm(Player $player) :void 
	{
		$form = new CustomForm(function(Player $player, ?array $data)
			{
				if(is_null($data)) $this->startForm($player);
				if($data[1] == null) return true;
				if($data[0] == true){
					$this->execCommand($player, "is remove ". $data[1]);
				}else {
					$this->execCommand($player, "is add ". $data[1]);
				}
			});
		$form->setTitle("§l§6HELPER FORM");
		$form->addToggle("§l§f• §aAdd§f/§aRemove Helper §l§f•");
		$form->addInput("§l§f• §1Player Name §l§f•");
		$form->sendToPlayer($player);

	}

	public function execCommand(Player $player, string $command) :void 
	{
		Server::getInstance()->getCommandMap()->dispatch($player, $command);
	}

	public function getSkyblockTop() {
      $skyblockArray = SkyBlock::getInstance()->skyblock->get("SkyBlock", []);
      $first = "N/A";
      $firstValue = 0;
      $second = "N/A";
      $secondValue = 0;
      $third = "N/A";
      $thirdValue = 0;
      $fourth = "N/A";
      $fourthValue = 0;
      $fifth = "N/A";
      $fifthValue = 0;
      $values = [];
      $copyOfArray = $skyblockArray;

      foreach(array_keys($skyblockArray) as $user) {

        $value = $skyblockArray[$user]["Value"];
        $values[] = $value;
        rsort($values);
      }

      $counter = count($skyblockArray);
      if ($counter > 5) {

        $counter = 5;
      }

      for ($i = 1; $i <= count($skyblockArray); $i++) {

        $value = $values[$i - 1];

        $NameIndex = array_search($value, array_column($copyOfArray, "Value"));
        $keys = array_keys($copyOfArray);
        $NameValue = $copyOfArray[$keys[$NameIndex]];
        $Name = array_search($NameValue, $copyOfArray);

        if ($i === 1) {$first = $NameValue["Members"][0]; $firstValue = $value;}
        if ($i === 2) {$second = $NameValue["Members"][0]; $secondValue = $value;}
        if ($i === 3) {$third = $NameValue["Members"][0]; $thirdValue = $value;}
        if ($i === 4) {$fourth = $NameValue["Members"][0]; $fourthValue = $value;}
        if ($i === 5) {$fifth = $NameValue["Members"][0]; $fifthValue = $value;}

        unset($copyOfArray[$Name]);
      }

      $mess = TextFormat::WHITE . "§l§aＴＯＰ§e 1: " . TextFormat::GRAY . "§e【§6 {$first} §e】  " . TextFormat::WHITE . "Worth: " . TextFormat::GRAY . "{$firstValue} \n" . TextFormat::WHITE . "§l§aＴＯＰ§e 2: " . TextFormat::GRAY . "§e【§6 {$second} §e】   " . TextFormat::WHITE . "Worth: " . TextFormat::GRAY . "{$secondValue} \n" . TextFormat::WHITE . "§l§aＴＯＰ§e 3: " . TextFormat::GRAY . "§e【§6 {$third} §e】 " . TextFormat::WHITE . "Worth: " . TextFormat::GRAY . "{$thirdValue} \n" . TextFormat::WHITE . "§l§aＴＯＰ§e 4: " . TextFormat::GRAY . "§e【§6 {$fourth} §e】 " . TextFormat::WHITE . "Worth: " . TextFormat::GRAY . "{$fourthValue} \n" . TextFormat::WHITE . "§l§aＴＯＰ§e 5: " . TextFormat::GRAY . "§e【§6 {$fifth} §e】  " . TextFormat::WHITE . "Worth: " . TextFormat::GRAY . "{$fifthValue}";
      return $mess;
    }
}