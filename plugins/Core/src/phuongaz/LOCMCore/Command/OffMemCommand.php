<?php

namespace phuongaz\LOCMCore\Command;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use phuongaz\LOCMCore\Loader;
use pocketmine\Player;
use pocketmine\Server;
Class offMemCommand extends Command{

	public function __construct(){
		parent::__construct("offmem", "OFF MEM");
	}

	public function execute(CommandSender $sender, string $label, array $args):bool{
		if($sender->isOp()){
			$status = $this->getStatus();
			Loader::$isoff = $status;
			$sender->sendMessage("OFF MEM SYSTEM: ". $this->parseString($status));
			foreach(Server::getInstance()->getOnlinePlayers() as $player){
				if($player->isOp()) continue;
				$player->kick("§l§eMÁY CHỦ HIỆN ĐANG BẢO TRÌ\n§l§fVUI LÒNG QUAY LẠI SAU", false);
			}
		}
		return true;
	}

	public function getStatus() :bool{
		return (Loader::$isoff) ? false : true;
	}

	public function parseString(bool $bool) :string{
		return ($bool) ? "ON" : "OFF";
	}
}