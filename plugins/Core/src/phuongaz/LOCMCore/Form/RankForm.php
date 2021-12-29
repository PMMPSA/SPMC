<?php

namespace phuongaz\LOCMCore\Form;


use pocketmine\Player;
use jojoe77777\FormAPI\{CustomForm, ModalForm, SimpleForm};
use phuongaz\Coin\Coin;
use pocketmine\Server;
use phuongaz\LOCMCore\Loader;

Class RankForm{


	private static $ranks = [
		"VIP1" => [
			"DAY" => 3,
			"PRICE" => 10,
			"URL" => "https://img.zing.vn/volamthuphi/images/data/event2015/vip1.png",
			"CONTENT" => "
			§l§fQuyển lợi§e VIP1
			§l§f⇾§e /fly§f Bật tắt chức năng bay
			§l§f⇾§e /feed§f Hồi đầy thức ăn
			"
		],
		"VIP2" => [
			"DAY" => 7,
			"PRICE" => 25,
			"URL" => "https://img.zing.vn/volamthuphi/images/data/event2015/vip2.png",
			"CONTENT" => "
			§l§fQuyển lợi§e VIP1
			§l§f⇾§f Bao gồm tất cả quyền lợi VIP1
			§l§f⇾§e /tp§f dịch chuyển đến người chơi khác
			§l§f⇾§e /repair§f Sửa vật phẩm miễn phí
			§l§f⇾§e /heal§f hồi đầy máu
			"
		],
		"VIP3" => [
			"DAY" => 15,
			"PRICE" => 60,
			"URL" => "https://img.zing.vn/volamthuphi/images/data/event2015/vip3.png",
			"CONTENT" => "
			§l§fQuyển lợi§e VIP3
			§l§f⇾§f Bao gồm tất cả quyền lợi VIP2
			§l§f⇾§e /ec§f nâng cấp giới hạn enchant lên 5
			§l§f⇾§e /sell§f Tính năng tự động bán
			§l§f⇾§e /nick§f Đổi tên
			§l§f⇾§e /size§f thay đổi kích thước 
			Đang cập nhật thêm
			"
		],
		"VIP4" => [
			"DAY" => 30,
			"PRICE" => 130,
			"URL" => "https://img.zing.vn/volamthuphi/images/data/event2015/vip4.png",
			"CONTENT" => "
			§l§fQuyển lợi§e VIP4
			§l§f⇾§f Bao gồm tất cả quyền lợi VIP3
			§l§f⇾§e /time§f điều chỉnh thời gian 
			"
		],
		"VIP5" => [
			"DAY" => 70,
			"PRICE" => 250,
			"URL" => "https://img.zing.vn/volamthuphi/images/data/event2015/vip5.png",
			"CONTENT" => "
			§l§fQuyển lợi§e VIP5
			§l§f⇾§f Bao gồm tất cả quyền lợi VIP4
			"
		],
		"VIP6" => [
			"DAY" => 120,
			"PRICE" => 400,
			"URL" => "https://img.zing.vn/volamthuphi/images/data/event2015/vip6.png",
			"CONTENT" => "
			§l§fQuyển lợi§e VIP6
			§l§f⇾§e Chưa cập nhật
			"
		]
	];

	public function send(Player $player){
		$form = new SimpleForm(function(Player $player, ?int $data){
			if(is_null($data)){
				Server::getInstance()->getCommandMap()->dispatch($player, "lcoin");
				return;
			}
			$this->modalform($player, $data);
		});

		$form->setTitle("§l§dCÁC GÓI ＶＩＰ");
		$form->setContent("§l§e→§f Lcoin bạn đang sở hữu:§e ". Coin::getInstance()->getCoin($player));
		$i = 0;
		foreach(self::$ranks as $rank){
			$form->addButton("§l§f•§0 " .array_keys(self::$ranks)[$i]. " §f•\n§l§7(§e ". $rank["DAY"] . "§f ngày,§e ". $rank["PRICE"]. " §fLcoin§7)", 1, $rank["URL"]);
			$i++;
		}
		$form->sendToPlayer($player);
	}

	public function modalform(Player $player, ?int $data){
		$rank = array_keys(self::$ranks)[$data];
		$rankdata = self::$ranks[$rank];
		$form = new ModalForm(function(Player $player, ?bool $data) use ($rankdata, $rank){
			if(is_null($data)) $this->send($player);
			if($data){
				$coin = Coin::getInstance()->getCoin($player);
				if($coin >= $rankdata["PRICE"]){
					$manager = Loader::getInstance()->getRankManager();
					$check = $manager->checkRank($player, $rank);
					if($check or is_null($check)){
						$manager->addRank($player, $rank, $rankdata["DAY"]);
						Coin::getInstance()->reduceCoin($player, $rankdata["PRICE"]);
						$this->notice($player, "§l§d↦§a Thuê thành công §e".$rank."§a trong vòng§e ".$rankdata["DAY"]."§a ngày với giá §e".$rankdata["PRICE"]." §aLcoin");		
						Server::getInstance()->broadcastMessage("l§d↦§a Người chơi ".$player->getName()." thuê thành công §e".$rank);				
					}elseif(!$check){
						$manager->removeRank($player->getName());
						$manager->addRank($player, $rank, $rankdata["DAY"]);
						Coin::getInstance()->reduceCoin($player, $rankdata["PRICE"]);
						$this->notice($player, "§l§d↦§a Thuê thành công §e".$rank."§a trong vòng§e ".$rankdata["DAY"]."§a ngày với giá §e".$rankdata["PRICE"]." §aLcoin");	
						Server::getInstance()->broadcastMessage("l§d↦§a Người chơi ".$player->getName()." thuê thành công §e".$rank);	
					}
				}else{
					$this->notice($player, "§l§cBạn không đủ Lcoin!");
				}
			}
		});
		$form->setTitle("§l§6".$rank);
		$content = "";
		if(Loader::getInstance()->getRankManager()->checkRank($player, $rank)){
			$content .= "§l§d↦§6Lưu ý:§e Rank hiện tại của bạn trùng với rank bạn đang mua, nếu bạn vẫn đồng ý mua, hệ thống sẽ tự động cộng dồn ngày!\n";
		}elseif(!Loader::getInstance()->getRankManager()->checkRank($player, $rank)){
			$content .= "§l§d↦§6Lưu ý:§e Bạn đang sở hữu một Rank khác, nếu bạn đồng ý mua, rank củ bạn sẽ mất\n";
		}
		$content .= $rankdata["CONTENT"];
		$form->setContent($content);
		$form->setButton1("§l§aTôi sẽ mua");
		$form->setButton2("§l§cKhông mua");
		$form->sendToPlayer($player);
	}

	public function notice(Player $player, string $content = ""){
		$form = new CustomForm(function(Player $player, ?array $data){});
		$form->setTitle("§l§6Ghi Chu");
		$form->addLabel($content);
		$form->sendToPlayer($player);
	}
}