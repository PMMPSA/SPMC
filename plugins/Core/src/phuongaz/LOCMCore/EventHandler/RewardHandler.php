<?php

namespace phuongaz\LOCMCore\EventHandler;

use pocketmine\{Server, Player};
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\Listener;

use phuongaz\LOCMCore\Loader;

use pocketmine\command\ConsoleCommandSender;

Class RewardHandler implements Listener{

	private $players, $reward;

	public function __construct(){
		$this->players = yaml_parse_file(Loader::getInstance()->getDataFolder(). "reward/players.yml");
		$this->reward = yaml_parse_file(Loader::getInstance()->getDataFolder(). "reward/reward.yml");
	}

	public function onJoin(PlayerJoinEvent $event) :void{
		$player = $event->getPlayer();
		if(in_array($player->getName(), $this->players["players"])){
			if($this->reward["day"] != date("d/m/Y")){
				$this->reloadConfig();
				$this->reward($player);
			}
		}else $this->reward($player);
	}

	public function reloadConfig() : void {
		$this->reward["day"] = date("d/m/Y");
		yaml_emit_file($this->getDataFolder(). "reward/reward.yml", $this->reward);
	}

	public function reward(Player $player) :void{
		$player->addTitle("Chúc Mừng", "Bạn vừa nhận được quà nhờ vào việc đăng nhập");
		$index = array_rand($this->reward["cmds"]);
		$cmd = $this->reward["cmd"][$index];
		$cmd = str_replace("{player}", $player->getName(), $cmd);
		Server::getInstance()->getCommandMap()->dispatch(new ConsoleCommandSender(), $cmd);
		$this->players["players"] = $player->getName();
	}
}