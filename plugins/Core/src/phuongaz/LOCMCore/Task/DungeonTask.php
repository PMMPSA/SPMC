<?php

namespace phuongaz\LOCMCore\Task;

use pocketmine\Player;
use pocketmine\command\ConsoleCommandSender;
use pocketmine\Server;
use phuongaz\LOCMCore\Loader;
use phuongaz\LOCMCore\Manager\DungeonManager;
use pocketmine\scheduler\Task;

Class DungeonTask extends Task{

	/** @var bool */
	public static $open = false;

	public function onRun(int $currentTick):void{
		if(date("H") % 2 == 0){
			Server::getInstance()->getCommandMap()->dispatch(new ConsoleCommandSender(), "boss clear-all");
			Server::getInstance()->getCommandMap()->dispatch(new ConsoleCommandSender(), "boss spawn-all");
		}
	}

	/**
	* @param string $msg
	* @return void
	*/
	private function broadcast(string $msg = ""): void{
		foreach(Server::getInstance()->getOnlinePlayers() as $player){
			$player->sendMessage($msg);
		}
	}
}