<?php 

namespace phuongaz\Heaven\Commands;

use pocketmine\command\{
	Command,
	CommandSender
};

use phuongaz\Heaven\GemsMain;

use pocketmine\{Server, Player};

Class GemsCommands extends Command
{

	private $plugin;

	public function __construct(GemsMain $plugin){
		$this->plugin = $plugin;
		parent::__construct('gems', 'Gems command', '/gems');
	}

	public function execute(CommandSender $sender, string $label, array $args) :bool {
		if(!$sender->isOp()) return false;
		if(!isset($args[0])){
			$this->plugin->sendGemsForm($sender);
			return true;
		} 
		if(is_numeric($args[0])){
			if(($player = Server::getInstance()->getPlayer($args[1])) !== null){
				$gem = $this->plugin->getGems($args[0]);
				$player->sendMessage("§l§e•§a Bạn vừa nhận được §e1§a ngọc khảm");
				$player->getInventory()->addItem($gem);					
			}				
		}
		if($args[0] == "chance"){
			$chance = $args[1];
			if(($player = Server::getInstance()->getPlayer($args[3])) !== null){
				$gems = [explode("-", $args[2])[0], explode("-", $args[2])[1]];
				if($this->chance($chance)){
					$t = (int)$gems[0];
					$f = (int)$gems[1];
					$id = mt_rand($t, $f);
					$gem = $this->plugin->getGems($id);
					$player->sendMessage("§l§e•§a Bạn vừa nhận được §e1§a ngọc khảm");
					$player->getInventory()->addItem($gem);
					return true;	
				}
				$player->sendMessage("§l§e•§c Chúc bạn may mắn lần sau");
			}
		}
		return true;
	}

	public function chance(int $succes):bool{
		$percentage = mt_rand(0, 10000);
		return ($succes >= 1 and $succes < $succes*100) ? true : false;
	}
}