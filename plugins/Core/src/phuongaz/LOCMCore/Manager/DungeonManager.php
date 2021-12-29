<?php

namespace phuongaz\LOCMCore\Manager;

use phuongaz\LOCMCore\Loader;
use pocketmine\Player;
use pocketmine\Server;
Class DungeonManager{

	private static $rounds =
	[
		1 => "test"
	];

	private static $currnet_round;

	public static $players = [];

	public function teleport(Player $player, ?int $round = 1){
		$map = self::$rounds[$round];
		$level = Server::getInstance()->getLevelByName($map);
		if(!is_null($level)){
			$pos = $level->getSafeSpawn();
			$player->teleport($pos);
			$player->sendMessage("Dungeon: Bạn đang ở vòng ".$round);
			self::$players[$round] = $player->getLowerCaseName();
		}
	}

	public function quit(Player $player){
		$name = $player->getLowerCaseName();
		if($this->isInDungeoconvertSizen($name)){

			$spawn = Loader::getInstance()->getDefaultLevel()->getSafeSpawn();
			$player->teleport($spawn);
		}	
		unset(self::$players[$name]);
	}

	public function isFinishRound(int $round){
		$nextround = $round++;
		if(isset(self::$rounds[$nextround])){
			return false;
		}
		return true;
	}

	public function nextRound(int $currnet_round){
		if(!$this->isFinishRound($currnet_round)){
			foreach(self::$players as $lowername => $round){
				if(($player = Server::getInstance()->getPlayer($lowername)) !== null){
					$this->teleport($player, $currnet_round++);
				}else{
					unset(self::$players[$lowername]);
				}
			}
		}
	}

	public function isInDungeon(string $name){
		if(isset(self::$players[$name])){
			return true;
		}
		return false;
	}

	public function isDungeon(string $name){
		$bool = false;
		foreach(array_keys(self::$rounds) as $round => $map){
			if($map == $name){
				$bool = true;
			}
		}
		return $bool;
	}
}