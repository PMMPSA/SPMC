<?php

/*
*   @author: phuongaz
*   @api: 3.0.0
*/

namespace phuongaz\sell;

use pocketmine\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\utils\Config;
use pocketmine\utils\TextFormat as TF;

use onebone\economyapi\EconomyAPI;
use jojoe77777\FormAPI\
{
	SimpleForm,
	CustomForm
};
use pocketmine\event\{Listener, block\BlockBreakEvent};
use pocketmine\Server;
class Main extends PluginBase implements Listener{

	public $can;

	public function onEnable(){
		$file = 'sell.yml';

			if(!file_exists($this->getDataFolder() . $file)) {
				@mkdir($this->getDataFolder());
				file_put_contents($this->getDataFolder() . $file, $this->getResource($file));
			}
			$this->sell = new Config($this->getDataFolder() . "sell.yml", Config::YAML);
				
		$this->getServer()->getPluginManager()->registerEvents($this, $this);

	}


	public function onCommand(CommandSender $sender, Command $cmd, string $label, array $args): bool{
		if(strtolower($cmd->getName()) === 'sell'){
			if($sender instanceof Player){
				/*  @var SimpleForm   */
				$form = new SimpleForm(function(Player $player, $data){
					if(is_null($data)) return;
					switch ($data) {
						case 0:
							$this->sellHand($player);
							break;
						case 1:
						    $this->sellAll($player);
						    break;
						case 2:
						Server::getInstance()->getCommandMap()->dispatch($player, "autosell");
						    break;
						default:
							# Nothing todo....
							break;
					}
				});
				$form->setTitle("§l§6SELL");
				$form->setContent("§l§e→ §fXu:§e ".EconomyAPI::getInstance()->myMoney($sender));
				$form->addButton("§l§f•§0 Bán vật phẩm trên tay§f •");
				$form->addButton("§l§f• §0Bán tất cả vật phẩm§f •");
				if($sender->hasPermission('autosell')){
					if($this->checkAutoSell($sender)){
						$status = "§aON";
					}else $status = "§cOFF"; 
					$form->addButton("§l§f• §0Tự động bán§l§f• \n ".$status);
				}
				$form->sendToPlayer($sender);
			}
		}elseif($cmd->getName() == "autosell"){
				if(!$sender->hasPermission('autosell')){
					return true;
				}
				if($this->checkAutoSell($sender)){
					unset($this->can[$sender->getName()]);
					$sender->sendMessage("§l§cTurn off auto sell");
				}else{
					$this->can[$sender->getName()] = true;
				}
				$sender->sendMessage("§l§aTurn on auto sell");
			}
		return true;
	}

	public function checkAutoSell($player){
		if(isset($this->can[$player->getName()])){
			return true;
		}
		return false;
	}

	public function sellAll($player)
	{
	    $allmoney = 0;	
	    /*  @var CustomForm   */
		$form = new CustomForm(function(Player $player, $data){
			if(is_null($data)) return;
		});
		$form->setTitle("§l§6SELL ALL");
		$form->addLabel("§l§e⇥ §fCác vật phẩm đã bán:");
		$form->addLabel("§l§a✓ §eVật phẩm§d |§a Số Lượng§d |§a Số tiền nhận §d|");
		$items = $player->getInventory()->getContents();
		foreach($items as $item){
			if($item->getNamedTagEntry("Gem") !== null) continue;
			if($this->sell->get($item->getId()) !== null && $this->sell->get($item->getId()) > 0){
				$price = $this->sell->get($item->getId()) * $item->getCount();
				EconomyAPI::getInstance()->addMoney($player, $price);
				$money = $this->sell->get($item->getId());
				$count = $item->getCount();
				$iname = $item->getName();
				$form->addLabel("§l§a✓§e $iname §f|§e $count §f|§e $price §f|");
				$allmoney = $allmoney + $price;
				$player->getInventory()->remove($item);
			}
		}
		$form->addLabel("§l§e⇥ §fTổng số tiền nhận được:§e ". $allmoney);
		$form->sendToPlayer($player);
	}

	public function sellHand($player)
	{
		$item = $player->getInventory()->getItemInHand();
		$itemId = $item->getId();
		if($item->getId() === 0){
			$player->sendMessage("§l§e⇥ §f Trên tay bạn hiện không có gì cả");
            return false;
		}
		if($this->sell->get($itemId) == null){
			$player->sendMessage("§l§e⇥§f Bạn không thể bán vật phẩm này");
			return false;
		}
		EconomyAPI::getInstance()->addMoney($player, $this->sell->get($itemId) * $item->getCount());
		$player->getInventory()->removeItem($item);
		$price = $this->sell->get($item->getId()) * $item->getCount();
		/*  @var CustomForm   */
		$form = new CustomForm(function(Player $player, $data){
			if(is_null($data)) return;
		});
		$iname = $item->getName();
		$count = $item->getCount();
		$form->setTitle("§l§6SELL HAND");
		$form->addLabel("§l§e⇥ §fCác vật phẩm đã bán:");
		$form->addLabel("§l§a✓ §eVật phẩm§d |§a Số Lượng§d |§a Số tiền nhận §d|");
		$form->addLabel("§l§a✓§e $iname §f|§e $count §f|§e $price §f|");
		$form->sendToPlayer($player);		
	}

	public function AutoSell($player) :bool
	{
		if(isset($this->can[$player->getName()])){
			return true;
		}
		return false;
	}

	public function onBreak(BlockBreakEvent $event) {
		
		$player = $event->getPlayer();
		if($this->AutoSell($player) == false) return true;
		foreach($event->getDrops() as $drop) {
			if(!$player->getInventory()->canAddItem($drop)) {
				//$event->getPlayer()->addTitle("§l§a✠§6 FULL INVENTORY §a✠", "§l§cTự động bán!");
                $this->sellAll($player);

            }
				break; 
			}
	}


}
